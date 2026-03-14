<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class SpeechToTextService
{
    private $enabled;
    private $provider;
    private $googleApiKey;
    private $openaiApiKey;
    private $assemblyAiKey;

    public function __construct()
    {
        $this->enabled = config('services.speech_to_text.enabled', false);
        $this->provider = config('services.speech_to_text.provider', 'google');
        $this->googleApiKey = config('services.speech_to_text.google_api_key');
        $this->openaiApiKey = config('services.speech_to_text.openai_api_key');
        $this->assemblyAiKey = config('services.speech_to_text.assembly_ai_key');
    }

    /**
     * 🎵 Convert audio file to text
     */
    public function convertAudioToText(string $audioPath, array $options = []): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'Speech-to-text service is disabled'
            ];
        }

        try {
            switch ($this->provider) {
                case 'google':
                    return $this->convertWithGoogle($audioPath, $options);
                
                case 'openai':
                    return $this->convertWithOpenAI($audioPath, $options);
                
                case 'assembly':
                    return $this->convertWithAssemblyAI($audioPath, $options);
                
                default:
                    return [
                        'success' => false,
                        'message' => 'Invalid speech-to-text provider'
                    ];
            }

        } catch (Exception $e) {
            Log::error('Speech-to-text conversion failed', [
                'provider' => $this->provider,
                'audio_path' => $audioPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to convert audio to text: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🔊 Convert audio URL to text
     */
    public function convertAudioUrlToText(string $audioUrl, array $options = []): array
    {
        try {
            // Download audio file first
            $audioPath = $this->downloadAudioFile($audioUrl);
            if (!$audioPath) {
                return [
                    'success' => false,
                    'message' => 'Failed to download audio file'
                ];
            }

            // Convert to text
            $result = $this->convertAudioToText($audioPath, $options);

            // Clean up temporary file
            Storage::disk('public')->delete($audioPath);

            return $result;

        } catch (Exception $e) {
            Log::error('Audio URL to text conversion failed', [
                'url' => $audioUrl,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process audio URL: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🎙️ Convert using Google Speech-to-Text API
     */
    private function convertWithGoogle(string $audioPath, array $options = []): array
    {
        if (!$this->googleApiKey) {
            return [
                'success' => false,
                'message' => 'Google Speech API key not configured'
            ];
        }

        try {
            $fullPath = storage_path('app/public/' . $audioPath);
            
            // Convert audio to base64
            $audioContent = base64_encode(file_get_contents($fullPath));
            
            // Prepare request
            $requestData = [
                'config' => [
                    'encoding' => $this->detectAudioEncoding($fullPath),
                    'sampleRateHertz' => 16000,
                    'languageCode' => $options['language'] ?? 'id-ID', // Indonesian
                    'alternativeLanguageCodes' => ['en-US'], // Fallback to English
                    'enableAutomaticPunctuation' => true,
                    'enableWordTimeOffsets' => false,
                    'model' => 'latest_long' // Better for longer audio
                ],
                'audio' => [
                    'content' => $audioContent
                ]
            ];

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://speech.googleapis.com/v1/speech:recognize?key={$this->googleApiKey}", $requestData);

            if (!$response->successful()) {
                $error = $response->json();
                throw new Exception('Google Speech API error: ' . ($error['error']['message'] ?? 'Unknown error'));
            }

            $result = $response->json();
            
            if (empty($result['results'])) {
                return [
                    'success' => false,
                    'message' => 'No speech detected in audio',
                    'provider' => 'google'
                ];
            }

            // Extract transcript
            $transcript = '';
            $confidence = 0;
            
            foreach ($result['results'] as $resultItem) {
                if (isset($resultItem['alternatives'][0])) {
                    $alternative = $resultItem['alternatives'][0];
                    $transcript .= $alternative['transcript'] . ' ';
                    $confidence = max($confidence, $alternative['confidence'] ?? 0);
                }
            }

            return [
                'success' => true,
                'transcript' => trim($transcript),
                'confidence' => $confidence,
                'language' => $options['language'] ?? 'id-ID',
                'provider' => 'google',
                'duration' => $this->getAudioDuration($fullPath)
            ];

        } catch (Exception $e) {
            Log::error('Google Speech-to-text failed', [
                'audio_path' => $audioPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Google Speech conversion failed: ' . $e->getMessage(),
                'provider' => 'google'
            ];
        }
    }

    /**
     * 🤖 Convert using OpenAI Whisper API
     */
    private function convertWithOpenAI(string $audioPath, array $options = []): array
    {
        if (!$this->openaiApiKey) {
            return [
                'success' => false,
                'message' => 'OpenAI API key not configured'
            ];
        }

        try {
            $fullPath = storage_path('app/public/' . $audioPath);
            
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                ])
                ->attach('file', file_get_contents($fullPath), basename($fullPath))
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => $options['language'] ?? 'id', // Indonesian
                    'response_format' => 'json',
                    'temperature' => 0
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new Exception('OpenAI Whisper API error: ' . ($error['error']['message'] ?? 'Unknown error'));
            }

            $result = $response->json();

            return [
                'success' => true,
                'transcript' => $result['text'] ?? '',
                'confidence' => 0.95, // Whisper doesn't provide confidence scores
                'language' => $options['language'] ?? 'id',
                'provider' => 'openai',
                'duration' => $this->getAudioDuration($fullPath)
            ];

        } catch (Exception $e) {
            Log::error('OpenAI Whisper conversion failed', [
                'audio_path' => $audioPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'OpenAI Whisper conversion failed: ' . $e->getMessage(),
                'provider' => 'openai'
            ];
        }
    }

    /**
     * 🔧 Convert using AssemblyAI
     */
    private function convertWithAssemblyAI(string $audioPath, array $options = []): array
    {
        if (!$this->assemblyAiKey) {
            return [
                'success' => false,
                'message' => 'AssemblyAI API key not configured'
            ];
        }

        try {
            $fullPath = storage_path('app/public/' . $audioPath);
            
            // Step 1: Upload audio file
            $uploadResponse = Http::timeout(60)
                ->withHeaders([
                    'authorization' => $this->assemblyAiKey,
                ])
                ->attach('file', file_get_contents($fullPath), basename($fullPath))
                ->post('https://api.assemblyai.com/v2/upload');

            if (!$uploadResponse->successful()) {
                throw new Exception('AssemblyAI upload failed');
            }

            $uploadResult = $uploadResponse->json();
            $audioUrl = $uploadResult['upload_url'];

            // Step 2: Request transcription
            $transcriptResponse = Http::timeout(60)
                ->withHeaders([
                    'authorization' => $this->assemblyAiKey,
                    'content-type' => 'application/json'
                ])
                ->post('https://api.assemblyai.com/v2/transcript', [
                    'audio_url' => $audioUrl,
                    'language_code' => $options['language'] ?? 'id', // Indonesian
                    'punctuate' => true,
                    'format_text' => true
                ]);

            if (!$transcriptResponse->successful()) {
                throw new Exception('AssemblyAI transcription request failed');
            }

            $transcriptResult = $transcriptResponse->json();
            $transcriptId = $transcriptResult['id'];

            // Step 3: Poll for completion
            $maxAttempts = 30; // 5 minutes max
            $attempt = 0;

            while ($attempt < $maxAttempts) {
                sleep(10); // Wait 10 seconds

                $statusResponse = Http::timeout(30)
                    ->withHeaders([
                        'authorization' => $this->assemblyAiKey,
                    ])
                    ->get("https://api.assemblyai.com/v2/transcript/{$transcriptId}");

                if ($statusResponse->successful()) {
                    $status = $statusResponse->json();
                    
                    if ($status['status'] === 'completed') {
                        return [
                            'success' => true,
                            'transcript' => $status['text'] ?? '',
                            'confidence' => $status['confidence'] ?? 0,
                            'language' => $options['language'] ?? 'id',
                            'provider' => 'assembly',
                            'duration' => $status['audio_duration'] ?? 0
                        ];
                    } elseif ($status['status'] === 'error') {
                        throw new Exception('AssemblyAI transcription failed: ' . ($status['error'] ?? 'Unknown error'));
                    }
                }

                $attempt++;
            }

            throw new Exception('AssemblyAI transcription timeout');

        } catch (Exception $e) {
            Log::error('AssemblyAI conversion failed', [
                'audio_path' => $audioPath,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'AssemblyAI conversion failed: ' . $e->getMessage(),
                'provider' => 'assembly'
            ];
        }
    }

    /**
     * 📥 Download audio file from URL
     */
    private function downloadAudioFile(string $audioUrl): ?string
    {
        try {
            $response = Http::timeout(30)->get($audioUrl);
            
            if (!$response->successful()) {
                return null;
            }

            $filename = 'voice_' . time() . '_' . uniqid() . '.ogg';
            $path = 'whatsapp/voice/' . $filename;
            
            Storage::disk('public')->put($path, $response->body());
            
            return $path;

        } catch (Exception $e) {
            Log::error('Audio file download failed', [
                'url' => $audioUrl,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * 🎵 Detect audio encoding
     */
    private function detectAudioEncoding(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'wav':
                return 'LINEAR16';
            case 'flac':
                return 'FLAC';
            case 'ogg':
                return 'OGG_OPUS';
            case 'mp3':
                return 'MP3';
            case 'webm':
                return 'WEBM_OPUS';
            default:
                return 'OGG_OPUS'; // Default for WhatsApp voice messages
        }
    }

    /**
     * ⏱️ Get audio duration (approximate)
     */
    private function getAudioDuration(string $filePath): float
    {
        try {
            $fileSize = filesize($filePath);
            // Rough estimate: 1 second ≈ 16KB for voice messages
            return round($fileSize / 16000, 1);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * 🧹 Clean up old voice files
     */
    public function cleanupOldVoiceFiles(int $daysOld = 7): int
    {
        try {
            $cutoffTime = now()->subDays($daysOld);
            $voiceFiles = Storage::disk('public')->files('whatsapp/voice');
            $deletedCount = 0;

            foreach ($voiceFiles as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);
                
                if ($lastModified < $cutoffTime->timestamp) {
                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }

            Log::info('Voice files cleanup completed', [
                'deleted_count' => $deletedCount,
                'cutoff_days' => $daysOld
            ]);

            return $deletedCount;

        } catch (Exception $e) {
            Log::error('Voice files cleanup failed', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * 📊 Get service statistics
     */
    public function getServiceStats(): array
    {
        return [
            'enabled' => $this->enabled,
            'provider' => $this->provider,
            'google_configured' => !empty($this->googleApiKey),
            'openai_configured' => !empty($this->openaiApiKey),
            'assembly_configured' => !empty($this->assemblyAiKey),
            'voice_files_count' => count(Storage::disk('public')->files('whatsapp/voice') ?? [])
        ];
    }
}