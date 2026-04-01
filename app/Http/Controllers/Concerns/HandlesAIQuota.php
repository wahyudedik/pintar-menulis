<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

/**
 * Reusable AI quota check & consume logic for AI generator controllers.
 */
trait HandlesAIQuota
{
    /**
     * Get credit cost for a feature.
     */
    protected function creditCost(string $feature): int
    {
        return config("credits.{$feature}", 1);
    }

    /**
     * Check if the current user has a valid subscription and enough quota.
     * Pass $feature to check against specific credit cost.
     */
    protected function checkQuota(?string $feature = null): ?array
    {
        $sub = auth()->user()->currentSubscription();
        if (!$sub || !$sub->isValid()) {
            return ['success' => false, 'quota_error' => true, 'message' => '⚡ Kamu belum memiliki langganan aktif.'];
        }

        $cost = $feature ? $this->creditCost($feature) : 1;
        if ($sub->remaining_quota < $cost) {
            return [
                'success'     => false,
                'quota_error' => true,
                'message'     => "🚫 Kuota tidak cukup. Fitur ini butuh {$cost} kredit, sisa kamu {$sub->remaining_quota}.",
            ];
        }
        return null;
    }

    /**
     * Return a 403 JSON response from a quota error array.
     */
    protected function quotaResponse(array $err): JsonResponse
    {
        return response()->json($err, 403);
    }

    /**
     * Consume credits for a feature and return the remaining count.
     */
    protected function consumeQuota(?string $feature = null): int
    {
        $cost = $feature ? $this->creditCost($feature) : 1;
        $sub  = auth()->user()->currentSubscription();
        $sub->consumeQuota($cost);
        $sub->refresh();
        return $sub->remaining_quota;
    }

    /**
     * Log the exception internally and return a safe generic error response.
     */
    protected function errorResponse(\Exception $e, string $userMessage = 'Terjadi kesalahan. Silakan coba lagi.', int $status = 500): JsonResponse
    {
        \Log::error($userMessage . ': ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'trace'   => $e->getTraceAsString(),
        ]);
        return response()->json(['success' => false, 'message' => $userMessage], $status);
    }
}
