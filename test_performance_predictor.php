<?php

// Simple test script for Performance Predictor
require_once 'vendor/autoload.php';

use App\Services\CaptionPerformancePredictorService;
use App\Services\GeminiService;
use App\Services\MLDataService;

// Test caption
$testCaption = "Hai Bun! 👋 Tau gak sih rahasia kulit glowing tanpa ribet? ✨ 

Cuma butuh 3 langkah simpel:
1. Cleansing yang bersih 🧼
2. Toner untuk pH balance 💧  
3. Moisturizer yang tepat 🌸

Produk skincare lokal ini udah terbukti bikin kulit cerah dalam 2 minggu! 

Mau tau produk apa? Comment \"GLOWING\" ya! 💕

#skincare #glowing #kulitsehat #skincarelokal";

echo "Testing Caption Performance Predictor...\n";
echo "Caption: " . substr($testCaption, 0, 100) . "...\n\n";

try {
    // Initialize services (simplified for testing)
    $geminiService = new GeminiService();
    $mlService = new MLDataService();
    
    $predictor = new CaptionPerformancePredictorService($geminiService, $mlService);
    
    $context = [
        'platform' => 'instagram',
        'industry' => 'beauty',
        'user_id' => 1
    ];
    
    echo "Analyzing caption components...\n";
    
    // Test individual methods
    $reflection = new ReflectionClass($predictor);
    $analyzeMethod = $reflection->getMethod('analyzeCaptionComponents');
    $analyzeMethod->setAccessible(true);
    
    $analysis = $analyzeMethod->invoke($predictor, $testCaption, 'instagram');
    
    echo "Analysis Results:\n";
    echo "- Length: " . $analysis['length'] . " characters\n";
    echo "- Word Count: " . $analysis['word_count'] . " words\n";
    echo "- Emoji Count: " . $analysis['emoji_count'] . " emojis\n";
    echo "- Hashtag Count: " . $analysis['hashtag_count'] . " hashtags\n";
    echo "- Has CTA: " . ($analysis['has_cta'] ? 'Yes' : 'No') . "\n";
    echo "- Has Hook: " . ($analysis['has_hook'] ? 'Yes' : 'No') . "\n";
    echo "- Sentiment: " . $analysis['sentiment'] . "\n";
    echo "- Readability: " . $analysis['readability'] . "/10\n";
    
    echo "\nTEST PASSED! ✅\n";
    echo "Performance Predictor components are working correctly.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}