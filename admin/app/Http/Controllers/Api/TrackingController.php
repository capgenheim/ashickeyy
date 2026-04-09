<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    private $mongo;

    public function __construct()
    {
        $this->mongo = new MongoClient(env('DB_DSN', 'mongodb://ashickey-mongo:27017'));
    }

    /**
     * Stash geolocation analytical trace
     */
    public function logAnalytics(Request $request) 
    {
        $validated = $request->validate([
            'visitorId' => 'required|string',
            'postSlug' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'userAgent' => 'nullable|string',
            'action' => 'nullable|string',
        ]);

        $db = $this->mongo->ashickey;
        $collection = $db->visitor_analytics;

        try {
            $collection->insertOne([
                'visitorId' => $validated['visitorId'],
                'postSlug' => $validated['postSlug'],
                'action' => $validated['action'] ?? 'app_open',
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'userAgent' => $request->userAgent() ?? $validated['userAgent'],
                'ip_address' => $request->ip(),
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Analytics Log Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Register a new Push Notification Subscription Vault
     */
    public function subscribePush(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $db = $this->mongo->ashickey;
        $collection = $db->push_subscriptions;

        try {
            // upsert to prevent exact duplicate endpoints
            $collection->updateOne(
                ['endpoint' => $validated['endpoint']],
                ['$set' => [
                    'endpoint' => $validated['endpoint'],
                    'keys' => [
                        'p256dh' => $validated['keys']['p256dh'],
                        'auth' => $validated['keys']['auth']
                    ],
                    'updated_at' => new \MongoDB\BSON\UTCDateTime()
                ]],
                ['upsert' => true]
            );

            return response()->json(['status' => 'subscribed']);
        } catch (\Exception $e) {
            Log::error('Push Subscribe Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Subscribe email for newsletters
     */
    public function subscribeEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);

        $db = $this->mongo->ashickey;
        $collection = $db->email_subscriptions;

        try {
            $collection->updateOne(
                ['email' => $validated['email']],
                ['$set' => [
                    'email' => $validated['email'],
                    'subscribed_at' => new \MongoDB\BSON\UTCDateTime()
                ]],
                ['upsert' => true]
            );

            return response()->json(['status' => 'subscribed']);
        } catch (\Exception $e) {
            Log::error('Email Subscribe Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to subscribe.'], 500);
        }
    }
}
