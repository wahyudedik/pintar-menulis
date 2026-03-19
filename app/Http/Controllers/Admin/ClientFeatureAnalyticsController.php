<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureUsageLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientFeatureAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // days
        $category = $request->get('category', 'all');
        $from = now()->subDays((int)$period)->startOfDay();

        $query = FeatureUsageLog::where('created_at', '>=', $from);
        if ($category !== 'all') {
            $query->where('feature_category', $category);
        }

        // ── Summary cards ─────────────────────────────────────────────────────
        $totalUsage      = (clone $query)->count();
        $activeUsers     = (clone $query)->distinct('user_id')->count('user_id');
        $totalFeatures   = (clone $query)->distinct('feature_key')->count('feature_key');
        $avgResponseMs   = (int) ((clone $query)->whereNotNull('duration_ms')->avg('duration_ms') ?? 0);

        // ── Top features by usage ─────────────────────────────────────────────
        $topFeatures = (clone $query)
            ->select('feature_key', 'feature_label', 'feature_category',
                DB::raw('COUNT(*) as total_uses'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('ROUND(AVG(duration_ms)) as avg_ms'),
                DB::raw('SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as success_count')
            )
            ->groupBy('feature_key', 'feature_label', 'feature_category')
            ->orderByDesc('total_uses')
            ->limit(20)
            ->get()
            ->map(function ($f) use ($totalUsage) {
                $f->usage_pct    = $totalUsage > 0 ? round($f->total_uses / $totalUsage * 100, 1) : 0;
                $f->success_rate = $f->total_uses > 0 ? round($f->success_count / $f->total_uses * 100, 1) : 0;
                return $f;
            });

        // ── Usage by category ─────────────────────────────────────────────────
        $byCategory = (clone $query)
            ->select('feature_category',
                DB::raw('COUNT(*) as total_uses'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('COUNT(DISTINCT feature_key) as feature_count')
            )
            ->groupBy('feature_category')
            ->orderByDesc('total_uses')
            ->get();

        // ── Daily trend (last N days) ─────────────────────────────────────────
        $dailyTrend = FeatureUsageLog::where('created_at', '>=', $from)
            ->select('usage_date', DB::raw('COUNT(*) as uses'), DB::raw('COUNT(DISTINCT user_id) as users'))
            ->groupBy('usage_date')
            ->orderBy('usage_date')
            ->get()
            ->keyBy(fn($r) => $r->usage_date->format('Y-m-d'));

        // Fill missing dates
        $trendDates  = [];
        $trendUses   = [];
        $trendUsers  = [];
        for ($i = (int)$period - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $trendDates[] = now()->subDays($i)->format('d M');
            $trendUses[]  = $dailyTrend[$d]->uses ?? 0;
            $trendUsers[] = $dailyTrend[$d]->users ?? 0;
        }

        // ── Underused features (in featureMap but 0 or low usage) ────────────
        $allFeatureKeys = collect(FeatureUsageLog::featureMap())
            ->unique(fn($v) => $v[0])
            ->mapWithKeys(fn($v, $k) => [$v[0] => ['label' => $v[1], 'category' => $v[2]]]);

        $usedKeys = (clone $query)->distinct('feature_key')->pluck('feature_key')->toArray();
        $underused = $allFeatureKeys->filter(fn($v, $k) => !in_array($k, $usedKeys));

        // ── Per-package breakdown ─────────────────────────────────────────────
        $byPackage = (clone $query)
            ->select('subscription_package',
                DB::raw('COUNT(*) as total_uses'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->groupBy('subscription_package')
            ->orderByDesc('total_uses')
            ->get();

        // ── Power users (top 10 by usage) ────────────────────────────────────
        $powerUsers = (clone $query)
            ->select('user_id',
                DB::raw('COUNT(*) as total_uses'),
                DB::raw('COUNT(DISTINCT feature_key) as features_used'),
                DB::raw('MAX(created_at) as last_active')
            )
            ->groupBy('user_id')
            ->orderByDesc('total_uses')
            ->limit(10)
            ->with('user:id,name,email,avatar')
            ->get();

        // ── Optimization insights ─────────────────────────────────────────────
        $insights = $this->generateInsights($topFeatures, $byCategory, $underused, $activeUsers, $totalUsage, $period);

        // ── Categories list for filter ────────────────────────────────────────
        $categories = FeatureUsageLog::distinct('feature_category')->pluck('feature_category')->sort()->values();

        return view('admin.client-feature-analytics.index', compact(
            'period', 'category',
            'totalUsage', 'activeUsers', 'totalFeatures', 'avgResponseMs',
            'topFeatures', 'byCategory', 'byPackage', 'powerUsers',
            'trendDates', 'trendUses', 'trendUsers',
            'underused', 'insights', 'categories'
        ));
    }

    public function userDetail(Request $request, User $user)
    {
        $period = $request->get('period', '30');
        $from   = now()->subDays((int)$period)->startOfDay();

        $logs = FeatureUsageLog::where('user_id', $user->id)
            ->where('created_at', '>=', $from);

        $totalUses    = (clone $logs)->count();
        $featuresUsed = (clone $logs)->distinct('feature_key')->count('feature_key');
        $lastActive   = (clone $logs)->max('created_at');

        $byFeature = (clone $logs)
            ->select('feature_key', 'feature_label', 'feature_category',
                DB::raw('COUNT(*) as uses'),
                DB::raw('MAX(created_at) as last_used'),
                DB::raw('ROUND(AVG(duration_ms)) as avg_ms')
            )
            ->groupBy('feature_key', 'feature_label', 'feature_category')
            ->orderByDesc('uses')
            ->get();

        $byCategory = (clone $logs)
            ->select('feature_category', DB::raw('COUNT(*) as uses'))
            ->groupBy('feature_category')
            ->orderByDesc('uses')
            ->get();

        $dailyActivity = (clone $logs)
            ->select('usage_date', DB::raw('COUNT(*) as uses'))
            ->groupBy('usage_date')
            ->orderBy('usage_date')
            ->get()
            ->keyBy(fn($r) => $r->usage_date->format('Y-m-d'));

        $actDates = [];
        $actUses  = [];
        for ($i = (int)$period - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $actDates[] = now()->subDays($i)->format('d M');
            $actUses[]  = $dailyActivity[$d]->uses ?? 0;
        }

        $recentLogs = FeatureUsageLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('admin.client-feature-analytics.user-detail', compact(
            'user', 'period',
            'totalUses', 'featuresUsed', 'lastActive',
            'byFeature', 'byCategory',
            'actDates', 'actUses',
            'recentLogs'
        ));
    }

    public function featureDetail(Request $request, string $featureKey)
    {
        $period = $request->get('period', '30');
        $from   = now()->subDays((int)$period)->startOfDay();

        $logs = FeatureUsageLog::where('feature_key', $featureKey)
            ->where('created_at', '>=', $from);

        $info        = (clone $logs)->first();
        $totalUses   = (clone $logs)->count();
        $uniqueUsers = (clone $logs)->distinct('user_id')->count('user_id');
        $avgMs       = (int)((clone $logs)->avg('duration_ms') ?? 0);
        $successRate = $totalUses > 0
            ? round((clone $logs)->where('success', true)->count() / $totalUses * 100, 1)
            : 0;

        $dailyTrend = (clone $logs)
            ->select('usage_date', DB::raw('COUNT(*) as uses'), DB::raw('COUNT(DISTINCT user_id) as users'))
            ->groupBy('usage_date')
            ->orderBy('usage_date')
            ->get()
            ->keyBy(fn($r) => $r->usage_date->format('Y-m-d'));

        $trendDates = [];
        $trendUses  = [];
        $trendUsers = [];
        for ($i = (int)$period - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $trendDates[] = now()->subDays($i)->format('d M');
            $trendUses[]  = $dailyTrend[$d]->uses ?? 0;
            $trendUsers[] = $dailyTrend[$d]->users ?? 0;
        }

        $byPackage = (clone $logs)
            ->select('subscription_package', DB::raw('COUNT(*) as uses'))
            ->groupBy('subscription_package')
            ->orderByDesc('uses')
            ->get();

        $topUsers = (clone $logs)
            ->select('user_id', DB::raw('COUNT(*) as uses'), DB::raw('MAX(created_at) as last_used'))
            ->groupBy('user_id')
            ->orderByDesc('uses')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();

        return view('admin.client-feature-analytics.feature-detail', compact(
            'featureKey', 'info', 'period',
            'totalUses', 'uniqueUsers', 'avgMs', 'successRate',
            'trendDates', 'trendUses', 'trendUsers',
            'byPackage', 'topUsers'
        ));
    }

    // ── Private: generate optimization insights ───────────────────────────────
    private function generateInsights($topFeatures, $byCategory, $underused, $activeUsers, $totalUsage, $period): array
    {
        $insights = [];

        // Most popular feature
        if ($topFeatures->isNotEmpty()) {
            $top = $topFeatures->first();
            $insights[] = [
                'type'  => 'success',
                'icon'  => '🏆',
                'title' => 'Fitur Terpopuler',
                'body'  => "{$top->feature_label} digunakan {$top->total_uses}x oleh {$top->unique_users} user ({$top->usage_pct}% dari total aktivitas). Pertimbangkan untuk menonjolkan fitur ini di onboarding.",
            ];
        }

        // Low success rate features
        $lowSuccess = $topFeatures->filter(fn($f) => $f->success_rate < 80 && $f->total_uses > 5);
        foreach ($lowSuccess->take(2) as $f) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => '⚠️',
                'title' => 'Success Rate Rendah',
                'body'  => "{$f->feature_label} memiliki success rate {$f->success_rate}% dari {$f->total_uses} penggunaan. Perlu investigasi error atau UX improvement.",
            ];
        }

        // Slow features
        $slowFeatures = $topFeatures->filter(fn($f) => $f->avg_ms > 3000 && $f->total_uses > 3);
        foreach ($slowFeatures->take(2) as $f) {
            $insights[] = [
                'type'  => 'warning',
                'icon'  => '🐢',
                'title' => 'Performa Lambat',
                'body'  => "{$f->feature_label} rata-rata {$f->avg_ms}ms. Response time di atas 3 detik dapat menurunkan engagement. Pertimbangkan optimasi query atau caching.",
            ];
        }

        // Underused features
        if ($underused->count() > 0) {
            $sample = $underused->take(3)->map(fn($v) => $v['label'])->implode(', ');
            $insights[] = [
                'type'  => 'info',
                'icon'  => '💡',
                'title' => 'Fitur Belum Digunakan',
                'body'  => "{$underused->count()} fitur belum ada aktivitas dalam {$period} hari terakhir: {$sample}. Pertimbangkan promosi in-app atau tutorial.",
            ];
        }

        // Dominant category
        if ($byCategory->isNotEmpty()) {
            $topCat = $byCategory->first();
            $insights[] = [
                'type'  => 'info',
                'icon'  => '📊',
                'title' => 'Kategori Dominan',
                'body'  => "Kategori '{$topCat->feature_category}' mendominasi dengan {$topCat->total_uses} penggunaan oleh {$topCat->unique_users} user. Fokuskan pengembangan di area ini.",
            ];
        }

        // User engagement
        if ($activeUsers > 0 && $totalUsage > 0) {
            $avgPerUser = round($totalUsage / $activeUsers, 1);
            $level = $avgPerUser >= 20 ? 'tinggi' : ($avgPerUser >= 8 ? 'sedang' : 'rendah');
            $insights[] = [
                'type'  => $avgPerUser >= 8 ? 'success' : 'warning',
                'icon'  => '👥',
                'title' => 'Engagement User',
                'body'  => "Rata-rata {$avgPerUser} aksi per user aktif dalam {$period} hari. Engagement {$level}." . ($avgPerUser < 8 ? ' Pertimbangkan email campaign atau notifikasi untuk re-engagement.' : ''),
            ];
        }

        return $insights;
    }
}
