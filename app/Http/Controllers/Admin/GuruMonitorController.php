<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MLTrainingData;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruMonitorController extends Controller
{
    /**
     * Overview semua guru — tabel kinerja + stats global
     */
    public function index()
    {
        // Per-guru stats in one query
        $gurus = User::where('role', 'guru')
            ->withCount([
                'mlTrainingData as total_entries',
                'mlTrainingData as excellent_entries' => fn($q) => $q->where('quality_rating', 'excellent'),
                'mlTrainingData as good_entries'      => fn($q) => $q->where('quality_rating', 'good'),
                'mlTrainingData as fair_entries'       => fn($q) => $q->where('quality_rating', 'fair'),
                'mlTrainingData as poor_entries'       => fn($q) => $q->where('quality_rating', 'poor'),
            ])
            ->orderByDesc('total_entries')
            ->get()
            ->map(function (User $guru) {
                $pendingWithdrawal = WithdrawalRequest::where('user_id', $guru->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->sum('amount');

                return [
                    'id'                  => $guru->id,
                    'name'                => $guru->name,
                    'email'               => $guru->email,
                    'total_entries'       => $guru->total_entries,
                    'excellent_entries'   => $guru->excellent_entries,
                    'good_entries'        => $guru->good_entries,
                    'fair_entries'        => $guru->fair_entries,
                    'poor_entries'        => $guru->poor_entries,
                    'quality_score'       => $guru->total_entries > 0
                        ? round((($guru->excellent_entries * 4) + ($guru->good_entries * 3) + ($guru->fair_entries * 2) + ($guru->poor_entries * 1)) / ($guru->total_entries * 4) * 100)
                        : 0,
                    'total_earnings'      => $guru->guru_total_earnings,
                    'pending_withdrawal'  => $pendingWithdrawal,
                    'available_balance'   => $guru->guru_total_earnings - $pendingWithdrawal,
                    'last_submission'     => MLTrainingData::where('guru_id', $guru->id)->latest()->value('created_at'),
                ];
            });

        $globalStats = [
            'total_gurus'       => User::where('role', 'guru')->count(),
            'total_entries'     => MLTrainingData::count(),
            'excellent_entries' => MLTrainingData::where('quality_rating', 'excellent')->count(),
            'total_paid_out'    => WithdrawalRequest::whereHas('user', fn($q) => $q->where('role', 'guru'))
                                    ->where('status', 'completed')->sum('amount'),
            'pending_payout'    => WithdrawalRequest::whereHas('user', fn($q) => $q->where('role', 'guru'))
                                    ->whereIn('status', ['pending', 'processing'])->sum('amount'),
            'entries_this_month'=> MLTrainingData::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)->count(),
        ];

        return view('admin.guru-monitor.index', compact('gurus', 'globalStats'));
    }

    /**
     * Detail kinerja satu guru
     */
    public function show(User $guru)
    {
        abort_if($guru->role !== 'guru', 404);

        $entries = MLTrainingData::where('guru_id', $guru->id)
            ->latest()
            ->paginate(20);

        $withdrawals = WithdrawalRequest::where('user_id', $guru->id)
            ->orderByDesc('created_at')
            ->get();

        $pendingWithdrawal = $withdrawals->whereIn('status', ['pending', 'processing'])->sum('amount');

        // Quality distribution
        $qualityDist = MLTrainingData::where('guru_id', $guru->id)
            ->select('quality_rating', DB::raw('count(*) as count'))
            ->groupBy('quality_rating')
            ->pluck('count', 'quality_rating');

        // Entries per day (last 30 days)
        $activityChart = MLTrainingData::where('guru_id', $guru->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $stats = [
            'total_entries'     => MLTrainingData::where('guru_id', $guru->id)->count(),
            'excellent_entries' => MLTrainingData::where('guru_id', $guru->id)->where('quality_rating', 'excellent')->count(),
            'total_earnings'    => $guru->guru_total_earnings,
            'pending_withdrawal'=> $pendingWithdrawal,
            'available_balance' => $guru->guru_total_earnings - $pendingWithdrawal,
            'total_paid_out'    => $withdrawals->where('status', 'completed')->sum('amount'),
        ];

        return view('admin.guru-monitor.show', compact('guru', 'entries', 'withdrawals', 'stats', 'qualityDist', 'activityChart'));
    }

    /**
     * Adjust guru earnings manually (koreksi admin)
     */
    public function adjustEarnings(Request $request, User $guru)
    {
        abort_if($guru->role !== 'guru', 404);

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $guru->increment('guru_total_earnings', $validated['amount']);

        return back()->with('success', "Earnings guru {$guru->name} disesuaikan sebesar Rp " . number_format($validated['amount'], 0, ',', '.') . ". Alasan: {$validated['reason']}");
    }
}
