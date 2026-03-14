<?php

namespace App\Jobs;

use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use App\Services\GeminiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    private $messageData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $whatsappService = app(WhatsAppService::class);
            $geminiService = app(GeminiService::class);

            $from = $this->messageData['from'];
            $message = $this->messageData['message'] ?? '';
            $messageType = $this->messageData['type'] ?? 'text';
            $mediaUrl = $this->messageData['url'] ?? null;

            // Record incoming message
            $whatsappMessage = WhatsAppMessage::recordIncoming([
                'phone_number' => $from,
                'message_type' => $messageType,
                'message_content' => $message,
                'media_url' => $mediaUrl,
                'external_id' => $this->messageData['id'] ?? null,
                'metadata' => [
                    'webhook_data' => $this->messageData,
                    'processed_by_job' => true
                ]
            ]);

            // Process based on message type
            switch ($messageType) {
                case 'text':
                    $this->processTextMessage($whatsappService, $geminiService, $from, $message, $whatsappMessage);
                    break;
                
                case 'image':
                    $this->processImageMessage($whatsappService, $geminiService, $from, $mediaUrl, $message, $whatsappMessage);
                    break;
                
                case 'audio':
                case 'voice':
                    $this->processVoiceMessage($whatsappService, $from, $mediaUrl, $whatsappMessage);
                    break;
                
                default:
                    $this->sendHelpMessage($whatsappService, $from, $whatsappMessage);
                    break;
            }

            $whatsappMessage->markAsProcessed();

        } catch (Exception $e) {
            Log::error('WhatsApp message processing job failed', [
                'message_data' => $this->messageData,
                'error' => $e->getMessage()
            ]);

            // Send error message to user
            if (isset($from)) {
                $whatsappService = app(WhatsAppService::class);
                $whatsappService->sendMessage($from, "❌ Terjadi kesalahan sistem. Tim kami sedang memperbaiki. Coba lagi dalam beberapa menit ya!");
            }
        }
    }

    private function processTextMessage(WhatsAppService $whatsappService, GeminiService $geminiService, string $from, string $message, WhatsAppMessage $whatsappMessage): void
    {
        $message = trim(strtolower($message));

        // Command routing
        switch (true) {
            case $message === 'help' || $message === 'bantuan':
                $this->sendHelpMessage($whatsappService, $from, $whatsappMessage);
                break;

            case $message === 'menu':
                $this->sendMenuMessage($whatsappService, $from, $whatsappMessage);
                break;

            case $message === 'daily' || $message === 'ide':
                $this->sendDailyIdeas($whatsappService, $from, $whatsappMessage);
                break;

            case str_starts_with($message, 'caption '):
                $prompt = substr($message, 8);
                $this->generateCaption($whatsappService, $geminiService, $from, $prompt, $whatsappMessage);
                break;

            case str_starts_with($message, 'video '):
                $prompt = substr($message, 6);
                $this->generateVideoIdeas($whatsappService, $geminiService, $from, $prompt, $whatsappMessage);
                break;

            case $message === 'status':
                $this->sendAccountStatus($whatsappService, $from, $whatsappMessage);
                break;

            case is_numeric($message) && (int)$message >= 1 && (int)$message <= 4:
                $this->generateContentFromIdea($whatsappService, $geminiService, $from, (int)$message, $whatsappMessage);
                break;

            default:
                // Treat as caption generation prompt
                $this->generateCaption($whatsappService, $geminiService, $from, $message, $whatsappMessage);
                break;
        }
    }

    private function processImageMessage(WhatsAppService $whatsappService, GeminiService $geminiService, string $from, ?string $mediaUrl, string $caption, WhatsAppMessage $whatsappMessage): void
    {
        if (!$mediaUrl) {
            $whatsappService->sendMessage($from, "❌ Gagal mengunduh gambar. Coba kirim ulang ya!");
            return;
        }

        // Send processing message
        $whatsappService->sendMessage($from, "🔄 Sedang menganalisis gambar dan generate caption... Tunggu sebentar ya!");

        // Download and analyze image
        $imagePath = $whatsappService->downloadMedia($mediaUrl);
        if (!$imagePath) {
            $whatsappService->sendMessage($from, "❌ Gagal mengunduh gambar. Coba kirim ulang ya!");
            return;
        }

        try {
            $fullPath = storage_path('app/public/' . $imagePath);
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = mime_content_type($fullPath);

            $result = $geminiService->analyzeImageWithVision([
                'image_data' => $imageData,
                'mime_type' => $mimeType,
                'analysis_types' => ['caption_generation', 'hashtag_suggestions', 'engagement_tips'],
                'context' => $caption ?: 'Generate caption untuk social media UMKM Indonesia',
                'platform' => 'instagram'
            ]);

            if ($result && isset($result['caption'])) {
                $responseMessage = "📸 *AI Image Caption*\n\n";
                $responseMessage .= $result['caption'] . "\n\n";
                
                if (isset($result['hashtags'])) {
                    $responseMessage .= "🏷️ *Hashtag Suggestions:*\n";
                    $responseMessage .= implode(' ', array_slice($result['hashtags'], 0, 10)) . "\n\n";
                }
                
                $responseMessage .= "_Powered by Pintar Menulis AI_ ✨";
                
                $whatsappService->sendMessage($from, $responseMessage);

                // Record the generated caption
                $whatsappMessage->update([
                    'caption_history_id' => null, // TODO: Create caption history record
                    'metadata' => array_merge($whatsappMessage->metadata ?? [], [
                        'ai_result' => $result,
                        'image_processed' => true
                    ])
                ]);
            } else {
                $whatsappService->sendMessage($from, "❌ Gagal menganalisis gambar. Coba kirim gambar yang lebih jelas ya!");
            }

            // Clean up
            \Storage::disk('public')->delete($imagePath);

        } catch (Exception $e) {
            Log::error('WhatsApp image processing failed in job', [
                'from' => $from,
                'error' => $e->getMessage()
            ]);
            $whatsappService->sendMessage($from, "❌ Terjadi kesalahan saat memproses gambar.");
        }
    }

    private function processVoiceMessage(WhatsAppService $whatsappService, string $from, ?string $mediaUrl, WhatsAppMessage $whatsappMessage): void
    {
        $message = "🎵 *Voice Note Diterima*\n\n";
        $message .= "Fitur speech-to-text sedang dalam pengembangan.\n";
        $message .= "Sementara ini, silakan ketik pesan text untuk generate caption.\n\n";
        $message .= "_Coming Soon: Voice to Caption_ 🚀";

        $whatsappService->sendMessage($from, $message);
    }

    private function generateCaption(WhatsAppService $whatsappService, GeminiService $geminiService, string $from, string $prompt, WhatsAppMessage $whatsappMessage): void
    {
        if (empty(trim($prompt))) {
            $whatsappService->sendMessage($from, "❌ Prompt kosong. Coba ketik topik yang mau dibuatin caption ya!");
            return;
        }

        $whatsappService->sendAICaption($from, $prompt, [
            'platform' => 'instagram',
            'tone' => 'engaging',
            'target_audience' => 'Gen Z Indonesia',
            'hashtag_count' => 10
        ]);
    }

    private function generateVideoIdeas(WhatsAppService $whatsappService, GeminiService $geminiService, string $from, string $prompt, WhatsAppMessage $whatsappMessage): void
    {
        if (empty(trim($prompt))) {
            $whatsappService->sendMessage($from, "❌ Prompt kosong. Coba ketik topik video yang mau dibuat!");
            return;
        }

        try {
            $result = $geminiService->generateVideoContent([
                'content_type' => 'ideas',
                'platform' => 'tiktok',
                'duration' => 30,
                'product' => $prompt,
                'target_audience' => 'Gen Z Indonesia',
                'goal' => 'viral',
                'styles' => ['trending', 'engaging'],
                'context' => 'UMKM Indonesia'
            ]);

            if ($result && isset($result['content'])) {
                $message = "🎬 *AI Video Ideas*\n\n";
                $message .= $result['content'] . "\n\n";
                $message .= "_Powered by Pintar Menulis AI_ ✨";
                
                $whatsappService->sendMessage($from, $message);
            } else {
                $whatsappService->sendMessage($from, "❌ Gagal generate ide video. Coba dengan topik yang lebih spesifik ya!");
            }

        } catch (Exception $e) {
            Log::error('WhatsApp video ideas generation failed in job', [
                'from' => $from,
                'prompt' => $prompt,
                'error' => $e->getMessage()
            ]);
            $whatsappService->sendMessage($from, "❌ Terjadi kesalahan sistem.");
        }
    }

    private function sendHelpMessage(WhatsAppService $whatsappService, string $from, WhatsAppMessage $whatsappMessage): void
    {
        $message = "🤖 *Pintar Menulis AI - WhatsApp Bot*\n\n";
        $message .= "📝 *Cara Pakai:*\n";
        $message .= "• Ketik pesan → Dapet caption AI\n";
        $message .= "• Kirim foto → Dapet caption + hashtag\n";
        $message .= "• Kirim voice note → Dapet caption (soon)\n\n";
        $message .= "🎯 *Perintah:*\n";
        $message .= "• `menu` - Lihat semua fitur\n";
        $message .= "• `daily` - Ide konten harian\n";
        $message .= "• `help` - Bantuan ini\n\n";
        $message .= "_Powered by Pintar Menulis AI_ ✨";

        $whatsappService->sendMessage($from, $message);
    }

    private function sendMenuMessage(WhatsAppService $whatsappService, string $from, WhatsAppMessage $whatsappMessage): void
    {
        $message = "📱 *Menu Pintar Menulis AI*\n\n";
        $message .= "🎯 *Generator Content:*\n";
        $message .= "1️⃣ Caption Instagram/Facebook\n";
        $message .= "2️⃣ Caption TikTok/Reels\n";
        $message .= "3️⃣ Thread Twitter/X\n";
        $message .= "4️⃣ Video Script & Ideas\n\n";
        $message .= "💬 Langsung ketik aja topik yang mau dibuatin caption!";

        $whatsappService->sendMessage($from, $message);
    }

    private function sendDailyIdeas(WhatsAppService $whatsappService, string $from, WhatsAppMessage $whatsappMessage): void
    {
        $whatsappService->sendDailyContentIdeas($from, [
            'business_type' => 'UMKM',
            'target_audience' => 'Gen Z Indonesia'
        ]);
    }

    private function sendAccountStatus(WhatsAppService $whatsappService, string $from, WhatsAppMessage $whatsappMessage): void
    {
        $message = "📊 *Status Akun*\n\n";
        $message .= "📱 WhatsApp: Terhubung ✅\n";
        $message .= "🤖 AI Engine: Online ✅\n";
        $message .= "⚡ Response Time: < 5 detik\n\n";
        $message .= "🌐 *Website:* pintar-menulis.test";

        $whatsappService->sendMessage($from, $message);
    }

    private function generateContentFromIdea(WhatsAppService $whatsappService, GeminiService $geminiService, string $from, int $ideaNumber, WhatsAppMessage $whatsappMessage): void
    {
        $ideas = [
            1 => "Share pengalaman unik dalam menjalankan bisnis hari ini",
            2 => "Tips praktis yang bisa langsung diterapkan customer",
            3 => "Behind the scenes proses kerja atau produksi",
            4 => "Statistik atau fakta menarik tentang industri"
        ];

        $prompt = $ideas[$ideaNumber] ?? $ideas[1];
        $this->generateCaption($whatsappService, $geminiService, $from, $prompt, $whatsappMessage);
    }
}
