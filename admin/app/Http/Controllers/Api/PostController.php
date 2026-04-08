<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 50);
        $cursor = $request->input('cursor');
        $categorySlug = $request->input('category');
        $tagSlug = $request->input('tag');
        $search = $request->input('q');
        $adminMode = $request->input('admin') === 'true';

        $user = AuthController::getAuthUser($request);
        $isAdmin = (bool) $user;

        $query = Post::query();

        if (! $adminMode || ! $isAdmin) {
            $query->where('status', 'published')
                  ->where('publishedAt', '<=', now());
        }

        if ($cursor) {
            $query->where('_id', '<', $cursor);
        }

        if ($categorySlug) {
            $cat = Category::where('slug', $categorySlug)->first();
            if ($cat) {
                $query->where('category', (string) $cat->_id);
            }
        }

        if ($tagSlug) {
            $tag = Tag::where('slug', $tagSlug)->first();
            if ($tag) {
                $query->where('tags', (string) $tag->_id);
            }
        }

        if ($search) {
            $query->whereRaw([
                '$text' => ['$search' => $search],
            ]);
        }

        $posts = $query->orderBy('publishedAt', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit + 1)
            ->get();

        $hasMore = $posts->count() > $limit;
        $results = $hasMore ? $posts->take($limit) : $posts;

        // Populate category and tags
        $results = $results->map(function ($post) {
            return $this->formatPost($post, false);
        });

        $nextCursor = $hasMore ? (string) $results->last()['_id'] : null;

        $response = response()->json([
            'posts' => $results->values(),
            'nextCursor' => $nextCursor,
            'hasMore' => $hasMore,
        ]);

        if (! $isAdmin) {
            $response->header('Cache-Control', 'public, s-maxage=60, stale-while-revalidate=300');
        }

        return $response;
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $post = Post::where('slug', $id)->first()
            ?? Post::find($id);

        if (! $post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $user = AuthController::getAuthUser($request);

        if ($post->status !== 'published' && ! $user) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        // Increment view count for public access
        if (! $user && $post->status === 'published') {
            $post->increment('views');
        }

        $response = response()->json(['post' => $this->formatPost($post, true)]);

        if (! $user) {
            $response->header('Cache-Control', 'public, s-maxage=60, stale-while-revalidate=300');
        }

        return $response;
    }

    public function store(Request $request): JsonResponse
    {
        $user = AuthController::getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|min:1|max:200',
            'content' => 'required|string|min:1',
            'excerpt' => 'required|string|min:1|max:500',
            'coverImage' => 'nullable|string',
            'category' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'status' => 'nullable|in:draft,published',
            'publishedAt' => 'nullable|date',
        ]);

        $slug = Str::slug($validated['title']);

        if (Post::where('slug', $slug)->exists()) {
            return response()->json(['error' => 'A post with a similar title already exists'], 409);
        }

        $status = $validated['status'] ?? 'draft';
        $pubAt = null;
        if ($status === 'published') {
            $pubAt = isset($validated['publishedAt']) ? \Carbon\Carbon::parse($validated['publishedAt']) : now();
        }

        $post = Post::create([
            'title' => e($validated['title']),
            'slug' => $slug,
            'content' => clean($validated['content']),
            'excerpt' => e($validated['excerpt']),
            'coverImage' => $validated['coverImage'] ?? '',
            'category' => $validated['category'],
            'tags' => $this->processDynamicTags($validated['tags'] ?? []),
            'status' => $status,
            'publishedAt' => $pubAt,
            'author' => $user->email,
            'readTime' => Post::calculateReadTime($validated['content']),
            'views' => 0,
        ]);

        return response()->json(['post' => $this->formatPost($post, true)], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = AuthController::getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $post = Post::find($id);
        if (! $post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|min:1|max:200',
            'content' => 'nullable|string|min:1',
            'excerpt' => 'nullable|string|min:1|max:500',
            'coverImage' => 'nullable|string',
            'category' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'status' => 'nullable|in:draft,published',
            'publishedAt' => 'nullable|date',
        ]);

        $updateData = [];

        if (isset($validated['title'])) {
            $updateData['title'] = e($validated['title']);
            $updateData['slug'] = Str::slug($validated['title']);
        }
        if (isset($validated['content'])) {
            $updateData['content'] = clean($validated['content']);
            $updateData['readTime'] = Post::calculateReadTime($validated['content']);
        }
        if (isset($validated['excerpt'])) {
            $updateData['excerpt'] = e($validated['excerpt']);
        }
        if (array_key_exists('coverImage', $validated)) {
            $updateData['coverImage'] = $validated['coverImage'];
        }
        if (isset($validated['category'])) {
            $updateData['category'] = $validated['category'];
        }
        if (isset($validated['tags'])) {
            $updateData['tags'] = $this->processDynamicTags($validated['tags']);
        }
        if (isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
            if ($validated['status'] === 'published') {
                if (isset($validated['publishedAt'])) {
                    $updateData['publishedAt'] = \Carbon\Carbon::parse($validated['publishedAt']);
                } elseif (! $post->publishedAt) {
                    $updateData['publishedAt'] = now();
                }
            } else {
                $updateData['publishedAt'] = null;
            }
        } elseif (isset($validated['publishedAt']) && $post->status === 'published') {
             $updateData['publishedAt'] = \Carbon\Carbon::parse($validated['publishedAt']);
        }

        $post->update($updateData);
        $post->refresh();

        return response()->json(['post' => $this->formatPost($post, true)]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = AuthController::getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $post = Post::find($id);
        if (! $post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }

    private function processDynamicTags(array $tags): array
    {
        $processed = [];
        foreach ($tags as $tagInput) {
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

    private function formatPost(Post $post, bool $includeContent): array
    {
        $category = $post->category ? Category::find($post->category) : null;

        $tagIds = $post->tags ?? [];
        $tags = [];
        if (! empty($tagIds)) {
            $tagModels = Tag::whereIn('_id', $tagIds)->get();
            $tags = $tagModels->map(fn ($t) => [
                '_id' => (string) $t->_id,
                'name' => $t->name,
                'slug' => $t->slug,
            ])->values()->toArray();
        }

        $data = [
            '_id' => (string) $post->_id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'coverImage' => $post->coverImage,
            'category' => $category ? [
                '_id' => (string) $category->_id,
                'name' => $category->name,
                'slug' => $category->slug,
            ] : null,
            'tags' => $tags,
            'status' => $post->status,
            'publishedAt' => $post->publishedAt?->toISOString(),
            'readTime' => $post->readTime,
            'views' => $post->views ?? 0,
            'createdAt' => $post->created_at?->toISOString(),
        ];

        if ($includeContent) {
            $data['content'] = $post->content;
            $data['author'] = $post->author;
        }

        return $data;
    }
}

if (! function_exists('clean')) {
    function clean(string $value): string
    {
        return strip_tags($value, '<p><br><strong><em><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><code><pre><blockquote>');
    }
}
