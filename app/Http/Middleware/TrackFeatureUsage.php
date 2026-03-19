<?php

namespace App\Http\Middleware;

use App\Models\FeatureUsageLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackFeatureUsage
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        // Only track authenticated clients
        if (!auth()->check() || auth()->user()->role !== 'client') {
            return $response;
        }

        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return $response;
        }

        $feature = FeatureUsageLog::resolveFeature($routeName);
        if (!$feature) {
            return $response;
        }

        try {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $success    = $response->getStatusCode() < 400;
            $user       = auth()->user();
            $package    = null;

            try {
                $sub     = $user->currentSubscription()?->load('package');
                $package = $sub?->package?->name;
            } catch (\Throwable) {}

            FeatureUsageLog::create([
                'user_id'              => $user->id,
                'feature_key'          => $feature[0],
                'feature_label'        => $feature[1],
                'feature_category'     => $feature[2],
                'route_name'           => $routeName,
                'http_method'          => $request->method(),
                'duration_ms'          => $durationMs,
                'success'              => $success,
                'subscription_package' => $package,
                'usage_date'           => now()->toDateString(),
            ]);
        } catch (\Throwable) {
            // Never block the user request due to tracking failure
        }

        return $response;
    }
}
