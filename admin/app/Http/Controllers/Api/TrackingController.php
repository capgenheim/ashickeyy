<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
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

        $db = app('db')->connection('mongodb')->getDatabase();
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

            AuditLog::record($validated['action'] ?? 'page_view', 'frontend', [
                'resource' => 'analytics',
                'resource_id' => $validated['visitorId'],
                'details' => [
                    'postSlug' => $validated['postSlug'] ?? null,
                    'action' => $validated['action'] ?? 'app_open',
                ],
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

        $db = app('db')->connection('mongodb')->getDatabase();
        $collection = $db->push_subscriptions;

        try {
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

            AuditLog::record('subscribe', 'frontend', [
                'resource' => 'push_subscription',
                'details' => ['endpoint_prefix' => substr($validated['endpoint'], 0, 50)],
            ]);

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

        $db = app('db')->connection('mongodb')->getDatabase();
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

            AuditLog::record('subscribe', 'frontend', [
                'resource' => 'email_subscription',
                'details' => ['email' => $validated['email']],
            ]);

            return response()->json(['status' => 'subscribed']);
        } catch (\Exception $e) {
            Log::error('Email Subscribe Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to subscribe.'], 500);
        }
    }
}
