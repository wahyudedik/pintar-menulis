<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        // Use gemini-2.5-flash - the latest stable model
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Generate copywriting content using Gemini AI
     */
    public function generateCopywriting(array $params)
    {
        // Validate API key
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key not configured');
            throw new \Exception('API Key tidak dikonfigurasi. Hubungi administrator.');
        }

        $prompt = $this->buildPrompt($params);

        try {
            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 4096,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ]
                ]
            ];

            Log::info('Gemini API Request', ['url' => $this->apiUrl, 'prompt_length' => strlen($prompt)]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey
                ])
                ->post($this->apiUrl, $requestData);

            $statusCode = $response->status();
            Log::info('Gemini API Response Status: ' . $statusCode);

            if ($response->successful()) {
                $data = $response->json();
                
                // Log response structure for debugging
                Log::info('Gemini API Response Structure', [
                    'has_candidates' => isset($data['candidates']),
                    'candidates_count' => isset($data['candidates']) ? count($data['candidates']) : 0,
                    'full_response' => $data
                ]);
                
                // Check if response has candidates
                if (!isset($data['candidates']) || !is_array($data['candidates']) || count($data['candidates']) === 0) {
                    Log::error('Gemini API: No candidates in response', ['response' => $data]);
                    throw new \Exception('AI tidak menghasilkan konten. Silakan coba dengan brief yang berbeda.');
                }
                
                $candidate = $data['candidates'][0];
                
                // Check finish reason
                if (isset($candidate['finishReason'])) {
                    $finishReason = $candidate['finishReason'];
                    Log::info('Gemini Finish Reason: ' . $finishReason);
                    
                    if ($finishReason === 'SAFETY') {
                        throw new \Exception('Konten diblokir oleh filter keamanan. Silakan gunakan brief yang lebih profesional.');
                    } elseif ($finishReason === 'RECITATION') {
                        throw new \Exception('Konten terlalu mirip dengan konten yang ada. Silakan coba brief yang lebih unik.');
                    } elseif ($finishReason !== 'STOP' && $finishReason !== 'MAX_TOKENS') {
                        Log::warning('Unexpected finish reason: ' . $finishReason);
                    }
                }
                
                // Extract text content
                if (isset($candidate['content']['parts']) && is_array($candidate['content']['parts'])) {
                    foreach ($candidate['content']['parts'] as $part) {
                        if (isset($part['text']) && !empty($part['text'])) {
                            return trim($part['text']);
                        }
                    }
                }
                
                // If we reach here, structure is unexpected
                Log::error('Gemini API: Cannot extract text from response', ['candidate' => $candidate]);
                throw new \Exception('Format response tidak valid. Silakan coba lagi.');
            }

            // Handle error responses
            $errorBody = $response->body();
            Log::error('Gemini API Error Response', [
                'status' => $statusCode,
                'body' => $errorBody
            ]);
            
            // Try to parse error message
            try {
                $errorData = $response->json();
                if (isset($errorData['error']['message'])) {
                    $errorMessage = $errorData['error']['message'];
                    
                    // Handle specific error cases
                    if (strpos($errorMessage, 'API key') !== false) {
                        throw new \Exception('API Key tidak valid. Silakan generate API key baru di https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'not found') !== false || strpos($errorMessage, 'not supported') !== false) {
                        throw new \Exception('Model Gemini tidak tersedia. API key mungkin tidak valid atau expired. Silakan generate API key baru di https://aistudio.google.com/app/apikey');
                    } elseif (strpos($errorMessage, 'quota') !== false) {
                        throw new \Exception('Kuota API habis. Hubungi administrator.');
                    } elseif (strpos($errorMessage, 'rate limit') !== false) {
                        throw new \Exception('Terlalu banyak request. Silakan tunggu beberapa saat.');
                    }
                    
                    throw new \Exception('API Error: ' . $errorMessage);
                }
            } catch (\JsonException $e) {
                // Response is not JSON
                Log::error('Non-JSON error response: ' . $errorBody);
            }
            
            throw new \Exception('Gagal terhubung ke AI service (Status: ' . $statusCode . '). Silakan cek API key di https://aistudio.google.com/app/apikey');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini Connection Exception', ['message' => $e->getMessage()]);
            throw new \Exception('Gagal terhubung ke AI service. Periksa koneksi internet Anda.');
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Gemini Request Exception', ['message' => $e->getMessage()]);
            throw new \Exception('Request gagal: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Re-throw our custom exceptions
            if (strpos($e->getMessage(), 'API') !== false || 
                strpos($e->getMessage(), 'konten') !== false ||
                strpos($e->getMessage(), 'Gagal') !== false) {
                throw $e;
            }
            
            // Log unexpected exceptions
            Log::error('Gemini Unexpected Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Terjadi kesalahan tidak terduga. Silakan coba lagi.');
        }
    }

    /**
     * Build prompt for Gemini based on parameters
     */
    protected function buildPrompt(array $params)
    {
        $type = $params['type'] ?? 'caption';
        $brief = $params['brief'] ?? '';
        $tone = $params['tone'] ?? 'casual';
        $platform = $params['platform'] ?? 'instagram';
        $keywords = $params['keywords'] ?? '';

        $prompt = "Kamu adalah copywriter profesional yang ahli dalam membuat konten promosi untuk UMKM Indonesia.\n\n";
        $prompt .= "Tugas: Buatkan {$type} untuk {$platform} dengan tone {$tone}.\n\n";
        $prompt .= "Brief dari client:\n{$brief}\n\n";
        
        if ($keywords) {
            $prompt .= "Keywords yang harus dimasukkan: {$keywords}\n\n";
        }

        $prompt .= "Panduan:\n";
        $prompt .= "1. Gunakan bahasa Indonesia yang menarik dan mudah dipahami\n";
        $prompt .= "2. Buat opening yang menarik perhatian dalam 3 detik pertama\n";
        $prompt .= "3. Fokus pada manfaat produk/jasa untuk customer\n";
        $prompt .= "4. Sertakan call-to-action yang jelas\n";
        $prompt .= "5. Maksimal 150 kata untuk caption\n";
        
        if ($platform === 'instagram') {
            $prompt .= "6. Sertakan 5-10 hashtag relevan di akhir\n";
        }

        $prompt .= "\nSekarang buatkan copywriting yang menarik:";

        return $prompt;
    }

    /**
     * Train ML model with guru's feedback
     */
    public function trainModel(array $trainingData)
    {
        // TODO: Implement ML training logic
        // This will store training data for future model improvement
        
        return [
            'success' => true,
            'message' => 'Training data berhasil disimpan'
        ];
    }

    /**
     * Get model performance metrics
     */
    public function getModelMetrics()
    {
        // TODO: Implement metrics retrieval
        
        return [
            'accuracy' => 0.85,
            'total_trainings' => 0,
            'last_trained' => null
        ];
    }
}
