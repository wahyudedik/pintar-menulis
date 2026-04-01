<?php

/**
 * Credit cost per AI feature.
 * Key = feature identifier used in controller/route.
 * Value = number of credits consumed per use.
 */
return [
    // ── Konten Dasar (1 kredit) ──────────────────────────────
    'generate'              => 1,  // Text Generator
    'image_caption'         => 1,  // Image Caption
    'seo_metadata'          => 1,  // SEO Metadata
    'faq_generator'         => 1,  // FAQ Generator
    'discount_campaign'     => 1,  // Discount Campaign
    'trend_tags'            => 1,  // Trend Tags
    'reels_hook'            => 1,  // Reels Hook

    // ── Fitur Menengah (2 kredit) ────────────────────────────
    'image_analysis'        => 2,  // Image & Design Analysis (Vision)
    'video_content'         => 2,  // Video Script Generator
    'multi_platform'        => 2,  // Multi-Platform Optimizer
    'content_repurpose'     => 2,  // Content Repurposing
    'google_ads'            => 2,  // Google Ads Campaign
    'promo_link'            => 2,  // Magic Promo Link
    'product_explainer'     => 2,  // Product Explainer
    'smart_comparison'      => 2,  // Smart Comparison
    'lead_magnet'           => 2,  // Lead Magnet
    'quality_badge'         => 2,  // Quality Badge Scanner
    'trend_content'         => 2,  // Trend Alert Content
    'optimal_content'       => 2,  // Analytics-Optimized Content
    'predict_performance'   => 2,  // Performance Predictor
    'ab_variants'           => 2,  // A/B Testing Variants

    // ── Fitur Berat (3 kredit) ───────────────────────────────
    'financial_analysis'    => 3,  // Analisis Keuangan (Vision + PDF)
    'ebook_analysis'        => 3,  // Analisis Ebook (PDF processing)
    'reader_trend'          => 3,  // Tren Pembaca
];
