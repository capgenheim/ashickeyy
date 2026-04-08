<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::orderBy('name')->get();

        $response = response()->json([
            'tags' => $tags->map(fn ($t) => [
                '_id' => (string) $t->_id,
                'name' => $t->name,
                'slug' => $t->slug,
                'createdAt' => $t->created_at?->toISOString(),
                'updatedAt' => $t->updated_at?->toISOString(),
            ])->values(),
        ]);

        $response->header('Cache-Control', 'public, s-maxage=300, stale-while-revalidate=600');

        return $response;
    }

    public function store(Request $request): JsonResponse
    {
        $user = AuthController::getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:1|max:50',
        ]);

        $slug = Str::slug($validated['name']);

        if (Tag::where('slug', $slug)->exists()) {
            return response()->json(['error' => 'Tag already exists'], 409);
        }

        $tag = Tag::create([
            'name' => e($validated['name']),
            'slug' => $slug,
        ]);

        return response()->json([
            'tag' => [
                '_id' => (string) $tag->_id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ],
        ], 201);
    }
}
