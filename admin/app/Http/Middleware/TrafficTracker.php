<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AnalyticsEntry;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Log;

class TrafficTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $durationMs = ($endTime - $startTime) * 1000;

        try {
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());

            // Skip tracking OPTIONS preflight requests
            if ($request->isMethod('OPTIONS')) {
                return $response;
            }

            $ip = $request->ip();
            $country = 'Unknown';
            $city = 'Unknown';
            $lat = null;
            $lon = null;

            // Only attempt location mapping if IP is not local
            if ($ip !== '127.0.0.1' && $ip !== '::1' && !str_starts_with($ip, '192.168.') && !str_starts_with($ip, '10.')) {
                if ($position = Location::get($ip)) {
                    $country = $position->countryName;
                    $city = $position->cityName;
                    $lat = $position->latitude;
                    $lon = $position->longitude;
                }
            }

            AnalyticsEntry::create([
                'ip_address' => $ip,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent() ?? 'Unknown',
                'is_robot' => $agent->isRobot(),
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
                'country' => $country,
                'city' => $city,
                'latitude' => $lat,
                'longitude' => $lon,
                'response_time_ms' => round($durationMs, 2),
                'response_status' => $response->getStatusCode(),
                'user_id' => auth()->check() ? auth()->id() : null,
            ]);

        } catch (\Exception $e) {
            // Failsafe so analytics doesn't break app flow
            Log::error('Analytics logging failed: ' . $e->getMessage());
        }

        return $response;
    }
}
