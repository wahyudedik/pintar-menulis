<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class MultiLanguageService
{
    private $supportedLanguages = [
        'bahasa_indonesia' => [
            'name' => 'Bahasa Indonesia',
            'code' => 'id',
            'flag' => '🇮🇩',
            'greeting' => 'Halo! Selamat datang di Pintar Menulis AI',
            'help_command' => 'bantuan'
        ],
        'bahasa_jawa' => [
            'name' => 'Bahasa Jawa',
            'code' => 'jv',
            'flag' => '🏛️',
            'greeting' => 'Sugeng rawuh ing Pintar Menulis AI',
            'help_command' => 'tulung'
        ],
        'bahasa_sunda' => [
            'name' => 'Bahasa Sunda',
            'code' => 'su',
            'flag' => '🏔️',
            'greeting' => 'Wilujeng sumping ka Pintar Menulis AI',
            'help_command' => 'pitulung'
        ],
        'bahasa_bali' => [
            'name' => 'Bahasa Bali',
            'code' => 'ban',
            'flag' => '🏝️',
            'greeting' => 'Om Swastiastu, rahayu ring Pintar Menulis AI',
            'help_command' => 'tulung'
        ],
        'bahasa_betawi' => [
            'name' => 'Bahasa Betawi',
            'code' => 'bew',
            'flag' => '🏙️',
            'greeting' => 'Halo bro! Selamat dateng di Pintar Menulis AI',
            'help_command' => 'bantuin'
        ],
        'bahasa_madura' => [
            'name' => 'Bahasa Madura',
            'code' => 'mad',
            'flag' => '⛵',
            'greeting' => 'Salamat dateng ka Pintar Menulis AI',
            'help_command' => 'tolong'
        ],
        'bahasa_bugis' => [
            'name' => 'Bahasa Bugis',
            'code' => 'bug',
            'flag' => '🚢',
            'greeting' => 'Selamat datang di Pintar Menulis AI',
            'help_command' => 'bantuan'
        ],
        'bahasa_banjar' => [
            'name' => 'Bahasa Banjar',
            'code' => 'bjn',
            'flag' => '🌊',
            'greeting' => 'Selamat datang di Pintar Menulis AI',
            'help_command' => 'tulung'
        ],
        'english' => [
            'name' => 'English',
            'code' => 'en',
            'flag' => '🇺🇸',
            'greeting' => 'Hello! Welcome to Pintar Menulis AI',
            'help_command' => 'help'
        ],
        'mix_bahasa' => [
            'name' => 'Mix Bahasa',
            'code' => 'mix',
            'flag' => '🌈',
            'greeting' => 'Halo! Welcome to Pintar Menulis AI - Campur bahasa oke!',
            'help_command' => 'help'
        ]
    ];

    /**
     * 🗣️ Get language information
     */
    public function getLanguageInfo(string $languageCode): ?array
    {
        return $this->supportedLanguages[$languageCode] ?? null;
    }

    /**
     * 📋 Get all supported languages
     */
    public function getSupportedLanguages(): array
    {
        return $this->supportedLanguages;
    }

    /**
     * 🔍 Detect language from text
     */
    public function detectLanguage(string $text): string
    {
        $text = strtolower($text);
        
        // Indonesian keywords
        if (preg_match('/\b(saya|aku|kamu|anda|dengan|untuk|dari|ini|itu|yang|adalah|akan|sudah|belum|tidak|bisa|mau|ingin)\b/', $text)) {
            return 'bahasa_indonesia';
        }
        
        // Javanese keywords
        if (preg_match('/\b(kulo|aku|sampeyan|kowe|lan|karo|saking|niki|niku|ingkang|badhe|sampun|dereng|mboten|saged)\b/', $text)) {
            return 'bahasa_jawa';
        }
        
        // Sundanese keywords
        if (preg_match('/\b(abdi|aing|anjeun|sareng|jeung|ti|ieu|eta|nu|bakal|parantos|can|teu|tiasa)\b/', $text)) {
            return 'bahasa_sunda';
        }
        
        // English keywords
        if (preg_match('/\b(i|you|we|they|with|for|from|this|that|which|will|have|has|not|can|want)\b/', $text)) {
            return 'english';
        }
        
        // Default to Indonesian
        return 'bahasa_indonesia';
    }

    /**
     * 💬 Get localized messages
     */
    public function getLocalizedMessages(string $languageCode): array
    {
        $messages = [
            'bahasa_indonesia' => [
                'welcome' => 'Selamat datang di Pintar Menulis AI! 🤖✨',
                'help_title' => '📱 *Panduan Pintar Menulis AI*',
                'help_usage' => '📝 *Cara Pakai:*',
                'help_commands' => '🎯 *Perintah Khusus:*',
                'help_examples' => '💡 *Contoh:*',
                'menu_title' => '📱 *Menu Pintar Menulis AI*',
                'daily_ideas' => '🌅 *Ide Konten Harian*',
                'error_message' => '❌ Terjadi kesalahan. Coba lagi nanti ya!',
                'processing' => '🤖 Sedang memproses... Tunggu sebentar ya!',
                'success' => '✅ Berhasil!',
                'failed' => '❌ Gagal!',
                'subscription_success' => '✅ Berhasil berlangganan notifikasi!',
                'unsubscribe_success' => '😢 Berhasil berhenti berlangganan',
                'voice_processing' => '🎵 Sedang memproses voice note...',
                'image_processing' => '🔄 Sedang menganalisis gambar...',
                'powered_by' => '_Powered by Pintar Menulis AI_ ✨'
            ],
            'bahasa_jawa' => [
                'welcome' => 'Sugeng rawuh ing Pintar Menulis AI! 🤖✨',
                'help_title' => '📱 *Pandhuan Pintar Menulis AI*',
                'help_usage' => '📝 *Cara Nggunakake:*',
                'help_commands' => '🎯 *Printah Khusus:*',
                'help_examples' => '💡 *Tuladha:*',
                'menu_title' => '📱 *Menu Pintar Menulis AI*',
                'daily_ideas' => '🌅 *Ide Konten Saben Dina*',
                'error_message' => '❌ Ana masalah. Coba maneh ya!',
                'processing' => '🤖 Lagi diproses... Sabar ya!',
                'success' => '✅ Sukses!',
                'failed' => '❌ Gagal!',
                'subscription_success' => '✅ Sukses langganan notifikasi!',
                'unsubscribe_success' => '😢 Sukses mandheg langganan',
                'voice_processing' => '🎵 Lagi ngolah voice note...',
                'image_processing' => '🔄 Lagi nganalisis gambar...',
                'powered_by' => '_Powered by Pintar Menulis AI_ ✨'
            ],
            'bahasa_sunda' => [
                'welcome' => 'Wilujeng sumping ka Pintar Menulis AI! 🤖✨',
                'help_title' => '📱 *Panuntun Pintar Menulis AI*',
                'help_usage' => '📝 *Cara Maké:*',
                'help_commands' => '🎯 *Paréntah Husus:*',
                'help_examples' => '💡 *Conto:*',
                'menu_title' => '📱 *Menu Pintar Menulis AI*',
                'daily_ideas' => '🌅 *Ide Eusi Poéan*',
                'error_message' => '❌ Aya masalah. Cobian deui!',
                'processing' => '🤖 Keur diprosés... Sabar heula!',
                'success' => '✅ Suksés!',
                'failed' => '❌ Gagal!',
                'subscription_success' => '✅ Suksés ngalanggan notifikasi!',
                'unsubscribe_success' => '😢 Suksés eureun ngalanggan',
                'voice_processing' => '🎵 Keur ngolah voice note...',
                'image_processing' => '🔄 Keur nganalisis gambar...',
                'powered_by' => '_Powered by Pintar Menulis AI_ ✨'
            ],
            'english' => [
                'welcome' => 'Welcome to Pintar Menulis AI! 🤖✨',
                'help_title' => '📱 *Pintar Menulis AI Guide*',
                'help_usage' => '📝 *How to Use:*',
                'help_commands' => '🎯 *Special Commands:*',
                'help_examples' => '💡 *Examples:*',
                'menu_title' => '📱 *Pintar Menulis AI Menu*',
                'daily_ideas' => '🌅 *Daily Content Ideas*',
                'error_message' => '❌ An error occurred. Please try again later!',
                'processing' => '🤖 Processing... Please wait!',
                'success' => '✅ Success!',
                'failed' => '❌ Failed!',
                'subscription_success' => '✅ Successfully subscribed to notifications!',
                'unsubscribe_success' => '😢 Successfully unsubscribed',
                'voice_processing' => '🎵 Processing voice note...',
                'image_processing' => '🔄 Analyzing image...',
                'powered_by' => '_Powered by Pintar Menulis AI_ ✨'
            ],
            'mix_bahasa' => [
                'welcome' => 'Welcome! Selamat datang di Pintar Menulis AI! 🤖✨',
                'help_title' => '📱 *Guide Pintar Menulis AI*',
                'help_usage' => '📝 *How to Use / Cara Pakai:*',
                'help_commands' => '🎯 *Special Commands / Perintah Khusus:*',
                'help_examples' => '💡 *Examples / Contoh:*',
                'menu_title' => '📱 *Menu Pintar Menulis AI*',
                'daily_ideas' => '🌅 *Daily Content Ideas / Ide Konten Harian*',
                'error_message' => '❌ Error occurred / Terjadi kesalahan. Try again / Coba lagi!',
                'processing' => '🤖 Processing / Sedang diproses... Please wait / Tunggu ya!',
                'success' => '✅ Success / Berhasil!',
                'failed' => '❌ Failed / Gagal!',
                'subscription_success' => '✅ Successfully subscribed / Berhasil langganan!',
                'unsubscribe_success' => '😢 Successfully unsubscribed / Berhasil berhenti langganan',
                'voice_processing' => '🎵 Processing voice note / Memproses voice note...',
                'image_processing' => '🔄 Analyzing image / Menganalisis gambar...',
                'powered_by' => '_Powered by Pintar Menulis AI_ ✨'
            ]
        ];

        return $messages[$languageCode] ?? $messages['bahasa_indonesia'];
    }

    /**
     * 🎯 Get localized help message
     */
    public function getHelpMessage(string $languageCode): string
    {
        $messages = $this->getLocalizedMessages($languageCode);
        $lang = $this->getLanguageInfo($languageCode);

        $helpMessage = "{$messages['help_title']}\n\n";
        $helpMessage .= "{$messages['help_usage']}\n";
        
        switch ($languageCode) {
            case 'bahasa_jawa':
                $helpMessage .= "• Ketik pesan → Entuk caption AI\n";
                $helpMessage .= "• Kirim foto → Entuk caption + hashtag\n";
                $helpMessage .= "• Kirim voice note → Entuk caption\n\n";
                $helpMessage .= "{$messages['help_commands']}\n";
                $helpMessage .= "• `menu` - Deleng kabeh fitur\n";
                $helpMessage .= "• `ide` - Ide konten saben dina\n";
                $helpMessage .= "• `tulung` - Pandhuan iki\n\n";
                $helpMessage .= "{$messages['help_examples']}\n";
                $helpMessage .= "\"produk kecantikan kanggo nom-noman\"\n";
                break;
                
            case 'bahasa_sunda':
                $helpMessage .= "• Ketik pesan → Meunang caption AI\n";
                $helpMessage .= "• Kirim poto → Meunang caption + hashtag\n";
                $helpMessage .= "• Kirim voice note → Meunang caption\n\n";
                $helpMessage .= "{$messages['help_commands']}\n";
                $helpMessage .= "• `menu` - Tingali sadaya fitur\n";
                $helpMessage .= "• `ide` - Ide eusi poéan\n";
                $helpMessage .= "• `pitulung` - Panuntun ieu\n\n";
                $helpMessage .= "{$messages['help_examples']}\n";
                $helpMessage .= "\"produk kageulisan pikeun budak ngora\"\n";
                break;
                
            case 'english':
                $helpMessage .= "• Type message → Get AI caption\n";
                $helpMessage .= "• Send photo → Get caption + hashtags\n";
                $helpMessage .= "• Send voice note → Get caption\n\n";
                $helpMessage .= "{$messages['help_commands']}\n";
                $helpMessage .= "• `menu` - View all features\n";
                $helpMessage .= "• `daily` - Daily content ideas\n";
                $helpMessage .= "• `help` - This guide\n\n";
                $helpMessage .= "{$messages['help_examples']}\n";
                $helpMessage .= "\"beauty products for teenagers\"\n";
                break;
                
            default: // Indonesian and others
                $helpMessage .= "• Ketik pesan → Dapet caption AI\n";
                $helpMessage .= "• Kirim foto → Dapet caption + hashtag\n";
                $helpMessage .= "• Kirim voice note → Dapet caption\n\n";
                $helpMessage .= "{$messages['help_commands']}\n";
                $helpMessage .= "• `menu` - Lihat semua fitur\n";
                $helpMessage .= "• `daily` - Ide konten harian\n";
                $helpMessage .= "• `bantuan` - Panduan ini\n\n";
                $helpMessage .= "{$messages['help_examples']}\n";
                $helpMessage .= "\"produk kecantikan untuk remaja\"\n";
                break;
        }
        
        $helpMessage .= "\n{$messages['powered_by']}";
        
        return $helpMessage;
    }

    /**
     * 📱 Get localized menu message
     */
    public function getMenuMessage(string $languageCode): string
    {
        $messages = $this->getLocalizedMessages($languageCode);
        
        $menuMessage = "{$messages['menu_title']}\n\n";
        
        switch ($languageCode) {
            case 'bahasa_jawa':
                $menuMessage .= "🎯 *Generator Konten:*\n";
                $menuMessage .= "1️⃣ Caption Instagram/Facebook\n";
                $menuMessage .= "2️⃣ Caption TikTok/Reels\n";
                $menuMessage .= "3️⃣ Thread Twitter/X\n";
                $menuMessage .= "4️⃣ Video Script & Ideas\n\n";
                $menuMessage .= "💬 Langsung ketik wae topik sing arep digawe caption!";
                break;
                
            case 'bahasa_sunda':
                $menuMessage .= "🎯 *Generator Eusi:*\n";
                $menuMessage .= "1️⃣ Caption Instagram/Facebook\n";
                $menuMessage .= "2️⃣ Caption TikTok/Reels\n";
                $menuMessage .= "3️⃣ Thread Twitter/X\n";
                $menuMessage .= "4️⃣ Video Script & Ideas\n\n";
                $menuMessage .= "💬 Langsung ketik wae topik nu rék dijieun caption!";
                break;
                
            case 'english':
                $menuMessage .= "🎯 *Content Generator:*\n";
                $menuMessage .= "1️⃣ Instagram/Facebook Captions\n";
                $menuMessage .= "2️⃣ TikTok/Reels Captions\n";
                $menuMessage .= "3️⃣ Twitter/X Threads\n";
                $menuMessage .= "4️⃣ Video Scripts & Ideas\n\n";
                $menuMessage .= "💬 Just type the topic you want to create a caption for!";
                break;
                
            default: // Indonesian and others
                $menuMessage .= "🎯 *Generator Konten:*\n";
                $menuMessage .= "1️⃣ Caption Instagram/Facebook\n";
                $menuMessage .= "2️⃣ Caption TikTok/Reels\n";
                $menuMessage .= "3️⃣ Thread Twitter/X\n";
                $menuMessage .= "4️⃣ Video Script & Ideas\n\n";
                $menuMessage .= "💬 Langsung ketik aja topik yang mau dibuatin caption!";
                break;
        }
        
        return $menuMessage;
    }

    /**
     * 🌅 Get localized daily ideas message
     */
    public function getDailyIdeasMessage(string $languageCode): string
    {
        $messages = $this->getLocalizedMessages($languageCode);
        $date = date('d M Y');
        
        $ideasMessage = "{$messages['daily_ideas']} - {$date}\n\n";
        
        switch ($languageCode) {
            case 'bahasa_jawa':
                $ideasMessage .= "📝 *Ide Konten Dina Iki:*\n";
                $ideasMessage .= "1. 🔥 Trending topic: Share pengalaman unik bisnis\n";
                $ideasMessage .= "2. 💡 Tips: Wenehi tips praktis kanggo customer\n";
                $ideasMessage .= "3. 🎯 Behind the scenes: Proses kerja saben dina\n";
                $ideasMessage .= "4. 📊 Data menarik: Statistik utawa fakta industri\n\n";
                $ideasMessage .= "💬 *Bales karo nomer kanggo generate caption:*\n";
                $ideasMessage .= "Tuladha: \"1\" kanggo ide kapisan\n\n";
                break;
                
            case 'bahasa_sunda':
                $ideasMessage .= "📝 *Ide Eusi Poé Ieu:*\n";
                $ideasMessage .= "1. 🔥 Trending topic: Share pangalaman unik bisnis\n";
                $ideasMessage .= "2. 💡 Tips: Masihan tips praktis pikeun customer\n";
                $ideasMessage .= "3. 🎯 Behind the scenes: Prosés gawé poéan\n";
                $ideasMessage .= "4. 📊 Data narik: Statistik atawa fakta industri\n\n";
                $ideasMessage .= "💬 *Bales ku nomer pikeun generate caption:*\n";
                $ideasMessage .= "Conto: \"1\" pikeun ide kahiji\n\n";
                break;
                
            case 'english':
                $ideasMessage .= "📝 *Today's Content Ideas:*\n";
                $ideasMessage .= "1. 🔥 Trending topic: Share unique business experiences\n";
                $ideasMessage .= "2. 💡 Tips: Give practical tips for customers\n";
                $ideasMessage .= "3. 🎯 Behind the scenes: Daily work processes\n";
                $ideasMessage .= "4. 📊 Interesting data: Industry statistics or facts\n\n";
                $ideasMessage .= "💬 *Reply with number to generate caption:*\n";
                $ideasMessage .= "Example: \"1\" for first idea\n\n";
                break;
                
            default: // Indonesian and others
                $ideasMessage .= "📝 *Ide Konten Hari Ini:*\n";
                $ideasMessage .= "1. 🔥 Trending topic: Share pengalaman unik bisnis\n";
                $ideasMessage .= "2. 💡 Tips: Bagikan tips praktis untuk customer\n";
                $ideasMessage .= "3. 🎯 Behind the scenes: Proses kerja sehari-hari\n";
                $ideasMessage .= "4. 📊 Data menarik: Statistik atau fakta industri\n\n";
                $ideasMessage .= "💬 *Balas dengan nomor untuk generate caption:*\n";
                $ideasMessage .= "Contoh: \"1\" untuk ide pertama\n\n";
                break;
        }
        
        $ideasMessage .= $messages['powered_by'];
        
        return $ideasMessage;
    }

    /**
     * 🔄 Auto-translate message based on user preference
     */
    public function translateMessage(string $message, string $targetLanguage): string
    {
        // Simple keyword replacement for common phrases
        $translations = [
            'bahasa_jawa' => [
                'Halo' => 'Sugeng',
                'Selamat datang' => 'Sugeng rawuh',
                'Terima kasih' => 'Matur nuwun',
                'Maaf' => 'Nyuwun pangapunten',
                'Berhasil' => 'Sukses',
                'Gagal' => 'Gagal',
                'Tunggu' => 'Sabar'
            ],
            'bahasa_sunda' => [
                'Halo' => 'Wilujeng',
                'Selamat datang' => 'Wilujeng sumping',
                'Terima kasih' => 'Hatur nuhun',
                'Maaf' => 'Hapunten',
                'Berhasil' => 'Suksés',
                'Gagal' => 'Gagal',
                'Tunggu' => 'Sabar heula'
            ]
        ];

        if (isset($translations[$targetLanguage])) {
            foreach ($translations[$targetLanguage] as $from => $to) {
                $message = str_ireplace($from, $to, $message);
            }
        }

        return $message;
    }

    /**
     * 📊 Get language usage statistics
     */
    public function getLanguageStats(): array
    {
        // This would typically query the database for actual usage stats
        return [
            'total_languages' => count($this->supportedLanguages),
            'most_popular' => 'bahasa_indonesia',
            'usage_distribution' => [
                'bahasa_indonesia' => 65,
                'bahasa_jawa' => 15,
                'bahasa_sunda' => 8,
                'english' => 7,
                'mix_bahasa' => 3,
                'others' => 2
            ]
        ];
    }
}