<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class WhatsAppService
{
    private $apiUrl;
    private $token;
    private $device;
    private $enabled;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.fonnte_api_url');
        $this->token = config('services.whatsapp.fonnte_token');
        $this->device = config('services.whatsapp.fonnte_device');
        $this->enabled = config('services.whatsapp.enabled', false);
    }

    /**
     * 📱 Send text message via WhatsApp
     */
    public function sendMessage(string $target, string $message, array $options = []): array
    {
        if (!$this->enabled) {
            Log::warning('WhatsApp service is disabled');
            return ['success' => false, 'message' => 'WhatsApp service disabled'];
        }

        try {
            $payload = [
                'target' => $this->formatPhoneNumber($target),
                'message' => $message,
                'countryCode' => '62', // Indonesia
            ];

            // Add optional parameters
            if (isset($options['delay'])) {
                $payload['delay'] = $options['delay'];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl . '/send', $payload);

            $result = $response->json();

            Log::info('WhatsApp message sent', [
                'target' => $target,
                'status' => $response->status(),
                'response' => $result
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
                'message' => $result['reason'] ?? 'Message sent successfully'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp send message failed', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp message: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🖼️ Send image with caption via WhatsApp
     */
    public function sendImage(string $target, string $imageUrl, string $caption = ''): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp service disabled'];
        }

        try {
            $payload = [
                'target' => $this->formatPhoneNumber($target),
                'url' => $imageUrl,
                'caption' => $caption,
                'countryCode' => '62',
            ];

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl . '/send', $payload);

            $result = $response->json();

            Log::info('WhatsApp image sent', [
                'target' => $target,
                'image_url' => $imageUrl,
                'status' => $response->status()
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
                'message' => $result['reason'] ?? 'Image sent successfully'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp send image failed', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp image: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🎵 Send audio file via WhatsApp
     */
    public function sendAudio(string $target, string $audioUrl): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp service disabled'];
        }

        try {
            $payload = [
                'target' => $this->formatPhoneNumber($target),
                'url' => $audioUrl,
                'countryCode' => '62',
            ];

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl . '/send', $payload);

            $result = $response->json();

            return [
                'success' => $response->successful(),
                'data' => $result,
                'message' => $result['reason'] ?? 'Audio sent successfully'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp send audio failed', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp audio: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🎙️ Process voice message with speech-to-text
     */
    public function processVoiceMessage(string $target, string $voiceUrl): array
    {
        try {
            // Send processing message
            $this->sendMessage($target, "🎵 Sedang memproses voice note... Tunggu sebentar ya!");

            // Convert voice to text
            $speechService = app(\App\Services\SpeechToTextService::class);
            $transcription = $speechService->convertAudioUrlToText($voiceUrl, [
                'language' => 'id-ID' // Indonesian
            ]);

            if (!$transcription['success']) {
                return $this->sendMessage($target, "❌ Gagal memproses voice note. Coba kirim ulang atau ketik pesan text ya!");
            }

            $transcript = $transcription['transcript'];
            $confidence = $transcription['confidence'] ?? 0;

            if (empty($transcript)) {
                return $this->sendMessage($target, "❌ Tidak ada suara yang terdeteksi. Coba rekam ulang dengan suara yang lebih jelas ya!");
            }

            // Generate caption from transcript
            $captionResult = $this->sendAICaption($target, $transcript, [
                'platform' => 'instagram',
                'tone' => 'engaging',
                'target_audience' => 'Gen Z Indonesia'
            ]);

            // Send transcript info
            $transcriptMessage = "🎙️ *Voice to Text Result*\n\n";
            $transcriptMessage .= "📝 *Transcript:* {$transcript}\n";
            $transcriptMessage .= "🎯 *Confidence:* " . round($confidence * 100, 1) . "%\n";
            $transcriptMessage .= "⏱️ *Duration:* " . ($transcription['duration'] ?? 'N/A') . "s\n\n";
            $transcriptMessage .= "✨ Caption AI sudah dikirim di pesan berikutnya!";

            $this->sendMessage($target, $transcriptMessage);

            return $captionResult;

        } catch (Exception $e) {
            Log::error('Voice message processing failed', [
                'target' => $target,
                'voice_url' => $voiceUrl,
                'error' => $e->getMessage()
            ]);

            return $this->sendMessage($target, "❌ Terjadi kesalahan saat memproses voice note. Coba lagi nanti ya!");
        }
    }

    /**
     * 📋 Send template message (for bulk/broadcast)
     */
    public function sendTemplate(array $targets, string $message, array $options = []): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp service disabled'];
        }

        $results = [];
        $delay = $options['delay'] ?? 2; // Default 2 seconds delay between messages

        foreach ($targets as $index => $target) {
            // Add progressive delay to avoid rate limiting
            $messageDelay = $delay * $index;
            
            $result = $this->sendMessage($target, $message, ['delay' => $messageDelay]);
            $results[] = [
                'target' => $target,
                'success' => $result['success'],
                'message' => $result['message']
            ];

            // Small delay between API calls
            if ($index < count($targets) - 1) {
                usleep(500000); // 0.5 second
            }
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        
        return [
            'success' => $successCount > 0,
            'total_sent' => $successCount,
            'total_failed' => count($results) - $successCount,
            'results' => $results
        ];
    }

    /**
     * 📊 Get device status and info
     */
    public function getDeviceStatus(): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp service disabled'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl . '/device');

            $result = $response->json();

            return [
                'success' => $response->successful(),
                'data' => $result,
                'connected' => $result['status'] ?? false
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp device status check failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to check device status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 🔄 Validate webhook signature
     */
    public function validateWebhook(string $signature, string $payload): bool
    {
        $webhookToken = config('services.whatsapp.webhook_token');
        $expectedSignature = hash_hmac('sha256', $payload, $webhookToken);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * 📞 Format phone number for Indonesian numbers
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle Indonesian phone numbers
        if (substr($phone, 0, 1) === '0') {
            // Replace leading 0 with 62
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            // Add 62 if not present
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * 💾 Download and store media from WhatsApp
     */
    public function downloadMedia(string $mediaUrl, string $filename = null): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->get($mediaUrl);

            if (!$response->successful()) {
                return null;
            }

            $filename = $filename ?: 'whatsapp_media_' . time() . '_' . uniqid();
            $path = 'whatsapp/media/' . $filename;
            
            Storage::disk('public')->put($path, $response->body());
            
            return $path;

        } catch (Exception $e) {
            Log::error('WhatsApp media download failed', [
                'url' => $mediaUrl,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * 🎯 Send AI-generated caption based on user input
     */
    public function sendAICaption(string $target, string $prompt, array $options = []): array
    {
        try {
            // Use existing GeminiService to generate caption
            $geminiService = app(GeminiService::class);
            
            $result = $geminiService->generateCopywriting([
                'user_id' => $options['user_id'] ?? null,
                'brief' => $prompt,
                'platform' => $options['platform'] ?? 'instagram',
                'tone' => $options['tone'] ?? 'engaging',
                'category' => $options['category'] ?? 'general',
                'target_audience' => $options['target_audience'] ?? 'Gen Z Indonesia',
                'cta_type' => $options['cta_type'] ?? 'engagement',
                'hashtag_count' => $options['hashtag_count'] ?? 10,
                'emoji_style' => $options['emoji_style'] ?? 'moderate',
                'variation_count' => 1,
                'auto_hashtag' => true,
                'local_language' => 'bahasa_indonesia'
            ]);

            if ($result && isset($result['caption'])) {
                $message = "🤖 *AI Caption Generator*\n\n";
                $message .= $result['caption'] . "\n\n";
                $message .= "📊 *Performance Score:* " . ($result['performance_score'] ?? 'N/A') . "\n";
                $message .= "⏱️ *Generated in:* " . ($result['generation_time'] ?? 'N/A') . "s\n\n";
                $message .= "_Powered by Pintar Menulis AI_ ✨";

                return $this->sendMessage($target, $message);
            }

            return $this->sendMessage($target, "❌ Maaf, gagal generate caption. Coba lagi ya!");

        } catch (Exception $e) {
            Log::error('WhatsApp AI caption generation failed', [
                'target' => $target,
                'prompt' => $prompt,
                'error' => $e->getMessage()
            ]);

            return $this->sendMessage($target, "❌ Terjadi kesalahan sistem. Tim kami sedang memperbaiki.");
        }
    }

    /**
     * 📅 Send daily content ideas
     */
    public function sendDailyContentIdeas(string $target, array $userPreferences = []): array
    {
        try {
            $geminiService = app(GeminiService::class);
            
            // Generate daily content ideas using existing generateCopywriting method
            $ideas = $geminiService->generateCopywriting([
                'user_id' => $userPreferences['user_id'] ?? null,
                'brief' => 'Generate 4 ide konten harian untuk UMKM Indonesia yang trending dan engaging',
                'platform' => 'instagram',
                'tone' => 'engaging',
                'category' => 'trend_fresh_ideas',
                'target_audience' => $userPreferences['target_audience'] ?? 'Gen Z Indonesia',
                'cta_type' => 'engagement',
                'hashtag_count' => 5,
                'emoji_style' => 'moderate',
                'variation_count' => 1,
                'auto_hashtag' => true,
                'local_language' => 'bahasa_indonesia'
            ]);

            $message = "🌅 *Daily Content Ideas - " . date('d M Y') . "*\n\n";
            
            if ($ideas && isset($ideas['caption'])) {
                $message .= $ideas['caption'] . "\n\n";
            } else {
                $message .= "📝 *Ide Konten Hari Ini:*\n";
                $message .= "1. 🔥 Trending topic: Share pengalaman unik bisnis\n";
                $message .= "2. 💡 Tips: Bagikan tips praktis untuk customer\n";
                $message .= "3. 🎯 Behind the scenes: Proses kerja sehari-hari\n";
                $message .= "4. 📊 Data menarik: Statistik atau fakta industri\n\n";
            }
            
            $message .= "💬 *Balas dengan nomor untuk generate caption:*\n";
            $message .= "Contoh: \"1\" untuk ide pertama\n\n";
            $message .= "_Powered by Pintar Menulis AI_ ✨";

            return $this->sendMessage($target, $message);

        } catch (Exception $e) {
            Log::error('WhatsApp daily content ideas failed', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);

            return $this->sendMessage($target, "❌ Gagal mengambil ide konten hari ini. Coba lagi nanti ya!");
        }
    }
}