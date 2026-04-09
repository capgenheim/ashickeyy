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
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use MongoDB\Client as MongoClient;

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
        ];
        $recentPosts = Post::orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPosts'));
    }

    public function posts()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('admin.posts', compact('posts'));
    }

    public function createPost()
    {
        $tags = Tag::orderBy('name')->get();
        return view('admin.post-editor', [
            'post' => null,
            'tags' => $tags,
        ]);
    }

    public function storePost(Request $request)
    {
        // Decode Tagify JSON if present
        if (is_string($request->input('tags'))) {
            $tagsData = json_decode($request->input('tags'), true);
            $parsedTags = is_array($tagsData) ? array_column($tagsData, 'value') : [];
            $request->merge(['tags' => $parsedTags]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:500',
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
            'tags' => $this->processDynamicTags($validated['tags'] ?? []),
            'status' => $status,
            'publishedAt' => $status === 'published' ? now() : null,
            'author' => Auth::user()->email,
            'readTime' => Post::calculateReadTime($validated['content']),
            'views' => 0,
        ]);

        if ($status === 'published') {
            $this->dispatchPushNotification($validated['title'], $slug);
            $this->dispatchEmailNotification($validated['title'], $slug, $validated['excerpt'], $validated['coverImage'] ?? '');
        }

        return redirect()->route('admin.posts')->with('success', 'Post created successfully.');
    }

    private function dispatchEmailNotification(string $title, string $slug, string $excerpt, string $coverImage)
    {
        try {
            \App\Jobs\BlastEmailJob::dispatch($title, $slug, $excerpt, $coverImage);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Redis BlastEmailJob Error: ' . $e->getMessage());
        }
    }

    private function dispatchPushNotification(string $title, string $slug)
    {
        try {
            $mongo = new MongoClient(env('DB_DSN', 'mongodb://ashickey-mongo:27017'));
            $collection = $mongo->ashickey->push_subscriptions;
            $subs = $collection->find([]);

            $auth = [
                'VAPID' => [
                    'subject' => 'mailto:admin@ashickey.space',
                    'publicKey' => 'BJgc7yMNMJQXLuw7LulYHUF7KQqzor8-lmnjInJsf_7N5MmgS8hpVCC5gUAZ2n9kgIwnGJV4Ex937XUzf9IrHxg',
                    'privateKey' => 'FkjxpSgx1Z2qhyl6dNdxR56mcHaGOmAhfLPHTJHr3ak',
                ],
            ];

            $webPush = new WebPush($auth);
            $payload = json_encode([
                'title' => 'New Post: ' . $title,
                'body' => 'Check out the latest insights on ashickey{}',
                'url' => 'https://ashickey.space/post/' . $slug,
            ]);

            foreach ($subs as $sub) {
                $subscription = Subscription::create([
                    'endpoint' => $sub['endpoint'],
                    'publicKey' => $sub['keys']['p256dh'],
                    'authToken' => $sub['keys']['auth'],
                ]);
                $webPush->queueNotification($subscription, $payload);
            }

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    if ($report->isSubscriptionExpired()) {
                        $collection->deleteOne(['endpoint' => $report->getRequest()->getUri()->__toString()]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Push Blast Failed: ' . $e->getMessage());
        }
    }

    public function editPost(string $id)
    {
        $post = Post::findOrFail($id);
        $tags = Tag::orderBy('name')->get();
        return view('admin.post-editor', compact('post', 'tags'));
    }

    public function updatePost(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        // Decode Tagify JSON if present
        if (is_string($request->input('tags'))) {
            $tagsData = json_decode($request->input('tags'), true);
            $parsedTags = is_array($tagsData) ? array_column($tagsData, 'value') : [];
            $request->merge(['tags' => $parsedTags]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:500',
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
            'tags' => $this->processDynamicTags($validated['tags'] ?? []),
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
        return response()->json(['url' => "/uploads/{$filename}"]); // Updated for Markdown editor upload image functionality
    }

    private function processDynamicTags(array $tags): array
    {
        $processed = [];
        foreach ($tags as $tagInput) {
            // Tagify sends back tags in different forms depending on implementation.
            // If they are JSON strings parsing may be needed, otherwise it's just strings.
            // Assuming the frontend submits array of strings (names or IDs):
            if (preg_match('/^[a-f\d]{24}$/i', $tagInput)) {
                $processed[] = $tagInput;
            } else {
                $slug = Str::slug($tagInput);
                if (!empty($slug)) {
                    $tagModel = Tag::firstOrCreate(['slug' => $slug], ['name' => $tagInput, 'slug' => $slug]);
                    $processed[] = (string) $tagModel->_id;
                }
            }
        }
        return array_unique($processed);
    }
}
