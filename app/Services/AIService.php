<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
    }

    public function generateCopywriting(array $params)
    {
        $prompt = $this->buildPrompt($params);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah copywriter profesional yang ahli dalam membuat konten promosi untuk UMKM Indonesia. Gunakan bahasa yang menarik, persuasif, dan sesuai dengan target audience.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'];
        }

        throw new \Exception('Failed to generate copywriting: ' . $response->body());
    }

    protected function buildPrompt(array $params)
    {
        $type = $params['type'] ?? 'caption';
        $brief = $params['brief'] ?? '';
        $tone = $params['tone'] ?? 'casual';
        $platform = $params['platform'] ?? 'instagram';
        $keywords = $params['keywords'] ?? '';

        $prompt = "Buatkan {$type} untuk {$platform} dengan tone {$tone}.\n\n";
        $prompt .= "Brief: {$brief}\n\n";
        
        if ($keywords) {
            $prompt .= "Keywords yang harus dimasukkan: {$keywords}\n\n";
        }

        $prompt .= "Pastikan konten:\n";
        $prompt .= "- Menarik perhatian dalam 3 detik pertama\n";
        $prompt .= "- Menggunakan bahasa yang sesuai dengan target audience UMKM\n";
        $prompt .= "- Memiliki call-to-action yang jelas\n";
        $prompt .= "- Tidak lebih dari 150 kata untuk caption\n";
        
        if ($platform === 'instagram') {
            $prompt .= "- Sertakan 5-10 hashtag relevan di akhir\n";
        }

        return $prompt;
    }
}
