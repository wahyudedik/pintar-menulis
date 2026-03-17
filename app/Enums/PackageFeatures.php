<?php

namespace App\Enums;

class PackageFeatures
{
    // ── Feature Keys ─────────────────────────────────────────────────────────
    const CAPTION_GENERATOR       = 'caption_generator';
    const IMAGE_CAPTION           = 'image_caption';
    const IMAGE_ANALYSIS          = 'image_analysis';
    const VIDEO_CONTENT           = 'video_content';
    const PERFORMANCE_PREDICTOR   = 'performance_predictor';
    const AB_TESTING              = 'ab_testing';
    const MULTI_PLATFORM          = 'multi_platform';
    const CONTENT_REPURPOSE       = 'content_repurpose';
    const GOOGLE_ADS              = 'google_ads';
    const PROMO_LINK              = 'promo_link';
    const PRODUCT_EXPLAINER       = 'product_explainer';
    const SEO_METADATA            = 'seo_metadata';
    const SMART_COMPARISON        = 'smart_comparison';
    const FAQ_GENERATOR           = 'faq_generator';
    const REELS_HOOK              = 'reels_hook';
    const QUALITY_BADGE           = 'quality_badge';
    const DISCOUNT_CAMPAIGN       = 'discount_campaign';
    const TREND_TAGS              = 'trend_tags';
    const LEAD_MAGNET             = 'lead_magnet';
    const COMPETITOR_ANALYSIS     = 'competitor_analysis';
    const BRAND_VOICE             = 'brand_voice';
    const BULK_CONTENT            = 'bulk_content';
    const TEMPLATE_MARKETPLACE    = 'template_marketplace';
    const KEYWORD_RESEARCH        = 'keyword_research';
    const ANALYTICS_EXPORT        = 'analytics_export';
    const TREND_ALERT             = 'trend_alert';
    const OPTIMAL_CONTENT         = 'optimal_content';

    // ── Human-readable labels ─────────────────────────────────────────────────
    public static function labels(): array
    {
        return [
            self::CAPTION_GENERATOR     => 'Caption Generator (Simple & Advanced)',
            self::IMAGE_CAPTION         => 'Image Caption Generator',
            self::IMAGE_ANALYSIS        => 'AI Image Analysis (Vision)',
            self::VIDEO_CONTENT         => 'Video Content Generator',
            self::PERFORMANCE_PREDICTOR => 'Caption Performance Predictor',
            self::AB_TESTING            => 'A/B Testing Variants',
            self::MULTI_PLATFORM        => 'Multi-Platform Optimizer',
            self::CONTENT_REPURPOSE     => 'Content Repurposing',
            self::GOOGLE_ADS            => 'Google Ads Campaign Generator',
            self::PROMO_LINK            => 'Magic Promo Link Generator',
            self::PRODUCT_EXPLAINER     => 'AI Product Explainer (WhatsApp)',
            self::SEO_METADATA          => 'SEO Metadata Generator',
            self::SMART_COMPARISON      => 'Smart Product Comparison',
            self::FAQ_GENERATOR         => 'FAQ Generator',
            self::REELS_HOOK            => 'Reels Hook Generator',
            self::QUALITY_BADGE         => 'Quality Badge Generator',
            self::DISCOUNT_CAMPAIGN     => 'Discount Campaign Generator',
            self::TREND_TAGS            => 'Trend Tags Generator',
            self::LEAD_MAGNET           => 'Lead Magnet Generator',
            self::COMPETITOR_ANALYSIS   => 'Competitor Analysis',
            self::BRAND_VOICE           => 'Brand Voice',
            self::BULK_CONTENT          => 'Bulk Content Generator',
            self::TEMPLATE_MARKETPLACE  => 'Template Marketplace',
            self::KEYWORD_RESEARCH      => 'Keyword Research',
            self::ANALYTICS_EXPORT      => 'Analytics & Export PDF',
            self::TREND_ALERT           => 'Trend Alert & Viral Ideas',
            self::OPTIMAL_CONTENT       => 'Analytics-Optimized Content',
        ];
    }

    // ── Grouped by category for the admin UI ─────────────────────────────────
    public static function groups(): array
    {
        return [
            'Konten Dasar' => [
                self::CAPTION_GENERATOR,
                self::IMAGE_CAPTION,
                self::IMAGE_ANALYSIS,
                self::VIDEO_CONTENT,
            ],
            'Optimasi & Analitik' => [
                self::PERFORMANCE_PREDICTOR,
                self::AB_TESTING,
                self::MULTI_PLATFORM,
                self::CONTENT_REPURPOSE,
                self::OPTIMAL_CONTENT,
                self::ANALYTICS_EXPORT,
            ],
            'Marketing & Iklan' => [
                self::GOOGLE_ADS,
                self::PROMO_LINK,
                self::PRODUCT_EXPLAINER,
                self::DISCOUNT_CAMPAIGN,
                self::LEAD_MAGNET,
            ],
            'SEO & Riset' => [
                self::SEO_METADATA,
                self::SMART_COMPARISON,
                self::FAQ_GENERATOR,
                self::KEYWORD_RESEARCH,
                self::TREND_TAGS,
                self::TREND_ALERT,
            ],
            'Kreativitas' => [
                self::REELS_HOOK,
                self::QUALITY_BADGE,
                self::COMPETITOR_ANALYSIS,
            ],
            'Tim & Lanjutan' => [
                self::BRAND_VOICE,
                self::BULK_CONTENT,
                self::TEMPLATE_MARKETPLACE,
            ],
        ];
    }

    // ── Default features per tier (fallback jika allowed_features null) ───────
    public static function tierDefaults(): array
    {
        return [
            // Paket Gratis — fitur dasar saja
            'free' => [
                self::CAPTION_GENERATOR,
                self::IMAGE_CAPTION,
            ],
            // Starter — tambah beberapa fitur marketing
            'starter' => [
                self::CAPTION_GENERATOR,
                self::IMAGE_CAPTION,
                self::IMAGE_ANALYSIS,
                self::PROMO_LINK,
                self::PRODUCT_EXPLAINER,
                self::DISCOUNT_CAMPAIGN,
                self::FAQ_GENERATOR,
                self::TREND_TAGS,
                self::TEMPLATE_MARKETPLACE,
            ],
            // Pro — hampir semua fitur
            'pro' => [
                self::CAPTION_GENERATOR,
                self::IMAGE_CAPTION,
                self::IMAGE_ANALYSIS,
                self::VIDEO_CONTENT,
                self::PERFORMANCE_PREDICTOR,
                self::AB_TESTING,
                self::MULTI_PLATFORM,
                self::CONTENT_REPURPOSE,
                self::GOOGLE_ADS,
                self::PROMO_LINK,
                self::PRODUCT_EXPLAINER,
                self::SEO_METADATA,
                self::SMART_COMPARISON,
                self::FAQ_GENERATOR,
                self::REELS_HOOK,
                self::DISCOUNT_CAMPAIGN,
                self::TREND_TAGS,
                self::LEAD_MAGNET,
                self::COMPETITOR_ANALYSIS,
                self::BRAND_VOICE,
                self::TEMPLATE_MARKETPLACE,
                self::KEYWORD_RESEARCH,
                self::ANALYTICS_EXPORT,
                self::TREND_ALERT,
                self::OPTIMAL_CONTENT,
            ],
            // Bisnis — semua fitur
            'business' => array_keys(self::labels()),
        ];
    }

    public static function all(): array
    {
        return array_keys(self::labels());
    }
}
