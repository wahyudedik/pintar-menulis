<?php

namespace App\Enums;

enum CopywritingCategory: string
{
    case WEBSITE_LANDING = 'website_landing';
    case ADS = 'ads';
    case SOCIAL_MEDIA = 'social_media';
    case MARKETPLACE = 'marketplace';
    case EMAIL_WHATSAPP = 'email_whatsapp';
    case PROPOSAL_COMPANY = 'proposal_company';
    case PERSONAL_BRANDING = 'personal_branding';
    case UX_WRITING = 'ux_writing';

    public function label(): string
    {
        return match($this) {
            self::WEBSITE_LANDING => 'Website & Landing Page',
            self::ADS => 'Iklan (Ads)',
            self::SOCIAL_MEDIA => 'Social Media Content',
            self::MARKETPLACE => 'Marketplace',
            self::EMAIL_WHATSAPP => 'Email & WhatsApp Marketing',
            self::PROPOSAL_COMPANY => 'Proposal & Company Profile',
            self::PERSONAL_BRANDING => 'Personal Branding',
            self::UX_WRITING => 'UX Writing',
        };
    }

    public function subcategories(): array
    {
        return match($this) {
            self::WEBSITE_LANDING => [
                'headline' => 'Headline Halaman Utama',
                'subheadline' => 'Subheadline',
                'service_description' => 'Deskripsi Layanan',
                'about_us' => 'Tentang Kami',
                'cta' => 'Call To Action',
                'faq' => 'FAQ',
                'pricing_page' => 'Halaman Pricing',
                'product_description' => 'Deskripsi Produk Digital',
            ],
            self::ADS => [
                'headline' => 'Headline Iklan',
                'body_text' => 'Body Text',
                'hook_3sec' => 'Hook 3 Detik Pertama',
                'video_script' => 'Script Video Promosi',
                'caption_promo' => 'Caption Promosi',
            ],
            self::SOCIAL_MEDIA => [
                'instagram_caption' => 'Caption Instagram',
                'thread_edukasi' => 'Thread Edukasi',
                'storytelling' => 'Konten Storytelling',
                'soft_selling' => 'Soft Selling',
                'hard_selling' => 'Hard Selling',
                'reels_tiktok_script' => 'Script Reels/TikTok',
                'educational_content' => 'Konten Edukasi',
            ],
            self::MARKETPLACE => [
                'product_title' => 'Judul Produk',
                'product_description' => 'Deskripsi Produk',
                'bullet_benefits' => 'Bullet Benefit',
                'faq' => 'FAQ Produk',
                'auto_reply' => 'Auto-Reply Chat',
            ],
            self::EMAIL_WHATSAPP => [
                'broadcast_promo' => 'Broadcast Promo',
                'follow_up' => 'Follow Up Calon Client',
                'partnership_offer' => 'Penawaran Kerja Sama',
                'payment_reminder' => 'Reminder Pembayaran',
                'closing_script' => 'Script Closing',
                'welcome_email' => 'Welcome Email',
                'abandoned_cart' => 'Abandoned Cart',
            ],
            self::PROPOSAL_COMPANY => [
                'project_proposal' => 'Proposal Proyek',
                'company_profile' => 'Company Profile',
                'service_offer' => 'Penawaran Jasa',
                'pitch_deck' => 'Pitch Deck SaaS',
                'investor_presentation' => 'Presentasi Investor',
            ],
            self::PERSONAL_BRANDING => [
                'instagram_bio' => 'Bio Instagram',
                'linkedin_summary' => 'LinkedIn Summary',
                'freelance_profile' => 'Deskripsi Profil Freelance',
                'portfolio' => 'Portofolio',
                'about_me' => 'About Me',
            ],
            self::UX_WRITING => [
                'feature_name' => 'Nama Fitur',
                'feature_description' => 'Deskripsi Fitur',
                'onboarding_message' => 'Onboarding Message',
                'notification' => 'Notifikasi Dalam Aplikasi',
                'error_message' => 'Error Message',
                'empty_state' => 'Empty State',
                'success_message' => 'Success Message',
                'button_copy' => 'Button Copy',
            ],
        };
    }

    public function basePrice(): int
    {
        return match($this) {
            self::WEBSITE_LANDING => 500000,
            self::ADS => 300000,
            self::SOCIAL_MEDIA => 50000,
            self::MARKETPLACE => 150000,
            self::EMAIL_WHATSAPP => 400000,
            self::PROPOSAL_COMPANY => 750000,
            self::PERSONAL_BRANDING => 200000,
            self::UX_WRITING => 1000000,
        };
    }

    public function estimatedTime(): string
    {
        return match($this) {
            self::WEBSITE_LANDING => '3-5 hari',
            self::ADS => '2-3 hari',
            self::SOCIAL_MEDIA => '1-2 hari',
            self::MARKETPLACE => '1-2 hari',
            self::EMAIL_WHATSAPP => '3-4 hari',
            self::PROPOSAL_COMPANY => '5-7 hari',
            self::PERSONAL_BRANDING => '2-3 hari',
            self::UX_WRITING => '7-10 hari',
        };
    }
}
