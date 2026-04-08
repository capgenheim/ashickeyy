<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    public function index(Request $request): JsonResponse
    {
        $user = AuthController::getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $media = Media::orderBy('created_at', 'desc')->limit(100)->get();

        return response()->json([
            'media' => $media->map(fn ($m) => [
                '_id' => (string) $m->_id,
                'filename' => $m->filename,
                'originalName' => $m->originalName,
                'url' => $m->url,
                'mimeType' => $m->mimeType,
                'size' => $m->size,
                'createdAt' => $m->created_at?->toISOString(),
            ])->values(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = AuthController::getAuthUser($request);
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'file' => 'required|file|max:5120',
        ]);

        $file = $request->file('file');

        if (! in_array($file->getMimeType(), self::ALLOWED_TYPES)) {
            return response()->json(
                ['error' => 'Invalid file type. Allowed: JPEG, PNG, GIF, WebP, SVG'],
                400
            );
        }

        $hash = Str::random(32);
        $ext = $file->getClientOriginalExtension();
        $filename = "{$hash}.{$ext}";

        $file->move(public_path('uploads'), $filename);

        $media = Media::create([
            'filename' => $filename,
            'originalName' => $file->getClientOriginalName(),
            'url' => "/uploads/{$filename}",
            'mimeType' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json([
            'media' => [
                '_id' => (string) $media->_id,
                'filename' => $media->filename,
                'originalName' => $media->originalName,
                'url' => $media->url,
                'mimeType' => $media->mimeType,
                'size' => $media->size,
            ],
        ], 201);
    }
}
