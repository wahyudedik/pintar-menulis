<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

/**
 * Reusable AI quota check & consume logic for AI generator controllers.
 */
trait HandlesAIQuota
{
    /**
     * Check if the current user has a valid subscription and remaining quota.
     * Returns an error array if quota is exceeded, null if OK.
     */
    protected function checkQuota(): ?array
    {
        $sub = auth()->user()->currentSubscription();
        if (!$sub || !$sub->isValid()) {
            return ['success' => false, 'quota_error' => true, 'message' => '⚡ Kamu belum memiliki langganan aktif.'];
        }
        if ($sub->remaining_quota <= 0) {
            return ['success' => false, 'quota_error' => true, 'message' => '🚫 Kuota AI kamu sudah habis bulan ini.'];
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
     * Consume one quota unit and return the remaining count.
     */
    protected function consumeQuota(): int
    {
        $sub = auth()->user()->currentSubscription();
        $sub->consumeQuota(1);
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
