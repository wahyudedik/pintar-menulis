<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use App\Services\GeminiService;
use App\Services\WhatsAppUserIntegrationService;
use App\Services\WhatsAppSubscriptionService;
use App\Services\MultiLanguageService;
use App\Models\User;
use App\Models\CaptionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class WhatsAppController extends Controller
{
    private $whatsappService;
    private $geminiService;
    private $userIntegrationService;
    private $subscriptionService;
    private $multiLanguageService;

    public function __construct(
        WhatsAppService $whatsappService, 
        GeminiService $geminiService,
        WhatsAppUserIntegrationService $userIntegrationService,
        WhatsAppSubscriptionService $subscriptionService,
        MultiLanguageService $multiLanguageService
    ) {
        $this->whatsappService = $whatsappService;
        $this->geminiService = $geminiService;
        $this->userIntegrationService = $userIntegrationService;
        $this->subscriptionService = $subscriptionService;
        $this->multiLanguageService = $multiLanguageService;
    }

    /**
     * 🎯 Handle incoming WhatsApp webhook
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Validate webhook signature
            $signature = $request->header('X-Hub-Signature-256');
            if ($signature && !$this->whatsappService->validateWebhook($signature, $request->getContent())) {
                Log::warning('Invalid WhatsApp webhook signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $data = $request->all();
            Log::info('WhatsApp webhook received', $data);

            // Extract message data
            $from = $data['from'] ?? null;
            $message = $data['message'] ?? '';
            $messageType = $data['type'] ?? 'text';
            $mediaUrl = $data['url'] ?? null;

            if (!$from) {
                return response()->json(['error' => 'No sender information'], 400);
            }

            // Process different message types
            switch ($messageType) {
                case 'text':
                    $this->handleTextMessage($from, $message);
                    break;
                
                case 'image':
                    $this->handleImageMessage($from, $mediaUrl, $message);
                    break;
                
                case 'audio':
                case 'voice':
                    $this->handleVoiceMessage($from, $mediaUrl);
                    break;
                
                default:
                    $this->sendHelpMessage($from);
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * 💬 Handle text messages
     */
    private function handleTextMessage(string $from, string $message): void
    {
        $message = trim(strtolower($message));

        // Command routing
        switch (true) {
            case $message === 'help' || $message === 'bantuan':
                $this->sendHelpMessage($from);
                break;

            case $message === 'menu':
                $this->sendMenuMessage($from);
                break;

            case $message === 'daily' || $message === 'ide':
                $this->sendDailyIdeas($from);
                break;

            case str_starts_with($message, 'caption '):
                $prompt = substr($message, 8); // Remove 'caption '
                $this->generateCaption($from, $prompt);
                break;

            case str_starts_with($message, 'video '):
                $prompt = substr($message, 6); // Remove 'video '
                $this->generateVideoIdeas($from, $prompt);
                break;

            case $message === 'status':
                $this->sendAccountStatus($from);
                break;

            case is_numeric($message) && (int)$message >= 1 && (int)$message <= 4:
                $this->generateContentFromIdea($from, (int)$message);
                break;

            default:
                // Treat as caption generation prompt
                $this->generateCaption($from, $message);
                break;
        }
    }

    /**
     * 🖼️ Handle image messages
     */
    private function handleImageMessage(string $from, ?string $mediaUrl, string $caption = ''): void
    {
        if (!$mediaUrl) {
            $this->whatsappService->sendMessage($from, "❌ Gagal mengunduh gambar. Coba kirim ulang ya!");
            return;
        }

        try {
            // Download and store image
            $imagePath = $this->whatsappService->downloadMedia($mediaUrl);
            if (!$imagePath) {
                $this->whatsappService->sendMessage($from, "❌ Gagal mengunduh gambar. Coba kirim ulang ya!");
                return;
            }

            // Send processing message
            $this->whatsappService->sendMessage($from, "🔄 Sedang menganalisis gambar dan generate caption... Tunggu sebentar ya!");

            // Get full image path
            $fullPath = storage_path('app/public/' . $imagePath);
            $imageData = base64_encode(file_get_contents($fullPath));
            $mimeType = mime_content_type($fullPath);

            // Generate image caption using Gemini Vision
            $result = $this->geminiService->analyzeImageWithVision([
                'image_data' => $imageData,
                'mime_type' => $mimeType,
                'analysis_types' => ['caption_generation', 'hashtag_suggestions', 'engagement_tips'],
                'context' => $caption ?: 'Generate caption untuk social media UMKM Indonesia',
                'platform' => 'instagram'
            ]);

            if ($result && isset($result['caption'])) {
                $message = "📸 *AI Image Caption*\n\n";
                $message .= $result['caption'] . "\n\n";
                
                if (isset($result['hashtags'])) {
                    $message .= "🏷️ *Hashtag Suggestions:*\n";
                    $message .= implode(' ', array_slice($result['hashtags'], 0, 10)) . "\n\n";
                }
                
                if (isset($result['engagement_tips'])) {
                    $message .= "💡 *Tips Engagement:*\n";
                    foreach (array_slice($result['engagement_tips'], 0, 3) as $tip) {
                        $message .= "• " . $tip . "\n";
                    }
                    $message .= "\n";
                }
                
                $message .= "_Powered by Noteds AI_ ✨";
                
                $this->whatsappService->sendMessage($from, $message);
            } else {
                $this->whatsappService->sendMessage($from, "❌ Gagal menganalisis gambar. Coba kirim gambar yang lebih jelas ya!");
            }

            // Clean up temporary file
            Storage::disk('public')->delete($imagePath);

        } catch (Exception $e) {
            Log::error('WhatsApp image processing failed', [
                'from' => $from,
                'error' => $e->getMessage()
            ]);

            $this->whatsappService->sendMessage($from, "❌ Terjadi kesalahan saat memproses gambar. Tim kami sedang memperbaiki.");
        }
    }

    /**
     * 🎵 Handle voice messages
     */
    private function handleVoiceMessage(string $from, ?string $mediaUrl): void
    {
        if (!$mediaUrl) {
            $this->whatsappService->sendMessage($from, "❌ Gagal mengunduh voice note. Coba kirim ulang ya!");
            return;
        }

        // Process voice message with speech-to-text
        $this->whatsappService->processVoiceMessage($from, $mediaUrl);
    }

    /**
     * 📋 Send help message
     */
    private function sendHelpMessage(string $from): void
    {
        $message = "🤖 *Noteds AI - WhatsApp Bot*\n\n";
        $message .= "📝 *Cara Pakai:*\n";
        $message .= "• Ketik pesan → Dapet caption AI\n";
        $message .= "• Kirim foto → Dapet caption + hashtag\n";
        $message .= "• Kirim voice note → Dapet caption (soon)\n\n";
        
        $message .= "🎯 *Perintah Khusus:*\n";
        $message .= "• `menu` - Lihat semua fitur\n";
        $message .= "• `daily` - Ide konten harian\n";
        $message .= "• `caption [topik]` - Generate caption\n";
        $message .= "• `video [topik]` - Ide video content\n";
        $message .= "• `status` - Cek akun kamu\n";
        $message .= "• `help` - Bantuan ini\n\n";
        
        $message .= "💡 *Contoh:*\n";
        $message .= "\"caption produk kecantikan untuk remaja\"\n";
        $message .= "\"video tutorial masak nasi goreng\"\n\n";
        
        $message .= "_Powered by Noteds AI_ ✨";

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * 📱 Send menu message
     */
    private function sendMenuMessage(string $from): void
    {
        $message = "📱 *Menu Noteds AI*\n\n";
        
        $message .= "🎯 *Generator Content:*\n";
        $message .= "1️⃣ Caption Instagram/Facebook\n";
        $message .= "2️⃣ Caption TikTok/Reels\n";
        $message .= "3️⃣ Thread Twitter/X\n";
        $message .= "4️⃣ Video Script & Ideas\n\n";
        
        $message .= "📸 *Fitur Gambar:*\n";
        $message .= "• Kirim foto → Auto caption\n";
        $message .= "• Analisis visual produk\n";
        $message .= "• Hashtag suggestions\n";
        $message .= "• Engagement tips\n\n";
        
        $message .= "📅 *Daily Features:*\n";
        $message .= "• Ide konten harian\n";
        $message .= "• Trending topics\n";
        $message .= "• Seasonal content\n";
        $message .= "• Reminder otomatis\n\n";
        
        $message .= "💬 *Cara Pakai:*\n";
        $message .= "Langsung ketik aja topik yang mau dibuatin caption!\n\n";
        $message .= "_Ketik `help` untuk panduan lengkap_ 📖";

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * 💡 Send daily content ideas
     */
    private function sendDailyIdeas(string $from): void
    {
        $this->whatsappService->sendDailyContentIdeas($from, [
            'business_type' => 'UMKM',
            'target_audience' => 'Gen Z Indonesia'
        ]);
    }

    /**
     * ✨ Generate caption from prompt
     */
    private function generateCaption(string $from, string $prompt): void
    {
        if (empty(trim($prompt))) {
            $this->whatsappService->sendMessage($from, "❌ Prompt kosong. Coba ketik topik yang mau dibuatin caption ya!\n\nContoh: \"produk skincare untuk remaja\"");
            return;
        }

        // Send processing message
        $this->whatsappService->sendMessage($from, "🤖 Sedang generate caption... Tunggu sebentar ya!");

        $this->whatsappService->sendAICaption($from, $prompt, [
            'platform' => 'instagram',
            'tone' => 'engaging',
            'target_audience' => 'Gen Z Indonesia',
            'hashtag_count' => 10
        ]);
    }

    /**
     * 🎬 Generate video content ideas
     */
    private function generateVideoIdeas(string $from, string $prompt): void
    {
        if (empty(trim($prompt))) {
            $this->whatsappService->sendMessage($from, "❌ Prompt kosong. Coba ketik topik video yang mau dibuat!\n\nContoh: \"tutorial masak nasi goreng\"");
            return;
        }

        try {
            // Send processing message
            $this->whatsappService->sendMessage($from, "🎬 Sedang generate ide video... Tunggu sebentar ya!");

            $result = $this->geminiService->generateVideoContent([
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
                $message .= "_Powered by Noteds AI_ ✨";
                
                $this->whatsappService->sendMessage($from, $message);
            } else {
                $this->whatsappService->sendMessage($from, "❌ Gagal generate ide video. Coba dengan topik yang lebih spesifik ya!");
            }

        } catch (Exception $e) {
            Log::error('WhatsApp video ideas generation failed', [
                'from' => $from,
                'prompt' => $prompt,
                'error' => $e->getMessage()
            ]);

            $this->whatsappService->sendMessage($from, "❌ Terjadi kesalahan sistem. Tim kami sedang memperbaiki.");
        }
    }

    /**
     * 📊 Send account status
     */
    private function sendAccountStatus(string $from): void
    {
        // For now, send general status
        // TODO: Implement user account linking
        $message = "📊 *Status Akun*\n\n";
        $message .= "📱 WhatsApp: Terhubung ✅\n";
        $message .= "🤖 AI Engine: Online ✅\n";
        $message .= "⚡ Response Time: < 5 detik\n";
        $message .= "🔄 Last Update: " . date('d M Y H:i') . "\n\n";
        
        $message .= "💡 *Tips:*\n";
        $message .= "• Daftar di website untuk fitur lengkap\n";
        $message .= "• Simpan nomor ini untuk akses cepat\n";
        $message .= "• Gunakan perintah `daily` untuk ide harian\n\n";
        
        $message .= "🌐 *Website:* pintar-menulis.test\n";
        $message .= "_Upgrade ke Premium untuk fitur unlimited_ 🚀";

        $this->whatsappService->sendMessage($from, $message);
    }

    /**
     * 🎯 Generate content from daily idea number
     */
    private function generateContentFromIdea(string $from, int $ideaNumber): void
    {
        $ideas = [
            1 => "Share pengalaman unik dalam menjalankan bisnis hari ini",
            2 => "Tips praktis yang bisa langsung diterapkan customer",
            3 => "Behind the scenes proses kerja atau produksi",
            4 => "Statistik atau fakta menarik tentang industri"
        ];

        $prompt = $ideas[$ideaNumber] ?? $ideas[1];
        
        $this->whatsappService->sendMessage($from, "🎯 Generate caption untuk ide #$ideaNumber...");
        $this->generateCaption($from, $prompt);
    }

    /**
     * 📤 API endpoint to send WhatsApp message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'target' => 'required|string',
            'message' => 'required|string',
            'delay' => 'nullable|integer|min:0|max:300'
        ]);

        $result = $this->whatsappService->sendMessage(
            $request->target,
            $request->message,
            ['delay' => $request->delay ?? 0]
        );

        return response()->json($result);
    }

    /**
     * 📊 Get WhatsApp device status
     */
    public function getStatus()
    {
        $status = $this->whatsappService->getDeviceStatus();
        return response()->json($status);
    }

    /**
     * 📢 Send broadcast message
     */
    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'targets' => 'required|array',
            'targets.*' => 'string',
            'message' => 'required|string',
            'delay' => 'nullable|integer|min:1|max:60'
        ]);

        $result = $this->whatsappService->sendTemplate(
            $request->targets,
            $request->message,
            ['delay' => $request->delay ?? 2]
        );

        return response()->json($result);
    }

    /**
     * 🔗 Link WhatsApp account to user
     */
    public function linkAccount(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|regex:/^[0-9+\-\s]+$/'
        ]);

        $result = $this->userIntegrationService->linkUserAccount(
            $request->whatsapp_number,
            auth()->id()
        );

        return response()->json($result);
    }

    /**
     * ✅ Verify WhatsApp account
     */
    public function verifyAccount(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6'
        ]);

        $result = $this->userIntegrationService->verifyWhatsAppAccount(
            auth()->id(),
            $request->verification_code
        );

        return response()->json($result);
    }

    /**
     * 📊 Get user WhatsApp analytics
     */
    public function getUserAnalytics(User $user)
    {
        $analytics = $this->userIntegrationService->getUserEngagementAnalytics($user->id);
        return response()->json($analytics);
    }

    /**
     * 📋 Get user subscription
     */
    public function getSubscription()
    {
        $user = auth()->user();
        
        if (!$user->whatsapp_number) {
            return response()->json(['error' => 'WhatsApp not linked'], 404);
        }

        $subscription = $user->whatsappSubscription;
        
        return response()->json([
            'subscription' => $subscription,
            'preferences' => $user->getWhatsAppPreferences(),
            'activity' => $user->getWhatsAppActivitySummary()
        ]);
    }

    /**
     * ⚙️ Update subscription preferences
     */
    public function updateSubscription(Request $request)
    {
        $request->validate([
            'daily_content' => 'boolean',
            'weekly_reminder' => 'boolean',
            'trending_notifications' => 'boolean',
            'promotional_messages' => 'boolean',
            'business_type' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'preferred_time' => 'nullable|date_format:H:i',
            'language' => 'nullable|string'
        ]);

        $user = auth()->user();
        
        if (!$user->whatsapp_number) {
            return response()->json(['error' => 'WhatsApp not linked'], 404);
        }

        $user->updateWhatsAppPreferences($request->only([
            'daily_content',
            'weekly_reminder', 
            'trending_notifications',
            'promotional_messages',
            'business_type',
            'target_audience',
            'preferred_time',
            'language'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully',
            'preferences' => $user->getWhatsAppPreferences()
        ]);
    }

    /**
     * 🗑️ Delete subscription
     */
    public function deleteSubscription()
    {
        $user = auth()->user();
        
        if (!$user->whatsapp_number) {
            return response()->json(['error' => 'WhatsApp not linked'], 404);
        }

        // Unsubscribe from WhatsApp
        if ($user->whatsappSubscription) {
            $user->whatsappSubscription->unsubscribe();
        }

        // Update user preferences
        $user->update([
            'whatsapp_notifications_enabled' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from WhatsApp notifications'
        ]);
    }
}