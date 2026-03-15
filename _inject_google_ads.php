<?php
$file = 'resources/views/client/ai-generator.blade.php';
$content = file_get_contents($file);
$panel = file_get_contents('_google_ads_panel.html');
$alpineData = file_get_contents('_google_ads_alpine_data.js');
$alpineMethod = file_get_contents('_google_ads_alpine_method.js');

// 1. Inject panel before ML Upgrade Modal comment
$search1 = '        <!-- 🤖 ML Upgrade Modal & Features -->';
$content = str_replace($search1, $panel . "\n" . $search1, $content);

// 2. Inject Alpine data properties before init()
$search2 = '            // Initialize - check if user is first time';
$content = str_replace($search2, $alpineData . "\n" . $search2, $content);

// 3. Inject generateGoogleAdsAction method after generateTrendContent method
$search3 = '            generateBasicTrendContent() {';
$content = str_replace($search3, $alpineMethod . "\n\n            generateBasicTrendContent() {", $content);

file_put_contents($file, $content);
echo "Done!\n";
