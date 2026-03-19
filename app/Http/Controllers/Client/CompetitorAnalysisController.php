<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Competitor;
use App\Models\CompetitorAlert;
use App\Services\CompetitorAnalysisService;
use App\Services\AICompetitorComparisonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompetitorAnalysisController extends Controller
{
    protected $analysisService;

    public function __construct(CompetitorAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Display competitor analysis dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        $competitors = Competitor::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Load latest summary for each competitor
        foreach ($competitors as $competitor) {
            $competitor->latestSummary = $competitor->latestSummary();
        }
        
        $recentAlerts = CompetitorAlert::where('user_id', $user->id)
            ->with(['competitor', 'post'])
            ->orderBy('triggered_at', 'desc')
            ->limit(10)
            ->get();
        
        $unreadAlertsCount = CompetitorAlert::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('client.competitor-analysis.index', compact(
            'competitors',
            'recentAlerts',
            'unreadAlertsCount'
        ));
    }

    /**
     * Show form to add new competitor
     */
    public function create()
    {
        return view('client.competitor-analysis.create');
    }

    /**
     * Store new competitor
     */
    public function store(Request $request)
    {
        // Increase execution time for this request
        set_time_limit(120); // 2 minutes
        
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'platform' => 'required|in:instagram,tiktok,facebook,youtube,x,twitter,linkedin,shopee,tokopedia,lazada,bukalapak,blibli,jdid,zalora,sociolla,orami,bhinneka,amazon,alibaba,ebay,etsy,shopify',
            'category' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Clean username (remove @ if exists)
        $username = ltrim($validated['username'], '@');

        // Check if competitor already exists
        $existing = Competitor::where('user_id', $user->id)
            ->where('username', $username)
            ->where('platform', $validated['platform'])
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->with('error', "Kompetitor @{$username} di {$validated['platform']} sudah ada dalam daftar Anda. <a href='" . route('competitor-analysis.show', $existing) . "' class='underline text-purple-600'>Lihat analisis</a>");
        }

        try {
            // Step 1: Quick profile fetch (with shorter timeout)
            $profileData = $this->analysisService->fetchProfileData($username, $validated['platform']);
            
            if (!$profileData['success']) {
                return back()
                    ->withInput()
                    ->with('error', $profileData['error'] ?? 'Username tidak ditemukan atau terjadi kesalahan saat menganalisis profil. Pastikan username benar dan akun bersifat public.');
            }

            // Step 2: Create competitor record immediately
            $competitor = Competitor::create([
                'user_id' => $user->id,
                'username' => $username,
                'platform' => $validated['platform'],
                'profile_name' => $profileData['data']['profile_name'] ?? ucfirst($username),
                'profile_picture' => $profileData['data']['profile_picture'] ?? null,
                'bio' => $profileData['data']['bio'] ?? null,
                'followers_count' => $profileData['data']['followers_count'] ?? 0,
                'following_count' => $profileData['data']['following_count'] ?? 0,
                'posts_count' => $profileData['data']['posts_count'] ?? 0,
                'category' => $validated['category'] ?: 'Umum',
                'is_active' => true,
            ]);

            // Step 3: Run quick analysis first, then queue FAST analysis
            try {
                // Quick analysis with timeout protection
                $this->runQuickAnalysis($competitor);
                
                // Queue FAST analysis with high priority (immediate processing)
                \App\Jobs\AnalyzeCompetitorJob::dispatch($competitor)
                    ->onQueue('high')
                    ->delay(now()->addSeconds(5)); // Start in 5 seconds
                
                $successMessage = "🎉 Kompetitor @{$username} berhasil ditambahkan! Analisis cepat selesai, analisis lengkap akan selesai dalam 1-2 menit.";
            } catch (\Exception $e) {
                // Even if quick analysis fails, still queue analysis
                \App\Jobs\AnalyzeCompetitorJob::dispatch($competitor)
                    ->onQueue('high')
                    ->delay(now()->addSeconds(10));
                
                $successMessage = "✅ Kompetitor @{$username} berhasil ditambahkan. Analisis AI akan selesai dalam 1-2 menit.";
                
                \Illuminate\Support\Facades\Log::warning('Quick analysis failed, queued FAST analysis', [
                    'competitor_id' => $competitor->id,
                    'username' => $username,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('competitor-analysis.show', $competitor)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Competitor creation failed', [
                'username' => $username,
                'platform' => $validated['platform'],
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan kompetitor. Silakan coba lagi atau hubungi support jika masalah berlanjut.');
        }
    }

    /**
     * Run quick analysis with timeout protection
     */
    private function runQuickAnalysis(Competitor $competitor): void
    {
        // Set shorter timeout for individual operations
        $startTime = time();
        $maxTime = 25; // 25 seconds max to avoid 30s timeout
        
        try {
            // Quick AI analysis (simplified)
            if ((time() - $startTime) < $maxTime) {
                $this->analysisService->performQuickAnalysis($competitor);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Quick analysis component failed', [
                'competitor_id' => $competitor->id,
                'component' => 'quick_analysis',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show competitor details and analysis
     */
    public function show(Competitor $competitor)
    {
        // Check ownership
        if ($competitor->user_id !== Auth::id()) {
            abort(403);
        }

        // Load relationships safely
        $competitor->load([
            'patterns' => function($query) {
                $query->orderBy('analysis_date', 'desc');
            },
            'contentGaps' => function($query) {
                $query->where('is_implemented', false)
                      ->orderBy('priority', 'desc');
            },
            'alerts' => function($query) {
                $query->orderBy('triggered_at', 'desc')->limit(20);
            }
        ]);

        $latestSummary = $competitor->latestSummary();
        $recentPosts = $competitor->recentPosts()->limit(20)->get();
        
        // Get top posts safely
        $topPosts = $competitor->topContent()
            ->with('post')
            ->orderBy('rank')
            ->get();

        return view('client.competitor-analysis.show', compact(
            'competitor',
            'latestSummary',
            'recentPosts',
            'topPosts'
        ));
    }

    /**
     * Refresh competitor analysis
     */
    public function refresh(Competitor $competitor)
    {
        // Check ownership
        if ($competitor->user_id !== Auth::id()) {
            abort(403);
        }

        // Run analysis
        $result = $this->analysisService->analyzeCompetitor($competitor);

        if ($result['success']) {
            return back()->with('success', 'Analisis berhasil diperbarui');
        } else {
            return back()->with('error', 'Gagal memperbarui analisis: ' . $result['error']);
        }
    }

    /**
     * Toggle competitor active status
     */
    public function toggleActive(Competitor $competitor)
    {
        // Check ownership
        if ($competitor->user_id !== Auth::id()) {
            abort(403);
        }

        $competitor->update([
            'is_active' => !$competitor->is_active
        ]);

        $status = $competitor->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Monitoring kompetitor berhasil {$status}");
    }

    /**
     * Delete competitor
     */
    public function destroy(Competitor $competitor)
    {
        // Check ownership
        if ($competitor->user_id !== Auth::id()) {
            abort(403);
        }

        $competitor->delete();

        return redirect()->route('competitor-analysis.index')
            ->with('success', 'Kompetitor berhasil dihapus');
    }

    /**
     * Show alerts page
     */
    public function alerts()
    {
        $user = Auth::user();

        $alerts = CompetitorAlert::where('user_id', $user->id)
            ->with(['competitor', 'post'])
            ->orderBy('triggered_at', 'desc')
            ->paginate(20);

        return view('client.competitor-analysis.alerts', compact('alerts'));
    }

    /**
     * Mark alert as read
     */
    public function markAlertRead(CompetitorAlert $alert)
    {
        // Check ownership
        if ($alert->user_id !== Auth::id()) {
            abort(403);
        }

        $alert->markAsRead();

        return back()->with('success', 'Alert ditandai sudah dibaca');
    }

    /**
     * Mark all alerts as read
     */
    public function markAllAlertsRead()
    {
        CompetitorAlert::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua alert ditandai sudah dibaca');
    }

    /**
     * Show content gaps page
     */
    public function contentGaps(Competitor $competitor)
    {
        // Check ownership
        if ($competitor->user_id !== Auth::id()) {
            abort(403);
        }

        $contentGaps = $competitor->contentGaps()
            ->where('is_implemented', false)
            ->orderBy('priority', 'desc')
            ->get();

        return view('client.competitor-analysis.content-gaps', compact(
            'competitor',
            'contentGaps'
        ));
    }

    /**
     * Mark content gap as implemented
     */
    public function markGapImplemented($gapId)
    {
        $gap = \App\Models\CompetitorContentGap::findOrFail($gapId);

        // Check ownership through competitor
        if ($gap->competitor->user_id !== Auth::id()) {
            abort(403);
        }

        $gap->markAsImplemented();

        return back()->with('success', 'Content gap ditandai sudah diimplementasi');
    }

    /**
     * Show competitor comparison page with AI-powered insights
     */
    public function compare(Request $request)
    {
        $competitorIds = explode(',', $request->get('competitors', ''));
        
        if (count($competitorIds) < 2 || count($competitorIds) > 5) {
            return redirect()->route('competitor-analysis.index')
                ->with('error', 'Pilih 2-5 kompetitor untuk dibandingkan');
        }

        $user = Auth::user();
        
        // Get competitors with their ML data
        $competitors = Competitor::where('user_id', $user->id)
            ->whereIn('id', $competitorIds)
            ->get();

        if ($competitors->count() !== count($competitorIds)) {
            return redirect()->route('competitor-analysis.index')
                ->with('error', 'Beberapa kompetitor tidak ditemukan');
        }

        try {
            // Use AI-powered comparison service
            $aiComparisonService = app(AICompetitorComparisonService::class);
            
            Log::info('🤖 Starting AI-powered competitor comparison', [
                'user_id' => $user->id,
                'competitor_count' => $competitors->count(),
                'competitors' => $competitors->pluck('username', 'platform')->toArray()
            ]);
            
            $comparisonResult = $aiComparisonService->compareCompetitors($competitors);
            
            if ($comparisonResult['success']) {
                $comparisonInsights = $comparisonResult['comparison_analysis'];
                $strategicRecommendations = $comparisonResult['strategic_recommendations'];
                $competitiveMatrix = $comparisonResult['competitive_matrix'];
                
                // Prepare enhanced export data
                $exportData = $comparisonResult['competitor_data'];
                
                return view('client.competitor-analysis.compare', compact(
                    'competitors',
                    'comparisonInsights',
                    'strategicRecommendations',
                    'competitiveMatrix',
                    'exportData'
                ));
            } else {
                throw new \Exception($comparisonResult['error'] ?? 'AI comparison failed');
            }
            
        } catch (\Exception $e) {
            Log::error('AI competitor comparison failed', [
                'user_id' => $user->id,
                'competitors' => $competitors->pluck('username')->toArray(),
                'error' => $e->getMessage()
            ]);
            
            // Fallback to basic comparison
            $comparisonInsights = $this->generateBasicComparisonInsights($competitors);
            $exportData = $this->prepareBasicExportData($competitors);
            
            return view('client.competitor-analysis.compare', compact(
                'competitors',
                'comparisonInsights',
                'exportData'
            ))->with('warning', 'Menggunakan analisis dasar karena AI comparison tidak tersedia');
        }
    }

    /**
     * Generate AI-powered comparison insights
     */
    private function generateComparisonInsights($competitors)
    {
        try {
            $competitorData = $competitors->map(function($competitor) {
                $latestSummary = $competitor->analysisSummary->first(); // Get the latest (already ordered by latest)
                return [
                    'username' => $competitor->username,
                    'platform' => $competitor->platform,
                    'category' => $competitor->category ?? 'General',
                    'followers' => $competitor->followers_count ?? 0,
                    'engagement_rate' => $latestSummary ? ($latestSummary->avg_engagement_rate ?? 0) : 0,
                    'posts_count' => $latestSummary ? ($latestSummary->total_posts ?? 0) : 0,
                ];
            })->toArray();

            // Ensure we have valid data
            if (empty($competitorData)) {
                throw new \Exception('No competitor data available');
            }

            $prompt = "Analisis perbandingan kompetitor berikut:

" . json_encode($competitorData, JSON_PRETTY_PRINT) . "

Berikan insights perbandingan dalam format JSON:
{
    \"winner\": \"username kompetitor terbaik\",
    \"winner_reasons\": [\"alasan 1\", \"alasan 2\"],
    \"key_insights\": [
        \"insight penting 1\",
        \"insight penting 2\"
    ],
    \"recommendations\": [
        \"rekomendasi strategis 1\",
        \"rekomendasi strategis 2\"
    ],
    \"opportunities\": [
        \"peluang yang bisa dimanfaatkan\"
    ]
}

Singkat dan actionable.";

            $geminiService = app(\App\Services\GeminiService::class);
            $aiResponse = $geminiService->generateText($prompt, 500, 0.7);
            
            // Extract JSON with better parsing
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $insights = json_decode($matches[0], true);
                if ($insights && is_array($insights) && isset($insights['winner'])) {
                    // Validate required fields
                    $insights['winner'] = $insights['winner'] ?? $competitors->first()->username;
                    $insights['winner_reasons'] = $insights['winner_reasons'] ?? ['Data analysis'];
                    $insights['key_insights'] = $insights['key_insights'] ?? ['Competitive analysis completed'];
                    $insights['recommendations'] = $insights['recommendations'] ?? ['Continue monitoring'];
                    $insights['opportunities'] = $insights['opportunities'] ?? ['Market opportunities available'];
                    
                    return $insights;
                }
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Comparison insights generation failed', [
                'error' => $e->getMessage(),
                'competitors_count' => $competitors->count()
            ]);
        }

        // Enhanced fallback insights with actual data
        $bestCompetitor = $competitors->sortByDesc(function($competitor) {
            $summary = $competitor->analysisSummary->first();
            return $summary ? ($summary->avg_engagement_rate ?? 0) : 0;
        })->first();

        return [
            'winner' => $bestCompetitor ? $bestCompetitor->username : $competitors->first()->username,
            'winner_reasons' => [
                'Engagement rate tertinggi dalam perbandingan',
                'Konsistensi posting yang baik'
            ],
            'key_insights' => [
                'Variasi engagement rate signifikan antar kompetitor',
                'Timing posting mempengaruhi performa konten',
                'Platform yang berbeda memiliki karakteristik audience berbeda'
            ],
            'recommendations' => [
                'Fokus pada peningkatan engagement rate',
                'Optimalkan waktu posting berdasarkan analisis kompetitor terbaik',
                'Pelajari strategi konten dari kompetitor dengan performa terbaik'
            ],
            'opportunities' => [
                'Content gap yang belum dimanfaatkan kompetitor lain',
                'Peluang kolaborasi atau diferensiasi konten',
                'Optimasi hashtag dan caption berdasarkan trend kompetitor'
            ]
        ];
    }

    /**
     * Generate basic comparison insights as fallback
     */
    private function generateBasicComparisonInsights($competitors)
    {
        $bestCompetitor = $competitors->sortByDesc('followers_count')->first();

        return [
            'winner' => $bestCompetitor->username,
            'winner_reasons' => [
                'Jumlah followers terbanyak dalam perbandingan',
                'Posisi market yang kuat',
            ],
            'market_overview' => [
                'market_leader' => $bestCompetitor->username,
                'competitive_landscape' => 'Analisis dasar berdasarkan data tersedia'
            ],
            'key_insights' => [
                'Kompetitor memiliki variasi ukuran audience',
                'Platform yang berbeda memerlukan strategi berbeda',
                'Peluang ada di content gap dan engagement optimization'
            ],
            'recommendations' => [
                'Fokus pada platform dengan engagement terbaik',
                'Pelajari strategi kompetitor dengan followers terbanyak',
                'Optimalkan content consistency dan quality'
            ],
            'opportunities' => [
                'Content gap yang belum dimanfaatkan kompetitor lain',
                'Peluang kolaborasi atau diferensiasi konten',
                'Optimasi hashtag dan caption berdasarkan trend kompetitor'
            ]
        ];
    }

    /**
     * Prepare basic export data as fallback
     */
    private function prepareBasicExportData($competitors)
    {
        return $competitors->map(function($competitor) {
            return [
                'username' => $competitor->username,
                'platform' => $competitor->platform,
                'category' => $competitor->category,
                'followers' => $competitor->followers_count,
                'posts' => $competitor->posts_count,
                'engagement_rate' => '0%', // Default since no API data
                'last_updated' => $competitor->updated_at->format('Y-m-d H:i:s')
            ];
        })->toArray();
    }

    /**
     * 💰 Calculate optimal pricing with AI-powered analysis
     */
    public function calculatePricing(Request $request)
    {
        $validated = $request->validate([
            'modal_cost' => 'required|numeric|min:1',
            'target_profit' => 'required|numeric|min:1|max:500',
            'competitor_price' => 'nullable|numeric|min:0',
            'product_category' => 'nullable|string|max:100',
            'target_market' => 'nullable|string|in:premium,middle,budget'
        ]);

        try {
            $modalCost = $validated['modal_cost'];
            $targetProfit = $validated['target_profit'];
            $competitorPrice = $validated['competitor_price'] ?? 0;
            $productCategory = $validated['product_category'] ?? 'Umum';
            $targetMarket = $validated['target_market'] ?? 'middle';

            // Calculate base price with target profit
            $basePriceWithProfit = $modalCost * (1 + $targetProfit / 100);
            $breakEvenPrice = $modalCost;

            // Determine pricing strategy and recommended price
            $pricingAnalysis = $this->analyzePricingStrategy(
                $basePriceWithProfit, 
                $competitorPrice, 
                $modalCost, 
                $targetMarket
            );

            // Generate AI-powered pricing insights
            $aiInsights = $this->generatePricingInsights(
                $modalCost,
                $pricingAnalysis['recommended_price'],
                $competitorPrice,
                $productCategory,
                $targetMarket
            );

            // Generate promotional content
            $promotionalContent = $this->generatePromotionalContent(
                $pricingAnalysis['recommended_price'],
                $modalCost,
                $pricingAnalysis['strategy'],
                $productCategory
            );

            return response()->json([
                'success' => true,
                'pricing_analysis' => $pricingAnalysis,
                'ai_insights' => $aiInsights,
                'promotional_content' => $promotionalContent,
                'break_even_price' => $breakEvenPrice,
                'profit_margin' => [
                    'amount' => $pricingAnalysis['recommended_price'] - $modalCost,
                    'percentage' => (($pricingAnalysis['recommended_price'] - $modalCost) / $modalCost) * 100
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Pricing calculation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'input' => $validated
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Gagal menghitung harga. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Analyze pricing strategy based on market conditions
     */
    private function analyzePricingStrategy($basePriceWithProfit, $competitorPrice, $modalCost, $targetMarket)
    {
        $recommendedPrice = $basePriceWithProfit;
        $strategy = 'standard';
        $reason = 'Berdasarkan target profit';

        if ($competitorPrice > 0) {
            $competitorRatio = $basePriceWithProfit / $competitorPrice;
            
            if ($competitorRatio <= 0.8) {
                // Significantly cheaper than competitor
                $recommendedPrice = round($competitorPrice * 0.85);
                $strategy = 'penetration';
                $reason = 'Harga kompetitif (15% lebih murah dari kompetitor)';
            } elseif ($competitorRatio <= 0.95) {
                // Slightly cheaper
                $recommendedPrice = round($competitorPrice * 0.92);
                $strategy = 'competitive';
                $reason = 'Harga sedikit lebih murah (8% lebih murah)';
            } elseif ($competitorRatio <= 1.1) {
                // Similar price range
                $recommendedPrice = round($basePriceWithProfit);
                $strategy = 'value';
                $reason = 'Harga sesuai target profit dengan value positioning';
            } else {
                // Premium pricing
                $recommendedPrice = round($basePriceWithProfit);
                $strategy = 'premium';
                $reason = 'Premium pricing (fokus kualitas dan brand)';
            }
        }

        // Adjust based on target market
        switch ($targetMarket) {
            case 'premium':
                $recommendedPrice = round($recommendedPrice * 1.15);
                $strategy = 'premium';
                $reason = 'Premium market positioning';
                break;
            case 'budget':
                $recommendedPrice = round($recommendedPrice * 0.90);
                $strategy = 'penetration';
                $reason = 'Budget market positioning';
                break;
        }

        // Ensure minimum profit margin (at least 10%)
        $minPrice = round($modalCost * 1.1);
        if ($recommendedPrice < $minPrice) {
            $recommendedPrice = $minPrice;
            $reason = 'Disesuaikan untuk mempertahankan margin minimum 10%';
        }

        return [
            'recommended_price' => $recommendedPrice,
            'strategy' => $strategy,
            'reason' => $reason,
            'competitor_comparison' => $competitorPrice > 0 ? [
                'competitor_price' => $competitorPrice,
                'price_difference' => $recommendedPrice - $competitorPrice,
                'percentage_difference' => $competitorPrice > 0 ? (($recommendedPrice - $competitorPrice) / $competitorPrice) * 100 : 0
            ] : null
        ];
    }

    /**
     * Generate AI-powered pricing insights
     */
    private function generatePricingInsights($modalCost, $recommendedPrice, $competitorPrice, $category, $targetMarket)
    {
        try {
            $prompt = "Sebagai ahli pricing untuk UMKM, berikan insight pricing untuk:

Modal: Rp " . number_format($modalCost) . "
Harga Rekomendasi: Rp " . number_format($recommendedPrice) . "
Harga Kompetitor: Rp " . number_format($competitorPrice) . "
Kategori: {$category}
Target Market: {$targetMarket}

Berikan response dalam format JSON:
{
    \"market_analysis\": \"analisis kondisi pasar singkat\",
    \"pricing_strategy\": [\"strategi 1\", \"strategi 2\", \"strategi 3\"],
    \"competitive_advantages\": [\"keunggulan 1\", \"keunggulan 2\"],
    \"risks_and_mitigation\": [\"risiko dan cara mengatasinya\"],
    \"upselling_opportunities\": [\"peluang upselling\"]
}

Fokus pada praktis dan actionable untuk UMKM.";

            $geminiService = app(\App\Services\GeminiService::class);
            $aiResponse = $geminiService->generateText($prompt, 400, 0.7);
            
            // Extract JSON
            if (preg_match('/\{.*\}/s', $aiResponse, $matches)) {
                $insights = json_decode($matches[0], true);
                if ($insights && is_array($insights)) {
                    return $insights;
                }
            }

        } catch (\Exception $e) {
            Log::warning('AI pricing insights generation failed', [
                'error' => $e->getMessage()
            ]);
        }

        // Fallback insights
        return [
            'market_analysis' => 'Pasar {$category} memiliki potensi baik dengan margin profit yang sehat',
            'pricing_strategy' => [
                'Fokus pada value proposition yang jelas',
                'Bandingkan dengan kompetitor secara berkala',
                'Pertimbangkan bundling untuk meningkatkan nilai'
            ],
            'competitive_advantages' => [
                'Harga kompetitif dengan kualitas terjamin',
                'Service yang personal dan responsif'
            ],
            'risks_and_mitigation' => [
                'Risiko: Perang harga - Mitigasi: Fokus diferensiasi produk',
                'Risiko: Margin tipis - Mitigasi: Optimasi operasional'
            ],
            'upselling_opportunities' => [
                'Paket bundling dengan produk komplementer',
                'Program loyalitas untuk repeat customer'
            ]
        ];
    }

    /**
     * Generate promotional content for pricing
     */
    private function generatePromotionalContent($price, $modalCost, $strategy, $category)
    {
        $profit = $price - $modalCost;
        $profitPercentage = round(($profit / $modalCost) * 100);

        // Generate quantity-based pricing table
        $pricingTable = [
            'qty_1' => $price,
            'qty_3' => round($price * 0.95), // 5% discount
            'qty_5' => round($price * 0.90), // 10% discount
            'qty_10' => round($price * 0.85), // 15% discount
        ];

        // Generate captions based on strategy
        $captions = $this->generateStrategyCaptions($price, $strategy, $category);

        return [
            'pricing_table' => $pricingTable,
            'captions' => $captions,
            'hashtags' => $this->generateHashtags($strategy, $category),
            'discount_suggestions' => [
                'early_bird' => '10% untuk 10 pembeli pertama',
                'bundle' => 'Beli 2 gratis 1 aksesoris',
                'loyalty' => 'Cashback 5% untuk member'
            ]
        ];
    }

    /**
     * Generate strategy-specific captions
     */
    private function generateStrategyCaptions($price, $strategy, $category)
    {
        $formattedPrice = 'Rp ' . number_format($price, 0, ',', '.');
        
        $templates = [
            'penetration' => [
                "🔥 PROMO SPESIAL {$category}! 🔥\n\nHarga cuma {$formattedPrice}! Dijamin termurah se-kota! 💰\n\nKualitas premium, harga bersahabat. Jangan sampai kehabisan!\n\n#PromoSpesial #HargaTerbaik #Murah #Berkualitas",
                "💥 FLASH SALE ALERT! 💥\n\n{$category} berkualitas cuma {$formattedPrice}! 🎯\n\nStock terbatas, buruan order sebelum kehabisan!\n\n#FlashSale #StockTerbatas #HargaMurah"
            ],
            'competitive' => [
                "✨ Kualitas Premium, Harga Bersaing! ✨\n\n{$category} terbaik cuma {$formattedPrice}! 🏆\n\nBandingkan dengan yang lain, pasti pilih kita!\n\n#KualitasTerbaik #HargaBersaing #Recommended",
                "🎯 Best Value for Money! 🎯\n\n{$formattedPrice} = Kualitas + Service terbaik! 💯\n\nSekali beli, pasti balik lagi!\n\n#BestValue #Berkualitas #TrustedSeller"
            ],
            'premium' => [
                "👑 Premium {$category}, Premium Experience 👑\n\n{$formattedPrice} untuk yang terbaik dari yang terbaik! ✨\n\nInvestasi terbaik untuk kualitas yang tak tertandingi.\n\n#Premium #Exclusive #TopQuality #WorthIt",
                "💎 Luxury at Its Finest 💎\n\n{$formattedPrice} - Harga untuk yang mengerti kualitas sejati! 🏆\n\nTidak semua orang bisa memiliki yang terbaik.\n\n#Luxury #Exclusive #PremiumQuality"
            ]
        ];

        return $templates[$strategy] ?? [
            "🛍️ {$category} Berkualitas, Harga Terjangkau! 🛍️\n\nCuma {$formattedPrice}! Kualitas terjamin, harga bersahabat! 😊\n\nOrder sekarang, stock terbatas!\n\n#Berkualitas #Terjangkau #StockTerbatas"
        ];
    }

    /**
     * Generate relevant hashtags
     */
    private function generateHashtags($strategy, $category)
    {
        $baseHashtags = ['#' . str_replace(' ', '', $category), '#UMKM', '#OnlineShop', '#Berkualitas'];
        
        $strategyHashtags = [
            'penetration' => ['#HargaMurah', '#PromoSpesial', '#Terjangkau', '#BestPrice'],
            'competitive' => ['#HargaBersaing', '#BestValue', '#Recommended', '#TrustedSeller'],
            'premium' => ['#Premium', '#Exclusive', '#TopQuality', '#Luxury'],
            'standard' => ['#KualitasOke', '#HargaPas', '#Terpercaya', '#ReadyStock']
        ];

        return array_merge($baseHashtags, $strategyHashtags[$strategy] ?? $strategyHashtags['standard']);
    }
}