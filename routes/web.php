<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CopywritingRequestController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public routes
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create/{package}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Copywriting Requests
    Route::get('/copywriting', [CopywritingRequestController::class, 'index'])->name('copywriting.index');
    Route::get('/copywriting/create/{order}', [CopywritingRequestController::class, 'create'])->name('copywriting.create');
    Route::post('/copywriting', [CopywritingRequestController::class, 'store'])->name('copywriting.store');
    Route::get('/copywriting/{copywriting}', [CopywritingRequestController::class, 'show'])->name('copywriting.show');
    Route::put('/copywriting/{copywriting}', [CopywritingRequestController::class, 'update'])->name('copywriting.update');
    Route::post('/copywriting/{copywriting}/revision', [CopywritingRequestController::class, 'requestRevision'])->name('copywriting.revision');
    Route::post('/copywriting/{copywriting}/rate', [CopywritingRequestController::class, 'rate'])->name('copywriting.rate');
    
    // Operator routes
    Route::prefix('operator')->name('operator.')->group(function () {
        Route::get('/queue', [CopywritingRequestController::class, 'queue'])->name('queue');
        Route::post('/copywriting/{copywriting}/assign', [CopywritingRequestController::class, 'assign'])->name('assign');
    });
});

require __DIR__.'/auth.php';

