<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\MLTrainingData;
use App\Models\MLModelVersion;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MLTrainingController extends Controller
{
    // Training Dashboard
    public function index()
    {
        // Get completed orders for review
        // Since ml_training_data doesn't have order_id, we'll show all completed orders
        $pendingReviews = Order::where('status', 'completed')
            ->whereNotNull('result')
            ->with(['user', 'operator'])
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        // Get training stats
        $stats = [
            'total_training_data' => MLTrainingData::count(),
            'pending_reviews' => Order::where('status', 'completed')
                ->whereNotNull('result')
                ->count(),
            'excellent_quality' => MLTrainingData::where('quality_rating', 'excellent')->count(),
            'average_quality' => $this->calculateAverageQuality(),
        ];

        // Get latest model version
        $latestModel = MLModelVersion::latest()->first();

        return view('guru.training-dashboard', compact('pendingReviews', 'stats', 'latestModel'));
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

        // Create training data
        MLTrainingData::create([
            'guru_id' => auth()->id(),
            'copywriting_request_id' => null, // Not linked to copywriting request
            'input_prompt' => $order->brief,
            'ai_output' => $order->result,
            'corrected_output' => $validated['corrected_output'] ?? $order->result,
            'quality_rating' => $validated['quality_rating'],
            'feedback_notes' => $validated['feedback_notes'],
            'metadata' => [
                'order_id' => $order->id,
                'category' => $order->category,
                'operator_id' => $order->operator_id,
            ],
        ]);

        return redirect()->route('guru.training')
            ->with('success', 'Training data berhasil disimpan!');
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

