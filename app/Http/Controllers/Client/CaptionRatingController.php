<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CaptionHistory;
use Illuminate\Http\Request;

class CaptionRatingController extends Controller
{
    /**
     * Rate a caption
     */
    public function rate(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500'
        ]);
        
        $caption = CaptionHistory::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $caption->update([
            'rating' => $validated['rating'],
            'feedback' => $validated['feedback'] ?? null
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil disimpan! Terima kasih atas feedback Anda.'
        ]);
    }
    
    /**
     * Get personal stats for client
     */
    public function myStats()
    {
        $userId = auth()->id();
        
        // Total generations
        $totalGenerations = CaptionHistory::where('user_id', $userId)->count();
        
        // Average rating
        $avgRating = CaptionHistory::where('user_id', $userId)
            ->whereNotNull('rating')
            ->avg('rating');
        
        // Best performing category (based on ratings)
        $bestCategory = CaptionHistory::where('user_id', $userId)
            ->whereNotNull('rating')
            ->selectRaw('category, AVG(rating) as avg_rating, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('avg_rating', 'desc')
            ->first();
        
        // Best performing platform
        $bestPlatform = CaptionHistory::where('user_id', $userId)
            ->whereNotNull('rating')
            ->selectRaw('platform, AVG(rating) as avg_rating, COUNT(*) as count')
            ->groupBy('platform')
            ->orderBy('avg_rating', 'desc')
            ->first();
        
        // Recent rated captions
        $recentRated = CaptionHistory::where('user_id', $userId)
            ->whereNotNull('rating')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['caption_text', 'rating', 'category', 'platform', 'created_at']);
        
        // Improvement suggestions
        $suggestions = $this->generateSuggestions($userId);
        
        return view('client.my-stats', compact(
            'totalGenerations',
            'avgRating',
            'bestCategory',
            'bestPlatform',
            'recentRated',
            'suggestions'
        ));
    }
    
    /**
     * Generate personalized suggestions
     */
    private function generateSuggestions($userId)
    {
        $suggestions = [];
        
        // Check if user has low-rated captions
        $lowRatedCount = CaptionHistory::where('user_id', $userId)
            ->where('rating', '<=', 2)
            ->count();
        
        if ($lowRatedCount > 3) {
            $suggestions[] = [
                'type' => 'warning',
                'title' => 'Coba Variasi Tone',
                'message' => 'Beberapa caption Anda rated rendah. Coba variasi tone (casual, formal, persuasive) untuk hasil lebih baik.'
            ];
        }
        
        // Check if user uses same category too much
        $categoryDistribution = CaptionHistory::where('user_id', $userId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->first();
        
        if ($categoryDistribution && $categoryDistribution->count > 10) {
            $suggestions[] = [
                'type' => 'info',
                'title' => 'Eksplorasi Kategori Lain',
                'message' => 'Anda sering pakai kategori ' . $categoryDistribution->category . '. Coba kategori lain untuk konten lebih variatif!'
            ];
        }
        
        // Check if user never uses local language
        $hasLocalLanguage = CaptionHistory::where('user_id', $userId)
            ->whereNotNull('local_language')
            ->exists();
        
        if (!$hasLocalLanguage) {
            $suggestions[] = [
                'type' => 'tip',
                'title' => 'Coba Bahasa Daerah',
                'message' => 'Belum pernah coba bahasa daerah? Caption dengan sentuhan lokal bisa lebih engage dengan audience!'
            ];
        }
        
        return $suggestions;
    }
}
