<?php

// Simple test script to check Gemini API
$apiKey = 'AIzaSyAtnGdgdSlsfi5sQLDkjTAY1EAObMRNi1A';
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Buatkan caption Instagram untuk produk kopi dengan tone casual']
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'topK' => 40,
        'topP' => 0.95,
        'maxOutputTokens' => 1024,
    ]
];

$ch = curl_init($apiUrl . '?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n\n";
echo "Response:\n";
echo $response;
echo "\n\n";

$decoded = json_decode($response, true);
echo "Decoded:\n";
print_r($decoded);
