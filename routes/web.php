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
    ->middleware(['auth'])
    ->name('dashboard');

// Public routes
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');
Route::get('/pricing', [\App\Http\Controllers\Client\SubscriptionController::class, 'pricing'])->name('pricing');

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
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Client routes
    Route::middleware(['role:client', 'verified'])->group(function () {
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
        
        // Image Caption (AI Vision)
        Route::get('/image-caption', [\App\Http\Controllers\Client\ImageCaptionController::class, 'index'])->name('image-caption.index');
        Route::get('/image-caption/create', [\App\Http\Controllers\Client\ImageCaptionController::class, 'create'])->name('image-caption.create');
        Route::post('/image-caption', [\App\Http\Controllers\Client\ImageCaptionController::class, 'store'])->name('image-caption.store');
        Route::get('/image-caption/{imageCaption}', [\App\Http\Controllers\Client\ImageCaptionController::class, 'show'])->name('image-caption.show');
        Route::delete('/image-caption/{imageCaption}', [\App\Http\Controllers\Client\ImageCaptionController::class, 'destroy'])->name('image-caption.destroy');

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
        
        // Template Marketplace
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/rate', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'rate'])->name('rate');
            Route::post('/{id}/favorite', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'toggleFavorite'])->name('favorite');
            Route::post('/{id}/use', [\App\Http\Controllers\Client\TemplateMarketplaceController::class, 'use'])->name('use');
            
            // Import/Export
            Route::get('/{id}/export', [\App\Http\Controllers\Client\TemplateImportExportController::class, 'exportSingle'])->name('export');
            Route::post('/export-multiple', [\App\Http\Controllers\Client\TemplateImportExportController::class, 'exportMultiple'])->name('export-multiple');
            Route::get('/export-all', [\App\Http\Controllers\Client\TemplateImportExportController::class, 'exportAll'])->name('export-all');
            Route::post('/import', [\App\Http\Controllers\Client\TemplateImportExportController::class, 'import'])->name('import');
            Route::post('/import-url', [\App\Http\Controllers\Client\TemplateImportExportController::class, 'importFromUrl'])->name('import-url');
        });

        // Subscription
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\SubscriptionController::class, 'index'])->name('index');
            Route::post('/trial/{package}', [\App\Http\Controllers\Client\SubscriptionController::class, 'startTrial'])->name('trial');
            Route::get('/checkout/{package}', [\App\Http\Controllers\Client\SubscriptionController::class, 'checkout'])->name('checkout');
            Route::post('/checkout/{package}', [\App\Http\Controllers\Client\SubscriptionController::class, 'processPayment'])->name('pay');
            Route::post('/cancel', [\App\Http\Controllers\Client\SubscriptionController::class, 'cancel'])->name('cancel');
        });
        
        // Competitor Analysis
        Route::prefix('competitor-analysis')->name('competitor-analysis.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'create'])->name('create');
            Route::get('/compare', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'compare'])->name('compare');
            Route::get('/alerts/list', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'alerts'])->name('alerts');
            Route::post('/', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'store'])->name('store');
            Route::post('/alerts/{alert}/read', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'markAlertRead'])->name('alert.read');
            Route::post('/alerts/read-all', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'markAllAlertsRead'])->name('alerts.read-all');
            Route::post('/content-gaps/{gap}/implement', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'markGapImplemented'])->name('gap.implement');
            Route::get('/{competitor}', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'show'])->name('show');
            Route::post('/{competitor}/refresh', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'refresh'])->name('refresh');
            Route::post('/{competitor}/toggle-active', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'toggleActive'])->name('toggle-active');
            Route::delete('/{competitor}', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'destroy'])->name('destroy');
            Route::get('/{competitor}/content-gaps', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'contentGaps'])->name('content-gaps');
        });
        
        // Browse Operators & Request Order
        Route::get('/browse-operators', [\App\Http\Controllers\Client\OrderRequestController::class, 'index'])->name('browse.operators');
        Route::post('/request-order', [\App\Http\Controllers\Client\OrderRequestController::class, 'store'])->name('request.order');

        // Referral
        Route::get('/referral', [\App\Http\Controllers\Client\ReferralController::class, 'index'])->name('client.referral.index');
        Route::get('/referral/withdraw', [\App\Http\Controllers\Client\ReferralController::class, 'withdrawCreate'])->name('client.referral.withdraw');
        Route::post('/referral/withdraw', [\App\Http\Controllers\Client\ReferralController::class, 'withdrawStore'])->name('client.referral.withdraw.store');
        
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
        
        // 👥 Project Collaboration Features
        Route::prefix('projects/{project}')->name('projects.')->group(function () {
            // Team Management
            Route::get('/team', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'index'])->name('collaboration.index');
            Route::post('/team/invite', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'inviteMember'])->name('collaboration.invite');
            Route::patch('/team/{member}/role', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'updateMemberRole'])->name('collaboration.update-role');
            Route::delete('/team/{member}', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'removeMember'])->name('collaboration.remove-member');
            Route::post('/leave', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'leaveProject'])->name('collaboration.leave');
            
            // Team Workspace
            Route::get('/workspace', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'workspace'])->name('collaboration.workspace');
            Route::get('/activity', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'activityFeed'])->name('collaboration.activity');
            
            // Content Management
            Route::get('/content', [\App\Http\Controllers\Client\ProjectContentController::class, 'index'])->name('content.index');
            Route::get('/content/create', [\App\Http\Controllers\Client\ProjectContentController::class, 'create'])->name('content.create');
            Route::post('/content', [\App\Http\Controllers\Client\ProjectContentController::class, 'store'])->name('content.store');
            Route::get('/content/{content}', [\App\Http\Controllers\Client\ProjectContentController::class, 'show'])->name('content.show');
            Route::get('/content/{content}/edit', [\App\Http\Controllers\Client\ProjectContentController::class, 'edit'])->name('content.edit');
            Route::put('/content/{content}', [\App\Http\Controllers\Client\ProjectContentController::class, 'update'])->name('content.update');
            Route::delete('/content/{content}', [\App\Http\Controllers\Client\ProjectContentController::class, 'destroy'])->name('content.destroy');
            
            // Content Workflow
            Route::post('/content/{content}/submit-review', [\App\Http\Controllers\Client\ProjectContentController::class, 'submitForReview'])->name('content.submit-review');
            Route::post('/content/{content}/approve', [\App\Http\Controllers\Client\ProjectContentController::class, 'approve'])->name('content.approve');
            Route::post('/content/{content}/reject', [\App\Http\Controllers\Client\ProjectContentController::class, 'reject'])->name('content.reject');
            
            // Comments & Feedback
            Route::post('/content/{content}/comments', [\App\Http\Controllers\Client\ProjectContentController::class, 'addComment'])->name('content.comments.store');
            Route::post('/content/{content}/comments/{comment}/resolve', [\App\Http\Controllers\Client\ProjectContentController::class, 'resolveComment'])->name('content.comments.resolve');
            
            // Version History
            Route::get('/content/{content}/versions', [\App\Http\Controllers\Client\ProjectContentController::class, 'versionHistory'])->name('content.versions');
            Route::post('/content/{content}/versions/{version}/restore', [\App\Http\Controllers\Client\ProjectContentController::class, 'restoreVersion'])->name('content.restore-version');
        });
        
        // Project Invitations (outside project scope)
        Route::get('/invitations/{invitation}/accept', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'acceptInvitation'])->name('invitations.accept');
        Route::get('/invitations/{invitation}/decline', [\App\Http\Controllers\Client\ProjectCollaborationController::class, 'declineInvitation'])->name('invitations.decline');
        
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

        // Earnings & Withdrawal
        Route::get('/earnings', [\App\Http\Controllers\Guru\WithdrawalController::class, 'earnings'])->name('earnings');
        Route::get('/withdrawal/create', [\App\Http\Controllers\Guru\WithdrawalController::class, 'create'])->name('withdrawal.create');
        Route::post('/withdrawal', [\App\Http\Controllers\Guru\WithdrawalController::class, 'store'])->name('withdrawal.store');
        Route::get('/withdrawal/history', [\App\Http\Controllers\Guru\WithdrawalController::class, 'history'])->name('withdrawal.history');
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

        // AI Health Monitor
        Route::get('/ai-health', [\App\Http\Controllers\Admin\AIHealthController::class, 'index'])->name('ai-health.index');
        Route::get('/ai-health/status', [\App\Http\Controllers\Admin\AIHealthController::class, 'status'])->name('ai-health.status');
        Route::get('/ai-health/chart-data', [\App\Http\Controllers\Admin\AIHealthController::class, 'chartData'])->name('ai-health.chart-data');
        Route::post('/ai-health/force-check', [\App\Http\Controllers\Admin\AIHealthController::class, 'forceCheck'])->name('ai-health.force-check');
        Route::post('/ai-health/clear-data', [\App\Http\Controllers\Admin\AIHealthController::class, 'clearData'])->name('ai-health.clear-data');

        // AI Model Management
        Route::get('/ai-models', [\App\Http\Controllers\Admin\AIModelController::class, 'index'])->name('ai-models.index');
        Route::get('/ai-models/stats', [\App\Http\Controllers\Admin\AIModelController::class, 'stats'])->name('ai-models.stats');
        Route::post('/ai-models/switch', [\App\Http\Controllers\Admin\AIModelController::class, 'switchModel'])->name('ai-models.switch');
        Route::post('/ai-models/reset-stats', [\App\Http\Controllers\Admin\AIModelController::class, 'resetStats'])->name('ai-models.reset-stats');

        // ML Data Manager
        Route::get('/ml-data', [\App\Http\Controllers\Admin\MLDataController::class, 'index'])->name('ml-data.index');
        Route::get('/ml-data/{mlData}', [\App\Http\Controllers\Admin\MLDataController::class, 'show'])->name('ml-data.show');
        Route::post('/ml-data/cleanup', [\App\Http\Controllers\Admin\MLDataController::class, 'cleanup'])->name('ml-data.cleanup');
        Route::get('/ml-data/stats', [\App\Http\Controllers\Admin\MLDataController::class, 'stats'])->name('ml-data.stats');
        Route::post('/ml-data/refresh', [\App\Http\Controllers\Admin\MLDataController::class, 'refresh'])->name('ml-data.refresh');
        
        // Package Management
        Route::get('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('packages');
        Route::get('/packages/create', [\App\Http\Controllers\Admin\PackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [\App\Http\Controllers\Admin\PackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [\App\Http\Controllers\Admin\PackageController::class, 'destroy'])->name('packages.destroy');
        Route::patch('/packages/{package}/toggle', [\App\Http\Controllers\Admin\PackageController::class, 'toggle'])->name('packages.toggle');

        // Subscription Management
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\PackageController::class, 'subscriptions'])->name('subscriptions');
        Route::post('/subscriptions/{subscription}/verify', [\App\Http\Controllers\Client\SubscriptionController::class, 'adminVerify'])->name('subscriptions.verify');
        Route::post('/subscriptions/{subscription}/reject', [\App\Http\Controllers\Client\SubscriptionController::class, 'adminReject'])->name('subscriptions.reject');
        
        // Financial Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
        
        // Payment Settings
        Route::get('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'index'])->name('payment-settings');
        Route::post('/payment-settings', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'store'])->name('payment-settings.store');
        Route::put('/payment-settings/{paymentSetting}', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'update'])->name('payment-settings.update');
        Route::delete('/payment-settings/{paymentSetting}', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'destroy'])->name('payment-settings.destroy');
        Route::post('/payment-settings/midtrans', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateMidtrans'])->name('payment-settings.midtrans-update');
        Route::post('/payment-settings/xendit', [\App\Http\Controllers\Admin\PaymentSettingController::class, 'updateXendit'])->name('payment-settings.xendit-update');
        
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

        // Guru Monitor
        Route::get('/guru-monitor', [\App\Http\Controllers\Admin\GuruMonitorController::class, 'index'])->name('guru-monitor.index');
        Route::get('/guru-monitor/{guru}', [\App\Http\Controllers\Admin\GuruMonitorController::class, 'show'])->name('guru-monitor.show');
        Route::post('/guru-monitor/{guru}/adjust-earnings', [\App\Http\Controllers\Admin\GuruMonitorController::class, 'adjustEarnings'])->name('guru-monitor.adjust-earnings');

        // Referral Monitor
        Route::get('/referrals', [\App\Http\Controllers\Admin\ReferralController::class, 'index'])->name('referrals.index');
    });
});

// API Routes
Route::prefix('api')->middleware(['auth', 'throttle:60,1'])->group(function () {
    // ── Tidak perlu cek fitur (semua paket bisa) ──────────────────────────────
    Route::post('/ai/generate', [\App\Http\Controllers\Client\AIGeneratorController::class, 'generate'])->name('api.ai.generate');
    Route::get('/check-first-time', [\App\Http\Controllers\Client\AIGeneratorController::class, 'checkFirstTime'])->name('api.check-first-time');
    Route::get('/templates/all', [\App\Http\Controllers\Client\AIGeneratorController::class, 'getAllTemplates'])->name('api.templates.all');
    Route::get('/ml/status', [\App\Http\Controllers\MLSuggestionsController::class, 'getStatus'])->name('api.ml.status');
    Route::get('/ml/preview', [\App\Http\Controllers\MLSuggestionsController::class, 'getPreview'])->name('api.ml.preview');
    Route::get('/ml/weekly-trends', [\App\Http\Controllers\MLSuggestionsController::class, 'getWeeklyTrends'])->name('api.ml.weekly-trends');
    Route::post('/ml/refresh', [\App\Http\Controllers\MLSuggestionsController::class, 'refreshSuggestions'])->name('api.ml.refresh');
    Route::get('/ml/cache-stats', [\App\Http\Controllers\MLSuggestionsController::class, 'getCacheStats'])->name('api.ml.cache-stats');
    Route::post('/caption/{id}/rate', [\App\Http\Controllers\Client\CaptionRatingController::class, 'rate'])->name('api.caption.rate');
    Route::get('/notifications', function() {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')->take(10)->get();
        return response()->json(['notifications' => $notifications]);
    });

    // ── Fitur dikontrol per paket ─────────────────────────────────────────────
    Route::post('/ai/generate-image-caption',    [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateImageCaption'])->middleware('feature:image_caption')->name('api.ai.generate-image-caption');
    Route::post('/ai/analyze-image',             [\App\Http\Controllers\Client\AIGeneratorController::class, 'analyzeImage'])->middleware('feature:image_analysis')->name('api.ai.analyze-image');
    Route::post('/ai/analyze-financial',         [\App\Http\Controllers\Client\AIGeneratorController::class, 'analyzeFinancial'])->middleware('feature:financial_analysis')->name('api.ai.analyze-financial');
    Route::post('/ai/analyze-ebook',             [\App\Http\Controllers\Client\AIGeneratorController::class, 'analyzeEbook'])->middleware('feature:ebook_analysis')->name('api.ai.analyze-ebook');
    Route::post('/ai/analyze-reader-trend',      [\App\Http\Controllers\Client\AIGeneratorController::class, 'analyzeReaderTrend'])->middleware('feature:reader_trend')->name('api.ai.analyze-reader-trend');
    Route::post('/ai/generate-video-content',    [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateVideoContent'])->middleware('feature:video_content')->name('api.ai.generate-video-content');
    Route::post('/ai/predict-performance',       [\App\Http\Controllers\Client\AIGeneratorController::class, 'predictPerformance'])->middleware('feature:performance_predictor')->name('api.ai.predict-performance');
    Route::post('/ai/generate-ab-variants',      [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateABVariants'])->middleware('feature:ab_testing')->name('api.ai.generate-ab-variants');
    Route::post('/ai/generate-multiplatform',    [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateMultiPlatform'])->middleware('feature:multi_platform')->name('api.ai.generate-multiplatform');
    Route::post('/ai/repurpose-content',         [\App\Http\Controllers\Client\AIGeneratorController::class, 'repurposeContent'])->middleware('feature:content_repurpose')->name('api.ai.repurpose-content');
    Route::post('/ai/generate-trend-content',    [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateTrendContent'])->middleware('feature:trend_alert')->name('api.ai.generate-trend-content');
    Route::post('/ai/generate-optimal-content',  [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateOptimalContent'])->middleware('feature:optimal_content')->name('api.ai.generate-optimal-content');
    Route::post('/ai/generate-google-ads',       [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateGoogleAds'])->middleware('feature:google_ads')->name('api.ai.generate-google-ads');
    Route::post('/ai/generate-promo-link',       [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateMagicPromoLink'])->middleware('feature:promo_link')->name('api.ai.generate-promo-link');
    Route::post('/ai/generate-product-explainer',[\App\Http\Controllers\Client\AIGeneratorController::class, 'generateProductExplainer'])->middleware('feature:product_explainer')->name('api.ai.generate-product-explainer');
    Route::post('/ai/generate-seo-metadata',     [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateSeoMetadata'])->middleware('feature:seo_metadata')->name('api.ai.generate-seo-metadata');
    Route::post('/ai/generate-comparison',       [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateComparison'])->middleware('feature:smart_comparison')->name('api.ai.generate-comparison');
    Route::post('/ai/generate-faq',              [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateFaq'])->middleware('feature:faq_generator')->name('api.ai.generate-faq');
    Route::post('/ai/generate-reels-hook',       [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateReelsHook'])->middleware('feature:reels_hook')->name('api.ai.generate-reels-hook');
    Route::post('/ai/generate-quality-badge',    [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateQualityBadge'])->middleware('feature:quality_badge')->name('api.ai.generate-quality-badge');
    Route::post('/ai/generate-discount-campaign',[\App\Http\Controllers\Client\AIGeneratorController::class, 'generateDiscountCampaign'])->middleware('feature:discount_campaign')->name('api.ai.generate-discount-campaign');
    Route::post('/ai/generate-trend-tags',       [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateTrendTags'])->middleware('feature:trend_tags')->name('api.ai.generate-trend-tags');
    Route::post('/ai/generate-lead-magnet',      [\App\Http\Controllers\Client\AIGeneratorController::class, 'generateLeadMagnet'])->middleware('feature:lead_magnet')->name('api.ai.generate-lead-magnet');
    Route::post('/ai/generate-content',          [\App\Http\Controllers\Client\ProjectContentController::class, 'generateContent'])->name('api.ai.generate-content');

    // 🔍 AI Analysis
    Route::post('/analysis/sentiment',       [\App\Http\Controllers\AIAnalysisController::class, 'analyzeSentiment'])->name('api.analysis.sentiment');
    Route::post('/analysis/image',           [\App\Http\Controllers\AIAnalysisController::class, 'analyzeImage'])->name('api.analysis.image');
    Route::post('/analysis/score-caption',   [\App\Http\Controllers\AIAnalysisController::class, 'scoreCaption'])->name('api.analysis.score-caption');
    Route::post('/analysis/recommendations', [\App\Http\Controllers\AIAnalysisController::class, 'getRecommendations'])->name('api.analysis.recommendations');
    Route::post('/analysis/campaign',        [\App\Http\Controllers\AIAnalysisController::class, 'analyzeCampaign'])->name('api.analysis.campaign');
    Route::post('/analysis/article',         [\App\Http\Controllers\AIAnalysisController::class, 'analyzeArticle'])->name('api.analysis.article');
    Route::get('/analysis/history',          [\App\Http\Controllers\AIAnalysisController::class, 'getHistory'])->name('api.analysis.history');
    Route::get('/analysis/dashboard',        [\App\Http\Controllers\AIAnalysisController::class, 'getDashboard'])->name('api.analysis.dashboard');

    // 🔧 Caption Optimizer
    Route::prefix('optimizer')->name('api.optimizer.')->group(function () {
        Route::post('/grammar/check',            [\App\Http\Controllers\CaptionOptimizerController::class, 'checkGrammar'])->name('grammar.check');
        Route::post('/grammar/quick-fix',        [\App\Http\Controllers\CaptionOptimizerController::class, 'quickGrammarFix'])->name('grammar.quick-fix');
        Route::post('/grammar/detailed-analysis',[\App\Http\Controllers\CaptionOptimizerController::class, 'getDetailedGrammarAnalysis'])->name('grammar.detailed-analysis');
        Route::post('/length/shorten',           [\App\Http\Controllers\CaptionOptimizerController::class, 'shortenCaption'])->name('length.shorten');
        Route::post('/length/expand',            [\App\Http\Controllers\CaptionOptimizerController::class, 'expandCaption'])->name('length.expand');
        Route::post('/length/smart-adjust',      [\App\Http\Controllers\CaptionOptimizerController::class, 'smartAdjustLength'])->name('length.smart-adjust');
        Route::get('/length/optimal-guide',      [\App\Http\Controllers\CaptionOptimizerController::class, 'getOptimalLength'])->name('length.optimal-guide');
        Route::post('/length/analyze-impact',    [\App\Http\Controllers\CaptionOptimizerController::class, 'analyzeLengthImpact'])->name('length.analyze-impact');
        Route::post('/batch-optimize',           [\App\Http\Controllers\CaptionOptimizerController::class, 'batchOptimize'])->name('batch-optimize');
        Route::get('/stats',                     [\App\Http\Controllers\CaptionOptimizerController::class, 'getOptimizerStats'])->name('stats');
    });

    // 🔑 Keyword Research
    Route::post('/keyword-research/search',  [\App\Http\Controllers\Client\KeywordResearchController::class, 'search'])->middleware('feature:keyword_research')->name('api.keyword-research.search');
    Route::get('/keyword-research/history',  [\App\Http\Controllers\Client\KeywordResearchController::class, 'history'])->middleware('feature:keyword_research')->name('api.keyword-research.history');

    // 💰 Competitor Pricing
    Route::post('/competitor-analysis/calculate-pricing', [\App\Http\Controllers\Client\CompetitorAnalysisController::class, 'calculatePricing'])->middleware('feature:competitor_analysis')->name('api.competitor-analysis.calculate-pricing');
});

// Webhook Routes (no CSRF, no auth)
Route::prefix('webhook')->group(function () {
    Route::post('/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'handleWebhook'])->name('webhook.whatsapp');
    Route::post('/midtrans', [\App\Http\Controllers\Client\SubscriptionController::class, 'webhookMidtrans'])->name('webhook.midtrans');
    Route::post('/xendit', [\App\Http\Controllers\Client\SubscriptionController::class, 'webhookXendit'])->name('webhook.xendit');
});

Route::prefix('api/whatsapp')->middleware(['auth'])->group(function () {
    Route::post('/send', [\App\Http\Controllers\WhatsAppController::class, 'sendMessage'])->name('api.whatsapp.send');
    Route::post('/broadcast', [\App\Http\Controllers\WhatsAppController::class, 'sendBroadcast'])->name('api.whatsapp.broadcast');
    Route::get('/status', [\App\Http\Controllers\WhatsAppController::class, 'getStatus'])->name('api.whatsapp.status');
    
    // User Integration Routes
    Route::post('/link-account', [\App\Http\Controllers\WhatsAppController::class, 'linkAccount'])->name('api.whatsapp.link-account');
    Route::post('/verify-account', [\App\Http\Controllers\WhatsAppController::class, 'verifyAccount'])->name('api.whatsapp.verify-account');
    Route::get('/user-analytics/{user}', [\App\Http\Controllers\WhatsAppController::class, 'getUserAnalytics'])->name('api.whatsapp.user-analytics');
    
    // Subscription Management
    Route::get('/subscription', [\App\Http\Controllers\WhatsAppController::class, 'getSubscription'])->name('api.whatsapp.subscription');
    Route::post('/subscription', [\App\Http\Controllers\WhatsAppController::class, 'updateSubscription'])->name('api.whatsapp.update-subscription');
    Route::delete('/subscription', [\App\Http\Controllers\WhatsAppController::class, 'deleteSubscription'])->name('api.whatsapp.delete-subscription');
});

// Admin WhatsApp Analytics Routes
Route::prefix('admin/whatsapp-analytics')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\WhatsAppAnalyticsController::class, 'index'])->name('admin.whatsapp-analytics.index');
    Route::get('/data', [\App\Http\Controllers\Admin\WhatsAppAnalyticsController::class, 'getAnalyticsData'])->name('admin.whatsapp-analytics.data');
    Route::post('/refresh', [\App\Http\Controllers\Admin\WhatsAppAnalyticsController::class, 'refresh'])->name('admin.whatsapp-analytics.refresh');
    Route::get('/export', [\App\Http\Controllers\Admin\WhatsAppAnalyticsController::class, 'export'])->name('admin.whatsapp-analytics.export');
});

// Article API Routes (public)
Route::prefix('api/articles')->group(function () {
    Route::get('/', [\App\Http\Controllers\ArticleApiController::class, 'index'])->name('api.articles.index');
    Route::get('/today', [\App\Http\Controllers\ArticleApiController::class, 'today'])->name('api.articles.today');
    Route::get('/category/{category}', [\App\Http\Controllers\ArticleApiController::class, 'byCategory'])->name('api.articles.by-category');
    Route::get('/industry/{industry}', [\App\Http\Controllers\ArticleApiController::class, 'byIndustry'])->name('api.articles.by-industry');
    Route::get('/{slug}', [\App\Http\Controllers\ArticleApiController::class, 'show'])->name('api.articles.show');
});

// 📊 Dashboard & Analytics API Routes (Authenticated)
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Dashboard Analytics
    Route::post('/dashboard/analytics-refresh', [DashboardController::class, 'refreshAnalytics'])->name('api.dashboard.analytics-refresh');
    
    // Analytics API
    Route::get('/analytics/insights', [\App\Http\Controllers\Client\AnalyticsController::class, 'insights'])->name('api.analytics.insights');
    Route::post('/analytics/competitor-comparison', [\App\Http\Controllers\Client\AnalyticsController::class, 'compareWithCompetitor'])->name('api.analytics.competitor-comparison');
    
    // Dynamic Date API
    Route::get('/dynamic-dates/seasonal-events', function() {
        return response()->json([
            'success' => true,
            'data' => \App\Services\DynamicDateService::getSeasonalEvents()
        ]);
    })->name('api.dynamic-dates.seasonal-events');
    
    Route::get('/dynamic-dates/national-days', function() {
        return response()->json([
            'success' => true,
            'data' => \App\Services\DynamicDateService::getNationalDays()
        ]);
    })->name('api.dynamic-dates.national-days');
    
    Route::get('/dynamic-dates/nearby-holidays', function() {
        return response()->json([
            'success' => true,
            'data' => \App\Services\DynamicDateService::getNearbyHolidays()
        ]);
    })->name('api.dynamic-dates.nearby-holidays');
    
    Route::get('/dynamic-dates/current-year', function() {
        return response()->json([
            'success' => true,
            'data' => [
                'current_year' => \App\Services\DynamicDateService::getCurrentYear(),
                'next_year' => \App\Services\DynamicDateService::getNextYear(),
                'academic_year' => \App\Services\DynamicDateService::getAcademicYear(),
                'quarterly_dates' => \App\Services\DynamicDateService::getQuarterlyDates()
            ]
        ]);
    })->name('api.dynamic-dates.current-year');
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

require __DIR__.'/auth.php';

// Load test routes only in local/testing environment
if (app()->environment('local', 'testing')) {
    require __DIR__.'/test.php';
}

