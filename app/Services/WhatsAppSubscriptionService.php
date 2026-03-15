<?php

namespace App\Services;

use App\Models\WhatsAppSubscription;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppSubscriptionService
{
    private $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * 📝 Handle subscription commands
     */
    public function handleSubscriptionCommand(string $phoneNumber, string $command): array
    {
        $command = strtolower(trim($command));

        switch ($command) {
            case 'subscribe':
            case 'langganan':
                return $this->subscribeUser($phoneNumber);

            case 'unsubscribe':
            case 'berhenti':
                return $this->unsubscribeUser($phoneNumber);

            case 'preferences':
            case 'pengaturan':
                return $this->showPreferences($phoneNumber);

            case 'settings':
            case 'setting':
                return $this->showSettingsMenu($phoneNumber);

            default:
                return $this->showSubscriptionHelp($phoneNumber);
        }
    }

    /**
     * ✅ Subscribe user to WhatsApp notifications
     */
    public function subscribeUser(string $phoneNumber, array $preferences = []): array
    {
        try {
            $subscription = WhatsAppSubscription::subscribe($phoneNumber, $preferences);

            $message = "✅ *Berhasil Berlangganan!*\n\n";
            $message .= "Kamu akan menerima:\n";
            $message .= "🌅 Ide konten harian (jam 8 pagi)\n";
            $message .= "📅 Reminder mingguan (Senin jam 9)\n";
            $message .= "🔥 Trending topics (Selasa & Jumat)\n\n";
            $message .= "💡 *Atur Preferensi:*\n";
            $message .= "Ketik `pengaturan` untuk customize notifikasi\n\n";
            $message .= "_Powered by Noteds AI_ ✨";

            return $this->whatsappService->sendMessage($phoneNumber, $message);

        } catch (Exception $e) {
            Log::error('WhatsApp subscription failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return $this->whatsappService->sendMessage($phoneNumber, "❌ Gagal berlangganan. Coba lagi nanti ya!");
        }
    }

    /**
     * ❌ Unsubscribe user from notifications
     */
    public function unsubscribeUser(string $phoneNumber): array
    {
        try {
            $subscription = WhatsAppSubscription::where('phone_number', $phoneNumber)->first();

            if (!$subscription) {
                return $this->whatsappService->sendMessage($phoneNumber, "ℹ️ Kamu belum berlangganan notifikasi.");
            }

            $subscription->unsubscribe();

            $message = "😢 *Berhasil Berhenti Berlangganan*\n\n";
            $message .= "Kamu tidak akan menerima notifikasi otomatis lagi.\n\n";
            $message .= "💬 Tapi kamu masih bisa:\n";
            $message .= "• Generate caption kapan saja\n";
            $message .= "• Kirim foto untuk analisis\n";
            $message .= "• Minta ide konten manual\n\n";
            $message .= "🔄 Ketik `langganan` untuk berlangganan lagi\n\n";
            $message .= "_Terima kasih sudah menggunakan Noteds AI_ 💙";

            return $this->whatsappService->sendMessage($phoneNumber, $message);

        } catch (Exception $e) {
            Log::error('WhatsApp unsubscription failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return $this->whatsappService->sendMessage($phoneNumber, "❌ Gagal berhenti berlangganan. Coba lagi nanti ya!");
        }
    }

    /**
     * 📋 Show current preferences
     */
    public function showPreferences(string $phoneNumber): array
    {
        $subscription = WhatsAppSubscription::where('phone_number', $phoneNumber)->first();

        if (!$subscription || !$subscription->is_active) {
            return $this->whatsappService->sendMessage($phoneNumber, "ℹ️ Kamu belum berlangganan. Ketik `langganan` untuk mulai!");
        }

        $prefs = $subscription->getPreferences();

        $message = "⚙️ *Pengaturan Langganan Kamu*\n\n";
        
        $message .= "📅 *Notifikasi:*\n";
        $message .= "• Konten harian: " . ($prefs['daily_content'] ? '✅' : '❌') . "\n";
        $message .= "• Reminder mingguan: " . ($prefs['weekly_reminder'] ? '✅' : '❌') . "\n";
        $message .= "• Trending topics: " . ($prefs['trending_notifications'] ? '✅' : '❌') . "\n";
        $message .= "• Promosi: " . ($prefs['promotional_messages'] ? '✅' : '❌') . "\n\n";
        
        $message .= "🎯 *Preferensi Konten:*\n";
        $message .= "• Bisnis: " . ($prefs['business_type'] ?: 'Belum diset') . "\n";
        $message .= "• Target audience: " . $prefs['target_audience'] . "\n";
        $message .= "• Platform: " . implode(', ', $prefs['platforms']) . "\n";
        $message .= "• Bahasa: " . $prefs['language'] . "\n\n";
        
        $message .= "⏰ *Waktu:*\n";
        $message .= "• Jam kirim: " . $prefs['preferred_time'] . "\n";
        $message .= "• Timezone: " . $prefs['timezone'] . "\n\n";
        
        $message .= "🔧 Ketik `setting` untuk mengubah pengaturan";

        return $this->whatsappService->sendMessage($phoneNumber, $message);
    }

    /**
     * ⚙️ Show settings menu
     */
    public function showSettingsMenu(string $phoneNumber): array
    {
        $message = "⚙️ *Menu Pengaturan*\n\n";
        $message .= "Pilih yang ingin diubah:\n\n";
        
        $message .= "📅 *Notifikasi:*\n";
        $message .= "1️⃣ `daily on/off` - Konten harian\n";
        $message .= "2️⃣ `trending on/off` - Trending topics\n";
        $message .= "3️⃣ `reminder on/off` - Reminder mingguan\n\n";
        
        $message .= "🎯 *Konten:*\n";
        $message .= "4️⃣ `bisnis [jenis]` - Set jenis bisnis\n";
        $message .= "5️⃣ `audience [target]` - Set target audience\n";
        $message .= "6️⃣ `bahasa [pilihan]` - Set bahasa\n\n";
        
        $message .= "⏰ *Waktu:*\n";
        $message .= "7️⃣ `waktu [HH:MM]` - Set jam kirim\n\n";
        
        $message .= "📋 *Contoh:*\n";
        $message .= "• `daily on` - Aktifkan konten harian\n";
        $message .= "• `bisnis fashion` - Set bisnis fashion\n";
        $message .= "• `waktu 09:00` - Set jam 9 pagi\n\n";
        
        $message .= "ℹ️ Ketik `pengaturan` untuk lihat setting saat ini";

        return $this->whatsappService->sendMessage($phoneNumber, $message);
    }

    /**
     * 🔧 Handle settings update
     */
    public function handleSettingsUpdate(string $phoneNumber, string $command): array
    {
        $subscription = WhatsAppSubscription::where('phone_number', $phoneNumber)->first();

        if (!$subscription) {
            return $this->subscribeUser($phoneNumber);
        }

        $parts = explode(' ', strtolower(trim($command)), 2);
        $action = $parts[0];
        $value = $parts[1] ?? '';

        try {
            switch ($action) {
                case 'daily':
                    return $this->updateNotificationSetting($subscription, 'daily_content', $value);

                case 'trending':
                    return $this->updateNotificationSetting($subscription, 'trending_notifications', $value);

                case 'reminder':
                    return $this->updateNotificationSetting($subscription, 'weekly_reminder', $value);

                case 'bisnis':
                case 'business':
                    return $this->updateBusinessType($subscription, $value);

                case 'audience':
                case 'target':
                    return $this->updateTargetAudience($subscription, $value);

                case 'bahasa':
                case 'language':
                    return $this->updateLanguage($subscription, $value);

                case 'waktu':
                case 'time':
                    return $this->updatePreferredTime($subscription, $value);

                default:
                    return $this->showSettingsMenu($phoneNumber);
            }

        } catch (Exception $e) {
            Log::error('Settings update failed', [
                'phone' => $phoneNumber,
                'command' => $command,
                'error' => $e->getMessage()
            ]);

            return $this->whatsappService->sendMessage($phoneNumber, "❌ Gagal update pengaturan. Format: `daily on/off`, `bisnis fashion`, dll.");
        }
    }

    /**
     * 🔔 Update notification setting
     */
    private function updateNotificationSetting(WhatsAppSubscription $subscription, string $setting, string $value): array
    {
        $enabled = in_array($value, ['on', 'aktif', 'ya', 'yes', '1', 'true']);
        $subscription->updatePreferences([$setting => $enabled]);

        $settingNames = [
            'daily_content' => 'Konten Harian',
            'trending_notifications' => 'Trending Topics',
            'weekly_reminder' => 'Reminder Mingguan'
        ];

        $settingName = $settingNames[$setting] ?? $setting;
        $status = $enabled ? 'diaktifkan' : 'dinonaktifkan';

        return $this->whatsappService->sendMessage(
            $subscription->phone_number,
            "✅ {$settingName} berhasil {$status}!"
        );
    }

    /**
     * 🏢 Update business type
     */
    private function updateBusinessType(WhatsAppSubscription $subscription, string $businessType): array
    {
        if (empty($businessType)) {
            return $this->whatsappService->sendMessage(
                $subscription->phone_number,
                "❌ Format: `bisnis [jenis]`\nContoh: `bisnis fashion`, `bisnis kuliner`, `bisnis kecantikan`"
            );
        }

        $subscription->updatePreferences(['business_type' => $businessType]);

        return $this->whatsappService->sendMessage(
            $subscription->phone_number,
            "✅ Jenis bisnis diupdate ke: {$businessType}\n\nKonten akan disesuaikan dengan bisnis {$businessType}!"
        );
    }

    /**
     * 🎯 Update target audience
     */
    private function updateTargetAudience(WhatsAppSubscription $subscription, string $audience): array
    {
        if (empty($audience)) {
            return $this->whatsappService->sendMessage(
                $subscription->phone_number,
                "❌ Format: `audience [target]`\nContoh: `audience remaja`, `audience ibu rumah tangga`"
            );
        }

        $subscription->updatePreferences(['target_audience' => $audience]);

        return $this->whatsappService->sendMessage(
            $subscription->phone_number,
            "✅ Target audience diupdate ke: {$audience}\n\nKonten akan disesuaikan untuk {$audience}!"
        );
    }

    /**
     * 🗣️ Update language preference
     */
    private function updateLanguage(WhatsAppSubscription $subscription, string $language): array
    {
        $languages = [
            'indonesia' => 'bahasa_indonesia',
            'jawa' => 'bahasa_jawa',
            'sunda' => 'bahasa_sunda',
            'bali' => 'bahasa_bali',
            'betawi' => 'bahasa_betawi',
            'english' => 'english',
            'mix' => 'mix_bahasa'
        ];

        $langCode = $languages[$language] ?? $language;

        $subscription->updatePreferences(['language' => $langCode]);

        return $this->whatsappService->sendMessage(
            $subscription->phone_number,
            "✅ Bahasa diupdate ke: {$language}\n\nKonten akan menggunakan {$language}!"
        );
    }

    /**
     * ⏰ Update preferred time
     */
    private function updatePreferredTime(WhatsAppSubscription $subscription, string $time): array
    {
        if (!preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $time)) {
            return $this->whatsappService->sendMessage(
                $subscription->phone_number,
                "❌ Format waktu salah!\nContoh: `waktu 08:00`, `waktu 14:30`"
            );
        }

        $subscription->updatePreferences(['preferred_time' => $time]);

        return $this->whatsappService->sendMessage(
            $subscription->phone_number,
            "✅ Waktu kirim diupdate ke: {$time}\n\nNotifikasi akan dikirim sekitar jam {$time}!"
        );
    }

    /**
     * ❓ Show subscription help
     */
    private function showSubscriptionHelp(string $phoneNumber): array
    {
        $message = "📱 *Panduan Langganan*\n\n";
        $message .= "🔔 *Perintah Utama:*\n";
        $message .= "• `langganan` - Berlangganan notifikasi\n";
        $message .= "• `berhenti` - Berhenti berlangganan\n";
        $message .= "• `pengaturan` - Lihat pengaturan\n";
        $message .= "• `setting` - Menu ubah pengaturan\n\n";
        
        $message .= "⚙️ *Pengaturan Cepat:*\n";
        $message .= "• `daily on/off` - Konten harian\n";
        $message .= "• `trending on/off` - Trending topics\n";
        $message .= "• `bisnis fashion` - Set jenis bisnis\n";
        $message .= "• `waktu 08:00` - Set jam kirim\n\n";
        
        $message .= "💡 Ketik `langganan` untuk mulai!";

        return $this->whatsappService->sendMessage($phoneNumber, $message);
    }

    /**
     * 📊 Get subscription statistics
     */
    public function getSubscriptionStats(): array
    {
        return WhatsAppSubscription::getStats();
    }

    /**
     * 🧹 Cleanup inactive subscriptions
     */
    public function cleanupInactiveSubscriptions(int $daysInactive = 90): int
    {
        try {
            $cutoffDate = now()->subDays($daysInactive);
            
            $inactiveCount = WhatsAppSubscription::where('last_interaction_at', '<', $cutoffDate)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'unsubscribed_at' => now()
                ]);

            Log::info('Inactive WhatsApp subscriptions cleaned up', [
                'deactivated_count' => $inactiveCount,
                'cutoff_days' => $daysInactive
            ]);

            return $inactiveCount;

        } catch (Exception $e) {
            Log::error('Subscription cleanup failed', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }
}