<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $stats = [
            'posts' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'drafts' => Post::where('status', 'draft')->count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'totalViews' => Post::sum('views') ?? 0,
        ];
        $recentPosts = Post::orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPosts'));
    }

    public function posts()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.posts', compact('posts', 'categories'));
    }

    public function createPost()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.post-editor', [
            'post' => null,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'category' => 'required|string',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'coverImage' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['title']);
        $status = $validated['status'];

        Post::create([
            'title' => e($validated['title']),
            'slug' => $slug,
            'content' => $validated['content'],
            'excerpt' => e($validated['excerpt']),
            'coverImage' => $validated['coverImage'] ?? '',
            'category' => $validated['category'],
            'tags' => $validated['tags'] ?? [],
            'status' => $status,
            'publishedAt' => $status === 'published' ? now() : null,
            'author' => Auth::user()->email,
            'readTime' => Post::calculateReadTime($validated['content']),
            'views' => 0,
        ]);

        return redirect()->route('admin.posts')->with('success', 'Post created successfully.');
    }

    public function editPost(string $id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.post-editor', compact('post', 'categories', 'tags'));
    }

    public function updatePost(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'category' => 'required|string',
            'tags' => 'nullable|array',
            'status' => 'required|in:draft,published',
            'coverImage' => 'nullable|string',
        ]);

        $post->update([
            'title' => e($validated['title']),
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'excerpt' => e($validated['excerpt']),
            'coverImage' => $validated['coverImage'] ?? '',
            'category' => $validated['category'],
            'tags' => $validated['tags'] ?? [],
            'status' => $validated['status'],
            'publishedAt' => $validated['status'] === 'published' && !$post->publishedAt ? now() : $post->publishedAt,
            'readTime' => Post::calculateReadTime($validated['content']),
        ]);

        return redirect()->route('admin.posts')->with('success', 'Post updated successfully.');
    }

    public function deletePost(string $id)
    {
        Post::findOrFail($id)->delete();
        return redirect()->route('admin.posts')->with('success', 'Post deleted.');
    }

    public function categories()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string|max:500']);
        Category::create(['name' => e($validated['name']), 'slug' => Str::slug($validated['name']), 'description' => e($validated['description'] ?? '')]);
        return redirect()->route('admin.categories')->with('success', 'Category created.');
    }

    public function deleteCategory(string $id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted.');
    }

    public function tags()
    {
        $tags = Tag::orderBy('name')->get();
        return view('admin.tags', compact('tags'));
    }

    public function storeTag(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:50']);
        Tag::create(['name' => e($validated['name']), 'slug' => Str::slug($validated['name'])]);
        return redirect()->route('admin.tags')->with('success', 'Tag created.');
    }

    public function deleteTag(string $id)
    {
        Tag::findOrFail($id)->delete();
        return redirect()->route('admin.tags')->with('success', 'Tag deleted.');
    }

    public function media()
    {
        $media = Media::orderBy('created_at', 'desc')->limit(100)->get();
        return view('admin.media', compact('media'));
    }

    public function uploadMedia(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120']);
        $file = $request->file('file');
        $filename = Str::random(32) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $filename);
        Media::create([
            'filename' => $filename,
            'originalName' => $file->getClientOriginalName(),
            'url' => "/uploads/{$filename}",
            'mimeType' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
        return redirect()->route('admin.media')->with('success', 'File uploaded.');
    }
}
