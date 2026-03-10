<?php

// Test routes untuk AI Analysis System
use Illuminate\Support\Facades\Route;

Route::get('/test-analysis', function () {
    return view('test-analysis');
})->middleware(['auth']);

Route::post('/test-sentiment', function () {
    $service = app(\App\Services\AIAnalysisService::class);
    $result = $service->analyzeSentiment("Produk ini sangat bagus dan saya sangat puas! Recommended banget! 🔥");
    return response()->json($result);
})->middleware(['auth']);

Route::post('/test-quality', function () {
    $service = app(\App\Services\AIAnalysisService::class);
    $result = $service->scoreCaption("Dapatkan diskon 50% hari ini! Jangan lewatkan kesempatan emas ini! 🎉", "instagram", "fashion");
    return response()->json($result);
})->middleware(['auth']);

Route::post('/test-recommendations', function () {
    $service = app(\App\Services\AIAnalysisService::class);
    $result = $service->getSmartRecommendations("Produk bagus", "instagram", "Wanita 18-35");
    return response()->json($result);
})->middleware(['auth']);
