<?php

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSubscription;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppUserIntegrationService
{
    private $whatsappService;
    private $multiLanguageService;

    public function __construct(WhatsAppService $whatsappService, MultiLanguageService $multiLanguageService)
    {
        $this->whatsappService = $whatsappService;
        $this->multiLanguageService = $multiLanguageService;
    }

    /**
     * 🔗 Link WhatsApp number to user account
     */
    public function linkUserAccount(string $phoneNumber, int $userId): array
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }

            // Check if phone number is already linked to another user
            $existingUser = User::where('whatsapp_number', $phoneNumber)
                ->where('id', '!=', $userId)
                ->first();

            if ($existingUser) {
                return [
                    'success' => false, 
                    'message' => 'WhatsApp number already linked to another account'
                ];
            }

            // Link WhatsApp to user
            $user->linkWhatsApp($phoneNumber);

            // Send verification code
            $verificationCode = $user->generateWhatsAppVerificationCode();
            
            $message = "🔐 *Verifikasi WhatsApp*\n\n";
            $message .= "Kode verifikasi untuk menghubungkan akun:\n\n";
            $message .= "**{$verificationCode}**\n\n";
            $message .= "Masukkan kode ini di website untuk menyelesaikan verifikasi.\n\n";
            $message .= "Kode berlaku selama 10 menit.\n\n";
            $message .= "_Powered by Noteds AI_ ✨";

            $result = $this->whatsappService->sendMessage($phoneNumber, $message);

            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Verification code sent to WhatsApp',
                    'verification_code' => $verificationCode // For testing only
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send verification code'
                ];
            }

        } catch (Exception $e) {
            Log::error('WhatsApp user linking failed', [
                'phone' => $phoneNumber,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to link WhatsApp account'
            ];
        }
    }

    /**
     * ✅ Verify WhatsApp account with code
     */
    public function verifyWhatsAppAccount(int $userId, string $code): array
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }

            if ($user->verifyWhatsApp($code)) {
                // Send welcome message
                $this->sendWelcomeMessage($user);

                return [
                    'success' => true,
                    'message' => 'WhatsApp account verified successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid verification code'
                ];
            }

        } catch (Exception $e) {
            Log::error('WhatsApp verification failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Verification failed'
            ];
        }
    }

    /**
     * 👋 Send welcome message to newly verified user
     */
    private function sendWelcomeMessage(User $user): void
    {
        if (!$user->whatsapp_number) {
            return;
        }

        $preferences = $user->getWhatsAppPreferences();
        $language = $preferences['language'] ?? 'bahasa_indonesia';
        $messages = $this->multiLanguageService->getLocalizedMessages($language);

        $welcomeMessage = "🎉 *Selamat! Akun WhatsApp Terverifikasi*\n\n";
        $welcomeMessage .= "Halo {$user->name}! 👋\n\n";
        $welcomeMessage .= "Akun WhatsApp kamu sudah terhubung dengan Noteds AI.\n\n";
        
        $welcomeMessage .= "🎯 *Fitur yang bisa kamu gunakan:*\n";
        $welcomeMessage .= "• Generate caption AI langsung via WhatsApp\n";
        $welcomeMessage .= "• Kirim foto untuk analisis otomatis\n";
        $welcomeMessage .= "• Terima ide konten harian\n";
        $welcomeMessage .= "• Sinkronisasi dengan akun website\n\n";
        
        $welcomeMessage .= "⚙️ *Pengaturan Notifikasi:*\n";
        $welcomeMessage .= "• Konten harian: " . ($preferences['daily_content'] ? '✅' : '❌') . "\n";
        $welcomeMessage .= "• Reminder mingguan: " . ($preferences['weekly_reminder'] ? '✅' : '❌') . "\n";
        $welcomeMessage .= "• Trending topics: " . ($preferences['trending_notifications'] ? '✅' : '❌') . "\n\n";
        
        $welcomeMessage .= "💡 Ketik `pengaturan` untuk mengubah preferensi\n";
        $welcomeMessage .= "💡 Ketik `bantuan` untuk panduan lengkap\n\n";
        
        $welcomeMessage .= "Selamat berkreasi! 🚀✨";

        $this->whatsappService->sendMessage($user->whatsapp_number, $welcomeMessage);
    }

    /**
     * 🔍 Find user by WhatsApp number
     */
    public function findUserByWhatsApp(string $phoneNumber): ?User
    {
        return User::where('whatsapp_number', $phoneNumber)
            ->where('whatsapp_verified', true)
            ->first();
    }

    /**
     * 📊 Sync user data with WhatsApp interactions
     */
    public function syncUserData(string $phoneNumber): void
    {
        try {
            $user = $this->findUserByWhatsApp($phoneNumber);
            if (!$user) {
                return;
            }

            // Update last interaction
            $user->updateWhatsAppInteraction();

            // Sync subscription data
            $subscription = WhatsAppSubscription::where('phone_number', $phoneNumber)->first();
            if ($subscription && !$subscription->user_id) {
                $subscription->update(['user_id' => $user->id]);
            }

            // Update user preferences from subscription
            if ($subscription) {
                $subscriptionPrefs = $subscription->getPreferences();
                $user->updateWhatsAppPreferences($subscriptionPrefs);
            }

        } catch (Exception $e) {
            Log::error('User data sync failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 📱 Send personalized message based on user data
     */
    public function sendPersonalizedMessage(string $phoneNumber, string $messageType, array $data = []): array
    {
        try {
            $user = $this->findUserByWhatsApp($phoneNumber);
            $preferences = $user ? $user->getWhatsAppPreferences() : [];
            $language = $preferences['language'] ?? 'bahasa_indonesia';

            $message = $this->buildPersonalizedMessage($messageType, $data, $user, $language);
            
            return $this->whatsappService->sendMessage($phoneNumber, $message);

        } catch (Exception $e) {
            Log::error('Personalized message failed', [
                'phone' => $phoneNumber,
                'type' => $messageType,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'message' => 'Failed to send personalized message'];
        }
    }

    /**
     * 🎯 Build personalized message content
     */
    private function buildPersonalizedMessage(string $type, array $data, ?User $user, string $language): string
    {
        $messages = $this->multiLanguageService->getLocalizedMessages($language);
        $userName = $user ? $user->name : 'Sobat';

        switch ($type) {
            case 'daily_content':
                $message = "🌅 *Selamat Pagi, {$userName}!*\n\n";
                $message .= $this->multiLanguageService->getDailyIdeasMessage($language);
                
                if ($user && $user->getWhatsAppPreferences()['business_type']) {
                    $businessType = $user->getWhatsAppPreferences()['business_type'];
                    $message .= "\n💼 *Khusus untuk bisnis {$businessType}:*\n";
                    $message .= $this->getBusinessSpecificTips($businessType);
                }
                break;

            case 'weekly_reminder':
                $message = "📅 *Reminder Mingguan untuk {$userName}*\n\n";
                $message .= "Sudah siap konten untuk minggu ini?\n\n";
                
                if ($user) {
                    $activity = $user->getWhatsAppActivitySummary();
                    $message .= "📊 *Aktivitas Minggu Lalu:*\n";
                    $message .= "• Total pesan: {$activity['total_messages']}\n";
                    $message .= "• Caption dibuat: " . ($activity['messages_received'] ?? 0) . "\n\n";
                }
                
                $message .= "💡 Ketik `daily` untuk ide konten hari ini!";
                break;

            case 'account_linked':
                $message = "🔗 *Akun Terhubung, {$userName}!*\n\n";
                $message .= "WhatsApp kamu sudah terhubung dengan akun website.\n\n";
                $message .= "Sekarang kamu bisa:\n";
                $message .= "• Akses riwayat caption di website\n";
                $message .= "• Sinkronisasi preferensi\n";
                $message .= "• Backup otomatis konten\n\n";
                $message .= "🌐 Login di: " . config('app.url');
                break;

            default:
                $message = "Halo {$userName}! " . ($data['message'] ?? 'Pesan khusus untuk kamu.');
                break;
        }

        return $message;
    }

    /**
     * 💼 Get business-specific tips
     */
    private function getBusinessSpecificTips(string $businessType): string
    {
        $tips = [
            'fashion' => "• Showcase outfit of the day\n• Behind the scenes fitting\n• Style tips untuk customer",
            'food' => "• Recipe sharing dengan twist\n• Food photography tips\n• Customer testimonial makanan",
            'beauty' => "• Before/after transformation\n• Skincare routine tutorial\n• Product review honest",
            'technology' => "• Tech tips dan tricks\n• Product comparison\n• Tutorial penggunaan",
            'default' => "• Share pengalaman unik\n• Tips praktis untuk customer\n• Behind the scenes bisnis"
        ];

        return $tips[$businessType] ?? $tips['default'];
    }

    /**
     * 📈 Get user engagement analytics
     */
    public function getUserEngagementAnalytics(int $userId): array
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->whatsapp_number) {
                return ['error' => 'User not found or WhatsApp not linked'];
            }

            $messages = WhatsAppMessage::where('phone_number', $user->whatsapp_number)
                ->where('created_at', '>=', now()->subDays(30))
                ->get();

            $dailyActivity = $messages->groupBy(function ($message) {
                return $message->created_at->format('Y-m-d');
            })->map(function ($dayMessages) {
                return [
                    'total' => $dayMessages->count(),
                    'incoming' => $dayMessages->where('direction', 'incoming')->count(),
                    'outgoing' => $dayMessages->where('direction', 'outgoing')->count()
                ];
            });

            $messageTypes = $messages->groupBy('message_type')->map->count();

            return [
                'user_info' => [
                    'name' => $user->name,
                    'whatsapp_number' => $user->formatted_whats_app,
                    'verified_at' => $user->whatsapp_verified_at,
                    'last_interaction' => $user->last_whatsapp_interaction
                ],
                'activity_summary' => $user->getWhatsAppActivitySummary(),
                'daily_activity' => $dailyActivity,
                'message_types' => $messageTypes,
                'preferences' => $user->getWhatsAppPreferences()
            ];

        } catch (Exception $e) {
            Log::error('User engagement analytics failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return ['error' => 'Failed to get analytics'];
        }
    }

    /**
     * 🔄 Bulk sync all users with WhatsApp data
     */
    public function bulkSyncUsers(): array
    {
        try {
            $syncedCount = 0;
            $errorCount = 0;

            // Get all WhatsApp messages with phone numbers that might belong to users
            $phoneNumbers = WhatsAppMessage::distinct('phone_number')
                ->whereNotNull('phone_number')
                ->pluck('phone_number');

            foreach ($phoneNumbers as $phoneNumber) {
                try {
                    $this->syncUserData($phoneNumber);
                    $syncedCount++;
                } catch (Exception $e) {
                    $errorCount++;
                    Log::warning('Individual user sync failed', [
                        'phone' => $phoneNumber,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Bulk user sync completed', [
                'synced_count' => $syncedCount,
                'error_count' => $errorCount
            ]);

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'error_count' => $errorCount
            ];

        } catch (Exception $e) {
            Log::error('Bulk user sync failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Bulk sync failed'
            ];
        }
    }
}