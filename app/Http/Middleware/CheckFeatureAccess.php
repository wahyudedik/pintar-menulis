<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFeatureAccess
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = $request->user();
        $sub  = $user?->currentSubscription()?->load('package');

        if (!$sub || !$sub->isValid() || !$sub->package) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success'     => false,
                    'feature_locked' => true,
                    'message'     => 'Fitur ini membutuhkan langganan aktif.',
                ], 403);
            }
            return redirect()->route('pricing')->with('error', 'Fitur ini membutuhkan langganan aktif.');
        }

        if (!$sub->package->hasFeature($feature)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success'     => false,
                    'feature_locked' => true,
                    'message'     => 'Fitur ini tidak tersedia di paket ' . $sub->package->name . '. Upgrade untuk mengakses.',
                    'upgrade_url' => route('pricing'),
                ], 403);
            }
            return redirect()->route('pricing')->with('error', 'Upgrade paket untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}
