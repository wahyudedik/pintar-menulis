<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CopywritingRequestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LegalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Legal pages
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('terms-of-service');
Route::get('/refund-policy', [LegalController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/contact', [LegalController::class, 'contact'])->name('contact');

// Dashboard with role-based routing
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public routes
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

// Article routes
Route::get('/articles', [\App\Http\Controllers\ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('articles.show');

// SEO Routes
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.xml');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/disconnect-google', [ProfileController::class, 'disconnectGoogle'])->name('profile.disconnect-google');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Client routes
    Route::middleware(['role:client'])->group(function () {
        // AI Generator
        Route::get('/ai-generator', [\App\Http\Controllers\Client\AIGeneratorController::class, 'index'])->name('ai.generator');
        
        // Keyword Research
        Route::get('/keyword-research', [\App\Http\Controllers\Client\KeywordResearchController::class, 'index'])->name('keyword-research.index');
        
        // Analytics
        Route::get('/analytics', [\App\Http\Controllers\Client\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::post('/analytics', [\App\Http\Controllers\Client\AnalyticsController::class, 'store'])->name('analytics.store');
        Route::get('/analytics/export-pdf', [\App\Http\Controllers\Client\AnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf');
        Route::get('/analytics/export-csv', [\App\Http\Controllers\Client\AnalyticsController::class, 'exportCsv'])->name('analytics.export.csv');
        Route::get('/analytics/{analytics}', [\App\Http\Controllers\Client\AnalyticsController::class, 'show'])->name('analytics.show');
        Route::put('/analytics/{analytics}', [\App\Http\Controllers\Client\AnalyticsController::class, 'update'])->name('analytics.update');
        Route::get('/analytics-insights', [\App\Http\Controllers\Client\AnalyticsController::class, 'insights'])->name('analytics.insights');
        
        // Brand Voice
        Route::get('/brand-voices', [\App\Http\Controllers\Client\BrandVoiceController::class, 'index'])->name('brand-voices.index');
        Route::post('/brand-voices', [\App\Http\Controllers\Client\BrandVoiceController::class, 'store'])->name('brand-voices.store');
        Route::delete('/brand-voices/{brandVoice}', [\App\Http\Controllers\Client\BrandVoiceController::class, 'destroy'])->name('brand-voices.destroy');
        
        // Caption History (for ML)
        Route::get('/caption-history', [\App\Http\Controllers\Client\CaptionHistoryController::class, 'index'])->name('caption-history.index');
        Route::get('/caption-history/{history}', [\App\Http\Controllers\Client\CaptionHistoryController::class, 'show'])->name('caption-history.show');
        Route::delete('/caption-history/{history}', [\App\Http\Controllers\Client\CaptionHistoryController::class, 'destroy'])->name('caption-history.destroy');
        Route::post('/caption-history/clear-all', [\App\Http\Controllers\Client\CaptionHistoryController::class, 'clearAll'])->name('caption-history.clear-all');
        
        // My Stats (Personal ML Insights)
        Route::get('/my-stats', [\App\Http\Controllers\Client\CaptionRatingController::class, 'myStats'])->name('my-stats');
        
        // Bulk Content Generator
        Route::get('/bulk-content', [\App\Http\Controllers\Client\BulkContentController::class, 'index'])->name('bulk-content.index');
        Route::get('/bulk-content/create', [\App\Http\Controllers\Client\BulkContentController::class, 'create'])->name('bulk-content.create');
        Route::post('/bulk-content/generate', [\App\Http\Controllers\Client\BulkContentController::class, 'generate'])->name('bulk-content.generate');
        Route::get('/bulk-content/{calendar}', [\App\Http\Controllers\Client\BulkContentController::class, 'show'])->name('bulk-content.show');
        Route::get('/bulk-content/{calendar}/export/{format}', [\App\Http\Controllers\Client\BulkContentController::class, 'export'])->name('bulk-content.export');
        Route::delete('/bulk-content/{calendar}', [\App\Http\Controllers\Client\BulkContentController::class, 'destroy'])->name('bulk-content.destroy');
        Route::post('/bulk-content/{calendar}/update/{dayNumber}', [\App\Http\Controllers\Client\BulkContentController::class, 'updateContent'])->name('bulk-content.update-content');
        
        // Feedback & Support
        Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback.index');
        Route::get('/feedback/create', [\App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
        Route::get('/feedback/{feedback}', [\App\Http\Controllers\FeedbackController::class, 'show'])->name('feedback.show');
        
        // Browse Operators & Request Order
        Route::get('/browse-operators', [\App\Http\Controllers\Client\OrderRequestController::class, 'index'])->name('browse.operators');
        Route::post('/request-order', [\App\Http\Controllers\Client\OrderRequestController::class, 'store'])->name('request.order');
        
        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create/{package}', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/revision', [OrderController::class, 'requestRevision'])->name('orders.revision');
        Route::post('/orders/{order}/rate', [OrderController::class, 'rate'])->name('orders.rate');
        Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/dispute', [OrderController::class, 'dispute'])->name('orders.dispute');
        
        // Payment
        Route::get('/payment/{order}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{order}/submit-proof', [\App\Http\Controllers\PaymentController::class, 'submitProof'])->name('payment.submit-proof');
        
        // Projects
        Route::resource('projects', ProjectController::class);
        
        // Copywriting Requests
        Route::get('/copywriting', [CopywritingRequestController::class, 'index'])->name('copywriting.index');
        Route::get('/copywriting/create/{order}', [CopywritingRequestController::class, 'create'])->name('copywriting.create');
        Route::post('/copywriting', [CopywritingRequestController::class, 'store'])->name('copywriting.store');
        Route::get('/copywriting/{copywriting}', [CopywritingRequestController::class, 'show'])->name('copywriting.show');
        Route::post('/copywriting/{copywriting}/revision', [CopywritingRequestController::class, 'requestRevision'])->name('copywriting.revision');
        Route::post('/copywriting/{copywriting}/rate', [CopywritingRequestController::class, 'rate'])->name('copywriting.rate');
    });
    
    // Operator routes
    Route::middleware(['role:operator,admin'])->prefix('operator')->name('operator.')->group(function () {
        // Order Queue & Management
        Route::get('/queue', [\App\Http\Controllers\Operator\OrderController::class, 'queue'])->name('queue');
        Route::post('/orders/{order}/accept', [\App\Http\Controllers\Operator\OrderController::class, 'accept'])->name('accept');
        Route::post('/orders/{order}/reject', [\App\Http\Controllers\Operator\OrderController::class, 'reject'])->name('reject');
        
        // Workspace
        Route::get('/workspace/{order}', [\App\Http\Controllers\Operator\OrderController::class, 'workspace'])->name('workspace');
        Route::post('/workspace/{order}/submit', [\App\Http\Controllers\Operator\OrderController::class, 'submit'])->name('submit');
        
        // Earnings
        Route::get('/earnings', [\App\Http\Controllers\Operator\OrderController::class, 'earnings'])->name('earnings');
        
        // Withdrawal
        Route::get('/withdrawal/create', [\App\Http\Controllers\Operator\WithdrawalController::class, 'create'])->name('withdrawal.create');
        Route::post('/withdrawal', [\App\Http\Controllers\Operator\WithdrawalController::class, 'store'])->name('withdrawal.store');
        Route::get('/withdrawal/history', [\App\Http\Controllers\Operator\WithdrawalController::class, 'history'])->name('withdrawal.history');
        
        // Profile
        Route::get('/profile/edit', [\App\Http\Controllers\Operator\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Operator\ProfileController::class, 'update'])->name('profile.update');
        
        // Legacy routes (keep for backward compatibility)
        Route::post('/copywriting/{copywriting}/assign', [CopywritingRequestController::class, 'assign'])->name('assign');
        Route::put('/copywriting/{copywriting}', [CopywritingRequestController::class, 'update'])->name('update');
    });
    
    // Guru routes
    Route::middleware(['role:guru,admin'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/training', [\App\Http\Controllers\Guru\MLTrainingController::class, 'index'])->name('training');
        Route::get('/training/{order}', [\App\Http\Controllers\Guru\MLTrainingController::class, 'show'])->name('training.show');
        Route::post('/training', [\App\Http\Controllers\Guru\MLTrainingController::class, 'store'])->name('training.store');
        Route::post('/training/caption', [\App\Http\Controllers\Guru\MLTrainingController::class, 'trainFromCaption'])->name('training.caption');
        Route::get('/training-history', [\App\Http\Controllers\Guru\MLTrainingController::class, 'history'])->name('training.history');
        Route::get('/analytics', [\App\Http\Controllers\Guru\MLTrainingController::class, 'analytics'])->name('analytics');
    });
    
    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verifyOperator'])->name('users.verify');
        Route::post('/users/{user}/unverify', [\App\Http\Controllers\Admin\UserController::class, 'unverifyOperator'])->name('users.unverify');
        
        // AI Usage Analytics
        Route::get('/ai-usage', [\App\Http\Controllers\Admin\AIUsageController::class, 'index'])->name('ai-usage.index');
        Route::get('/ai-usage/{user}', [\App\Http\Controllers\Admin\AIUsageController::class, 'show'])->name('ai-usage.show');
        Route::get('/ai-usage/{user}/stats', [\App\Http\Controllers\Admin\AIUsageController::class, 'userStats'])->name('ai-usage.stats');
        
        // ML Analytics (Machine Learning Insights)
        Route::get('/ml-analytics', [\App\Http\Controllers\Admin\MLAnalyticsController::class, 'index'])->name('ml-analytics.index');
        Route::get('/ml-analytics/export', [\App\Http\Controllers\Admin\MLAnalyticsController::class, 'exportTrainingData'])->name('ml-analytics.export');
        
        // Package Management
        Route::get('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('packages');
        Route::get('/packages/{package}/edit', [\App\Http\Controllers\Admin\PackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'update'])->name('packages.update');
        
        // Financial Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
        
        // Payment Settings
        Route::get('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('payment-settings');
        Route::post('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'store'])->name('payment-settings.store');
        Route::put('/payment-settings/{paymentSetting}', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'update'])->name('payment-settings.update');
        Route::delete('/payment-settings/{paymentSetting}', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'destroy'])->name('payment-settings.destroy');
        Route::post('/payment-settings/midtrans', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateMidtrans'])->name('payment-settings.midtrans-update');
        
        // Payment Verification
        Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments');
        Route::post('/payments/{payment}/verify', [\App\Http\Controllers\Admin\PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])->name('payments.reject');
        
        // Withdrawal Management
        Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals');
        Route::post('/withdrawals/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('/withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');
        Route::post('/withdrawals/{withdrawal}/complete', [\App\Http\Controllers\Admin\WithdrawalController::class, 'complete'])->name('withdrawals.complete');
        
        // Feedback Management
        Route::get('/feedback', [\App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedback');
        Route::get('/feedback/{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('feedback.show');
        Route::put('/feedback/{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'update'])->name('feedback.update');
        Route::delete('/feedback/{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'destroy'])->name('feedback.destroy');
        
        // Banner Information Management
        Route::get('/banner-information', [\App\Http\Controllers\Admin\BannerInformationController::class, 'index'])->name('banner-information.index');
        Route::put('/banner-information/{banner}', [\App\Http\Controllers\Admin\BannerInformationController::class, 'update'])->name('banner-information.update');
        
        // Ad Placements Management
        Route::get('/ad-placements', [\App\Http\Controllers\Admin\AdPlacementController::class, 'index'])->name('ad-placements.index');
        Route::put('/ad-placements/{placement}', [\App\Http\Controllers\Admin\AdPlacementController::class, 'update'])->name('ad-placements.update');
        Route::patch('/ad-placements/{placement}/toggle', [\App\Http\Controllers\Admin\AdPlacementController::class, 'toggle'])->name('ad-placements.toggle');
    });
});

// API Routes
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::post('/ai/generate', [\App\Http\Controllers\Client\AIGeneratorController::class, 'generate'])->name('api.ai.generate');
    Route::post('/ai/generate-image-caption', [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateImageCaption'])->name('api.ai.generate-image-caption');
    Route::get('/check-first-time', [\App\Http\Controllers\Client\AIGeneratorController::class, 'checkFirstTime'])->name('api.check-first-time');
    
    // 🤖 ML System API
    Route::get('/ml/status', [\App\Http\Controllers\MLSuggestionsController::class, 'getStatus'])->name('api.ml.status');
    Route::get('/ml/preview', [\App\Http\Controllers\MLSuggestionsController::class, 'getPreview'])->name('api.ml.preview');
    Route::get('/ml/weekly-trends', [\App\Http\Controllers\MLSuggestionsController::class, 'getWeeklyTrends'])->name('api.ml.weekly-trends');
    Route::post('/ml/refresh', [\App\Http\Controllers\MLSuggestionsController::class, 'refreshSuggestions'])->name('api.ml.refresh');
    Route::get('/ml/cache-stats', [\App\Http\Controllers\MLSuggestionsController::class, 'getCacheStats'])->name('api.ml.cache-stats');
    
    // 🔍 AI Analysis API
    Route::post('/analysis/sentiment', [\App\Http\Controllers\AIAnalysisController::class, 'analyzeSentiment'])->name('api.analysis.sentiment');
    Route::post('/analysis/image', [\App\Http\Controllers\AIAnalysisController::class, 'analyzeImage'])->name('api.analysis.image');
    Route::post('/analysis/score-caption', [\App\Http\Controllers\AIAnalysisController::class, 'scoreCaption'])->name('api.analysis.score-caption');
    Route::post('/analysis/recommendations', [\App\Http\Controllers\AIAnalysisController::class, 'getRecommendations'])->name('api.analysis.recommendations');
    Route::post('/analysis/campaign', [\App\Http\Controllers\AIAnalysisController::class, 'analyzeCampaign'])->name('api.analysis.campaign');
    Route::post('/analysis/article', [\App\Http\Controllers\AIAnalysisController::class, 'analyzeArticle'])->name('api.analysis.article');
    Route::get('/analysis/history', [\App\Http\Controllers\AIAnalysisController::class, 'getHistory'])->name('api.analysis.history');
    Route::get('/analysis/dashboard', [\App\Http\Controllers\AIAnalysisController::class, 'getDashboard'])->name('api.analysis.dashboard');
    
    // Keyword Research API
    Route::post('/keyword-research/search', [\App\Http\Controllers\Client\KeywordResearchController::class, 'search'])->name('api.keyword-research.search');
    Route::get('/keyword-research/history', [\App\Http\Controllers\Client\KeywordResearchController::class, 'history'])->name('api.keyword-research.history');
    
    // Caption Rating (Client)
    Route::post('/caption/{id}/rate', [\App\Http\Controllers\Client\CaptionRatingController::class, 'rate'])->name('api.caption.rate');
    
    Route::get('/notifications', function() {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response()->json(['notifications' => $notifications]);
    });
});

// Article API Routes (public)
Route::prefix('api/articles')->group(function () {
    Route::get('/', [\App\Http\Controllers\ArticleApiController::class, 'index'])->name('api.articles.index');
    Route::get('/today', [\App\Http\Controllers\ArticleApiController::class, 'today'])->name('api.articles.today');
    Route::get('/category/{category}', [\App\Http\Controllers\ArticleApiController::class, 'byCategory'])->name('api.articles.by-category');
    Route::get('/industry/{industry}', [\App\Http\Controllers\ArticleApiController::class, 'byIndustry'])->name('api.articles.by-industry');
    Route::get('/{slug}', [\App\Http\Controllers\ArticleApiController::class, 'show'])->name('api.articles.show');
});

// 🤖 AI Assistant API (Public - no auth required, with rate limiting)
Route::prefix('api')->middleware('throttle:30,1')->group(function () {
    Route::post('/assistant/chat', [\App\Http\Controllers\AIAssistantController::class, 'chat'])->name('api.assistant.chat');
    Route::get('/assistant/suggestions', [\App\Http\Controllers\AIAssistantController::class, 'getSuggestions'])->name('api.assistant.suggestions');
    Route::get('/assistant/tips', [\App\Http\Controllers\AIAssistantController::class, 'getTips'])->name('api.assistant.tips');
});

// Public API Routes (no auth required)
Route::prefix('api')->group(function () {
    Route::get('/banner/{type}', [\App\Http\Controllers\BannerInformationController::class, 'getByType']);
});

// Test route for debugging Gemini API
Route::get('/test-gemini', function() {
    try {
        $geminiService = app(\App\Services\GeminiService::class);
        
        $result = $geminiService->generateCopywriting([
            'type' => 'instagram_caption',
            'brief' => 'Produk kopi arabica premium dari Aceh dengan cita rasa yang khas',
            'tone' => 'casual',
            'platform' => 'instagram',
            'keywords' => 'kopi, arabica, premium'
        ]);
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'message' => 'Test berhasil!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware(['auth']);

// Test AI page
Route::get('/test-ai-page', function() {
    return view('test-ai');
})->middleware(['auth']);

require __DIR__.'/auth.php';

