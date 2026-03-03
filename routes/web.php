<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CopywritingRequestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Dashboard with role-based routing
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public routes
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    });
});

// API Routes
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::post('/ai/generate', [\App\Http\Controllers\Client\AIGeneratorController::class, 'generate'])->name('api.ai.generate');
    Route::get('/notifications', function() {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return response()->json(['notifications' => $notifications]);
    });
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

