<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $connection = \DB::connection('mongodb');
            $database = config('database.connections.mongodb.database', 'ashickey');
            $connection->getMongoClient()->selectDatabase($database)->command(['ping' => 1]);

            return response()->json([
                'status' => 'ok',
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 503);
        }
    }
}
