<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CaptionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $platform = $request->get('platform');
        $category = $request->get('category');
        $sort     = $request->get('sort', 'latest'); // latest, top_rated, most_liked

        $query = CaptionHistory::where('is_public', true)
            ->with('user:id,name,avatar,business_type')
            ->whereNotNull('rating')
            ->where('rating', '>=', 3);

        if ($platform) $query->where('platform', $platform);
        if ($category) $query->where('category', $category);

        $query = match ($sort) {
            'top_rated'  => $query->orderByDesc('rating')->orderByDesc('likes_count'),
            'most_liked' => $query->orderByDesc('likes_count')->orderByDesc('rating'),
            default      => $query->orderByDesc('created_at'),
        };

        $captions = $query->paginate(12);

        // Leaderboard: top creators this month
        $leaderboard = User::select('users.id', 'users.name', 'users.avatar', 'users.business_type')
            ->join('caption_histories', 'users.id', '=', 'caption_histories.user_id')
            ->where('caption_histories.is_public', true)
            ->where('caption_histories.created_at', '>=', now()->startOfMonth())
            ->groupBy('users.id', 'users.name', 'users.avatar', 'users.business_type')
            ->selectRaw('COUNT(*) as shared_count, ROUND(AVG(caption_histories.rating), 1) as avg_rating, SUM(caption_histories.likes_count) as total_likes')
            ->orderByDesc('total_likes')
            ->orderByDesc('avg_rating')
            ->limit(10)
            ->get();

        return view('client.explore', compact('captions', 'leaderboard', 'platform', 'category', 'sort'));
    }

    public function togglePublic(Request $request, CaptionHistory $caption)
    {
        if ($caption->user_id !== auth()->id()) abort(403);

        $caption->update(['is_public' => !$caption->is_public]);

        return response()->json([
            'success'   => true,
            'is_public' => $caption->is_public,
            'message'   => $caption->is_public ? 'Caption dipublikasikan ke Explore!' : 'Caption dihapus dari Explore.',
        ]);
    }

    public function like(CaptionHistory $caption)
    {
        if (!$caption->is_public) abort(404);

        // Simple like toggle via session (no extra table needed)
        $key = 'liked_captions.' . $caption->id;
        if (session()->has($key)) {
            $caption->decrement('likes_count');
            session()->forget($key);
            $liked = false;
        } else {
            $caption->increment('likes_count');
            session()->put($key, true);
            $liked = true;
        }

        return response()->json([
            'success'     => true,
            'liked'       => $liked,
            'likes_count' => $caption->fresh()->likes_count,
        ]);
    }
}
