<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name')->get();

        $response = response()->json([
            'categories' => $categories->map(fn ($c) => [
                '_id' => (string) $c->_id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description ?? '',
                'createdAt' => $c->created_at?->toISOString(),
                'updatedAt' => $c->updated_at?->toISOString(),
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
            'name' => 'required|string|min:1|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['name']);

        if (Category::where('slug', $slug)->exists()) {
            return response()->json(['error' => 'Category already exists'], 409);
        }

        $category = Category::create([
            'name' => e($validated['name']),
            'slug' => $slug,
            'description' => e($validated['description'] ?? ''),
        ]);

        return response()->json([
            'category' => [
                '_id' => (string) $category->_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
        ], 201);
    }
}
