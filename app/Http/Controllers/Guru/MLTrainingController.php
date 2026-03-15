<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MLTrainingData;
use App\Models\MLModelVersion;
use App\Models\Order;
use App\Models\CaptionAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MLTrainingController extends Controller
{
    const EARNINGS_PER_ENTRY = 5; // Rp 5 per training data entry

    // Training Dashboard
    public function index()
    {
        // Get completed orders for review
        $pendingReviews = Order::where('status', 'completed')
            ->whereNotNull('result')
            ->with(['user', 'operator'])
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        // Get successful captions from analytics for training
        $successfulCaptions = CaptionAnalytics::where('marked_as_successful', true)
            ->orWhere('engagement_rate', '>=', 5)
            ->with('user')
            ->orderByDesc('engagement_rate')
            ->take(10)
            ->get();

        // Get training stats
        $stats = [
            'total_training_data' => MLTrainingData::count(),
            'pending_reviews' => Order::where('status', 'completed')
                ->whereNotNull('result')
                ->count(),
            'excellent_quality' => MLTrainingData::where('quality_rating', 'excellent')->count(),
            'average_quality' => $this->calculateAverageQuality(),
            'successful_captions' => CaptionAnalytics::where('marked_as_successful', true)->count(),
            'my_total_entries' => MLTrainingData::where('guru_id', auth()->id())->count(),
            'my_total_earnings' => auth()->user()->guru_total_earnings,
        ];

        // Get latest model version
        $latestModel = MLModelVersion::latest()->first();

        return view('guru.training-dashboard', compact('pendingReviews', 'stats', 'latestModel', 'successfulCaptions'));
    }

    // Show review form for specific order
    public function show(Order $order)
    {
        if ($order->status !== 'completed' || !$order->result) {
            return redirect()->route('guru.training')
                ->with('error', 'Order ini belum bisa direview');
        }

        $order->load(['user', 'operator']);

        return view('guru.training-review', compact('order'));
    }

    // Submit training data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'quality_rating' => 'required|in:poor,fair,good,excellent',
            'corrected_output' => 'nullable|string',
            'feedback_notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // Prevent duplicate submission for same order by same guru
        $alreadySubmitted = MLTrainingData::where('guru_id', auth()->id())
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.order_id')) = ?", [$order->id])
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('guru.training')
                ->with('error', 'Kamu sudah pernah submit training data untuk order ini.');
        }

        // Create training data
        $training = MLTrainingData::create([
            'guru_id' => auth()->id(),
            'copywriting_request_id' => null,
            'input_prompt' => $order->brief ?? '(brief tidak tersedia)',
            'ai_output' => $order->result,
            'corrected_output' => $validated['corrected_output'] ?? $order->result,
            'quality_rating' => $validated['quality_rating'],
            'feedback_notes' => $validated['feedback_notes'],
            'metadata' => [
                'order_id' => $order->id,
                'category' => $order->category,
                'operator_id' => $order->operator_id,
            ],
            'earnings_paid' => true,
        ]);

        // Credit Rp 5 to guru's earnings balance
        auth()->user()->increment('guru_total_earnings', self::EARNINGS_PER_ENTRY);

        // Invalidate few-shot cache so new excellent data is picked up immediately
        if ($validated['quality_rating'] === 'excellent') {
            \Illuminate\Support\Facades\Cache::forget('fewshot_' . md5("{$order->category}__"));
        }

        return redirect()->route('guru.training')
            ->with('success', 'Training data berhasil disimpan! Kamu mendapat Rp ' . number_format(self::EARNINGS_PER_ENTRY, 0, ',', '.') . ' 🎉');
    }
    
    // Train from caption analytics
    public function trainFromCaption(Request $request)
    {
        $validated = $request->validate([
            'caption_id' => 'required|exists:caption_analytics,id',
            'quality_rating' => 'required|in:poor,fair,good,excellent',
            'corrected_output' => 'nullable|string',
            'feedback_notes' => 'nullable|string',
        ]);

        $caption = CaptionAnalytics::findOrFail($validated['caption_id']);

        // Prevent duplicate submission for same caption by same guru
        $alreadySubmitted = MLTrainingData::where('guru_id', auth()->id())
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.caption_id')) = ?", [$caption->id])
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('guru.training')
                ->with('error', 'Kamu sudah pernah submit training data untuk caption ini.');
        }

        // Create training data from caption
        MLTrainingData::create([
            'guru_id' => auth()->id(),
            'copywriting_request_id' => null,
            'input_prompt' => "Category: {$caption->category}, Subcategory: {$caption->subcategory}, Platform: {$caption->platform}, Tone: {$caption->tone}",
            'ai_output' => $caption->caption_text,
            'corrected_output' => $validated['corrected_output'] ?? $caption->caption_text,
            'quality_rating' => $validated['quality_rating'],
            'feedback_notes' => $validated['feedback_notes'],
            'metadata' => [
                'caption_id' => $caption->id,
                'category' => $caption->category,
                'platform' => $caption->platform,
                'engagement_rate' => $caption->engagement_rate,
                'likes' => $caption->likes,
                'comments' => $caption->comments,
                'shares' => $caption->shares,
            ],
            'earnings_paid' => true,
        ]);

        // Credit Rp 5 to guru's earnings balance
        auth()->user()->increment('guru_total_earnings', self::EARNINGS_PER_ENTRY);

        // Invalidate few-shot cache so new excellent data is picked up immediately
        if ($validated['quality_rating'] === 'excellent') {
            \Illuminate\Support\Facades\Cache::forget('fewshot_' . md5("{$caption->category}_{$caption->platform}_"));
        }

        // Mark caption as used for training
        $caption->update(['used_for_training' => true]);

        return redirect()->route('guru.training')
            ->with('success', 'Caption berhasil ditambahkan ke training data! Kamu mendapat Rp ' . number_format(self::EARNINGS_PER_ENTRY, 0, ',', '.') . ' 🎉');
    }

    // Training History
    public function history()
    {
        $trainingData = MLTrainingData::with('guru')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => MLTrainingData::count(),
            'excellent' => MLTrainingData::where('quality_rating', 'excellent')->count(),
            'good' => MLTrainingData::where('quality_rating', 'good')->count(),
            'fair' => MLTrainingData::where('quality_rating', 'fair')->count(),
            'poor' => MLTrainingData::where('quality_rating', 'poor')->count(),
        ];

        return view('guru.training-history', compact('trainingData', 'stats'));
    }

    // Model Performance Analytics
    public function analytics()
    {
        $models = MLModelVersion::orderBy('created_at', 'desc')->get();

        // Quality distribution
        $qualityDistribution = MLTrainingData::select('quality_rating', DB::raw('count(*) as count'))
            ->groupBy('quality_rating')
            ->get()
            ->pluck('count', 'quality_rating');

        // Category distribution - extract from metadata JSON
        $allData = MLTrainingData::whereNotNull('metadata')->get();
        $categoryDistribution = collect();
        foreach ($allData as $data) {
            $metadata = is_string($data->metadata) ? json_decode($data->metadata, true) : $data->metadata;
            if (isset($metadata['category'])) {
                $category = $metadata['category'];
                $categoryDistribution[$category] = ($categoryDistribution[$category] ?? 0) + 1;
            }
        }

        // Training data over time (last 30 days)
        $trainingOverTime = MLTrainingData::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $stats = [
            'total_training_data' => MLTrainingData::count(),
            'total_models' => MLModelVersion::count(),
            'average_quality' => $this->calculateAverageQuality(),
            'improvement_rate' => $this->calculateImprovementRate(),
        ];

        return view('guru.analytics', compact('models', 'qualityDistribution', 'categoryDistribution', 'trainingOverTime', 'stats'));
    }

    // Helper: Calculate average quality score
    private function calculateAverageQuality()
    {
        $ratings = [
            'poor' => 1,
            'fair' => 2,
            'good' => 3,
            'excellent' => 4,
        ];

        $data = MLTrainingData::select('quality_rating', DB::raw('count(*) as count'))
            ->groupBy('quality_rating')
            ->get();

        $totalScore = 0;
        $totalCount = 0;

        foreach ($data as $item) {
            $score = $ratings[$item->quality_rating] ?? 0;
            $totalScore += $score * $item->count;
            $totalCount += $item->count;
        }

        return $totalCount > 0 ? round($totalScore / $totalCount, 2) : 0;
    }

    // Helper: Calculate improvement rate
    private function calculateImprovementRate()
    {
        $last30Days = MLTrainingData::where('created_at', '>=', now()->subDays(30))
            ->where('quality_rating', 'excellent')
            ->count();

        $previous30Days = MLTrainingData::whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->where('quality_rating', 'excellent')
            ->count();

        if ($previous30Days == 0) return 0;

        return round((($last30Days - $previous30Days) / $previous30Days) * 100, 1);
    }
}

