<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureUsageLog extends Model
{
    protected $fillable = [
        'user_id', 'feature_key', 'feature_label', 'feature_category',
        'route_name', 'http_method', 'duration_ms', 'success',
        'subscription_package', 'usage_date',
    ];

    protected $casts = [
        'success'    => 'boolean',
        'usage_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Complete map: route_name => [feature_key, feature_label, category]
     * Covers ALL client-facing features.
     */
    public static function featureMap(): array
    {
        return [
            //  AI Generator (page + all sub-tools) 
            'ai.generator'                          => ['ai_generator_page',      'AI Generator',              'AI Tools'],
            'api.ai.generate'                       => ['ai_caption',             'AI Caption Generator',      'AI Tools'],
            'api.ai.generate-image-caption'         => ['ai_image_caption',       'AI Image Caption',          'AI Tools'],
            'api.ai.analyze-image'                  => ['ai_image_analysis',      'AI Image Analysis',         'AI Tools'],
            'api.ai.analyze-financial'              => ['ai_financial',           'Financial Analysis',        'AI Tools'],
            'api.ai.analyze-ebook'                  => ['ai_ebook',               'Ebook Analysis',            'AI Tools'],
            'api.ai.analyze-reader-trend'           => ['ai_reader_trend',        'Reader Trend Analysis',     'AI Tools'],
            'api.ai.generate-video-content'         => ['ai_video_content',       'Video Content Generator',   'AI Tools'],
            'api.ai.predict-performance'            => ['ai_performance',         'Performance Predictor',     'AI Tools'],
            'api.ai.generate-ab-variants'           => ['ai_ab_testing',          'A/B Testing Generator',     'AI Tools'],
            'api.ai.generate-multiplatform'         => ['ai_multiplatform',       'Multi-Platform Content',    'AI Tools'],
            'api.ai.repurpose-content'              => ['ai_repurpose',           'Content Repurpose',         'AI Tools'],
            'api.ai.generate-trend-content'         => ['ai_trend_content',       'Trend Content',             'AI Tools'],
            'api.ai.generate-optimal-content'       => ['ai_optimal_content',     'Optimal Content',           'AI Tools'],
            'api.ai.generate-google-ads'            => ['ai_google_ads',          'Google Ads Generator',      'AI Tools'],
            'api.ai.generate-promo-link'            => ['ai_promo_link',          'Promo Link Generator',      'AI Tools'],
            'api.ai.generate-product-explainer'     => ['ai_product_explainer',   'Product Explainer',         'AI Tools'],
            'api.ai.generate-seo-metadata'          => ['ai_seo_metadata',        'SEO Metadata Generator',    'AI Tools'],
            'api.ai.generate-comparison'            => ['ai_comparison',          'Smart Comparison',          'AI Tools'],
            'api.ai.generate-faq'                   => ['ai_faq',                 'FAQ Generator',             'AI Tools'],
            'api.ai.generate-reels-hook'            => ['ai_reels_hook',          'Reels Hook Generator',      'AI Tools'],
            'api.ai.generate-quality-badge'         => ['ai_quality_badge',       'Quality Badge',             'AI Tools'],
            'api.ai.generate-discount-campaign'     => ['ai_discount_campaign',   'Discount Campaign',         'AI Tools'],
            'api.ai.generate-trend-tags'            => ['ai_trend_tags',          'Trend Tags Generator',      'AI Tools'],
            'api.ai.generate-lead-magnet'           => ['ai_lead_magnet',         'Lead Magnet Generator',     'AI Tools'],
            'api.ai.generate-content'               => ['ai_project_content',     'AI Project Content',        'AI Tools'],
            'api.templates.all'                     => ['ai_templates_list',       'AI Templates List',         'AI Tools'],
            'api.check-first-time'                  => ['ai_first_time_check',    'AI First Time Check',       'AI Tools'],

            //  Caption Optimizer 
            'api.optimizer.grammar.check'           => ['opt_grammar',            'Grammar Checker',           'Caption Optimizer'],
            'api.optimizer.grammar.quick-fix'       => ['opt_grammar_fix',        'Grammar Quick Fix',         'Caption Optimizer'],
            'api.optimizer.grammar.detailed-analysis'=> ['opt_grammar_detail',    'Grammar Detail Analysis',   'Caption Optimizer'],
            'api.optimizer.length.shorten'          => ['opt_shorten',            'Caption Shortener',         'Caption Optimizer'],
            'api.optimizer.length.expand'           => ['opt_expand',             'Caption Expander',          'Caption Optimizer'],
            'api.optimizer.length.smart-adjust'     => ['opt_smart_adjust',       'Smart Length Adjust',       'Caption Optimizer'],
            'api.optimizer.length.optimal-guide'    => ['opt_optimal_guide',      'Optimal Length Guide',      'Caption Optimizer'],
            'api.optimizer.length.analyze-impact'   => ['opt_length_impact',      'Length Impact Analysis',    'Caption Optimizer'],
            'api.optimizer.batch-optimize'          => ['opt_batch',              'Batch Optimizer',           'Caption Optimizer'],
            'api.optimizer.stats'                   => ['opt_stats',              'Optimizer Stats',           'Caption Optimizer'],

            //  AI Analysis 
            'api.analysis.sentiment'                => ['analysis_sentiment',     'Sentiment Analysis',        'AI Analysis'],
            'api.analysis.image'                    => ['analysis_image',         'Image Analysis',            'AI Analysis'],
            'api.analysis.score-caption'            => ['analysis_score',         'Caption Scoring',           'AI Analysis'],
            'api.analysis.recommendations'          => ['analysis_recommend',     'AI Recommendations',        'AI Analysis'],
            'api.analysis.campaign'                 => ['analysis_campaign',      'Campaign Analysis',         'AI Analysis'],
            'api.analysis.article'                  => ['analysis_article',       'Article Analysis',          'AI Analysis'],
            'api.analysis.history'                  => ['analysis_history',       'Analysis History',          'AI Analysis'],
            'api.analysis.dashboard'                => ['analysis_dashboard',     'Analysis Dashboard',        'AI Analysis'],

            //  Campaign Analytics 
            'analytics.index'                       => ['analytics',              'Campaign Analytics',        'Analytics'],
            'analytics.store'                       => ['analytics_create',       'Create Analytics',          'Analytics'],
            'analytics.show'                        => ['analytics',              'Campaign Analytics',        'Analytics'],
            'analytics.update'                      => ['analytics_update',       'Update Analytics',          'Analytics'],
            'analytics.insights'                    => ['analytics_insights',     'Analytics Insights',        'Analytics'],
            'analytics.export.pdf'                  => ['analytics_export_pdf',   'Analytics Export PDF',      'Analytics'],
            'analytics.export.csv'                  => ['analytics_export_csv',   'Analytics Export CSV',      'Analytics'],
            'api.analytics.insights'                => ['analytics_insights_api', 'Analytics Insights API',    'Analytics'],
            'api.analytics.competitor-comparison'   => ['analytics_competitor',   'Competitor Comparison',     'Analytics'],

            //  Keyword Research 
            'keyword-research.index'                => ['keyword_research_page',  'Keyword Research',          'Keyword Research'],
            'api.keyword-research.search'           => ['keyword_search',         'Keyword Search',            'Keyword Research'],
            'api.keyword-research.history'          => ['keyword_history',        'Keyword History',           'Keyword Research'],

            //  Competitor Analysis 
            'competitor-analysis.index'             => ['competitor_analysis',    'Competitor Analysis',       'Competitor Analysis'],
            'competitor-analysis.create'            => ['competitor_add',         'Add Competitor',            'Competitor Analysis'],
            'competitor-analysis.store'             => ['competitor_add',         'Add Competitor',            'Competitor Analysis'],
            'competitor-analysis.show'              => ['competitor_detail',      'Competitor Detail',         'Competitor Analysis'],
            'competitor-analysis.compare'           => ['competitor_compare',     'Competitor Compare',        'Competitor Analysis'],
            'competitor-analysis.refresh'           => ['competitor_refresh',     'Competitor Refresh',        'Competitor Analysis'],
            'competitor-analysis.toggle-active'     => ['competitor_toggle',      'Toggle Competitor',         'Competitor Analysis'],
            'competitor-analysis.destroy'           => ['competitor_delete',      'Delete Competitor',         'Competitor Analysis'],
            'competitor-analysis.alerts'            => ['competitor_alerts',      'Competitor Alerts',         'Competitor Analysis'],
            'competitor-analysis.alert.read'        => ['competitor_alert_read',  'Read Alert',                'Competitor Analysis'],
            'competitor-analysis.alerts.read-all'   => ['competitor_alerts_all',  'Read All Alerts',           'Competitor Analysis'],
            'competitor-analysis.content-gaps'      => ['competitor_gaps',        'Content Gaps',              'Competitor Analysis'],
            'competitor-analysis.gap.implement'     => ['competitor_gap_impl',    'Implement Content Gap',     'Competitor Analysis'],
            'api.competitor-analysis.calculate-pricing' => ['competitor_pricing', 'Competitor Pricing Calc',   'Competitor Analysis'],

            //  Bulk Content Generator 
            'bulk-content.index'                    => ['bulk_content',           'Bulk Content Generator',    'Content Tools'],
            'bulk-content.create'                   => ['bulk_content',           'Bulk Content Generator',    'Content Tools'],
            'bulk-content.generate'                 => ['bulk_content_gen',       'Generate Bulk Content',     'Content Tools'],
            'bulk-content.show'                     => ['bulk_content_view',      'View Bulk Content',         'Content Tools'],
            'bulk-content.export'                   => ['bulk_content_export',    'Export Bulk Content',       'Content Tools'],
            'bulk-content.update-content'           => ['bulk_content_edit',      'Edit Bulk Content',         'Content Tools'],
            'bulk-content.destroy'                  => ['bulk_content_delete',    'Delete Bulk Content',       'Content Tools'],

            //  Image Caption 
            'image-caption.index'                   => ['image_caption',          'Image Caption',             'Content Tools'],
            'image-caption.create'                  => ['image_caption',          'Image Caption',             'Content Tools'],
            'image-caption.store'                   => ['image_caption_gen',      'Generate Image Caption',    'Content Tools'],
            'image-caption.show'                    => ['image_caption_view',     'View Image Caption',        'Content Tools'],
            'image-caption.destroy'                 => ['image_caption_delete',   'Delete Image Caption',      'Content Tools'],

            //  Template Marketplace 
            'templates.index'                       => ['templates',              'Template Marketplace',      'Marketplace'],
            'templates.show'                        => ['template_view',          'View Template',             'Marketplace'],
            'templates.create'                      => ['template_create',        'Create Template',           'Marketplace'],
            'templates.store'                       => ['template_create',        'Create Template',           'Marketplace'],
            'templates.edit'                        => ['template_edit',          'Edit Template',             'Marketplace'],
            'templates.update'                      => ['template_edit',          'Edit Template',             'Marketplace'],
            'templates.destroy'                     => ['template_delete',        'Delete Template',           'Marketplace'],
            'templates.use'                         => ['template_use',           'Use Template',              'Marketplace'],
            'templates.rate'                        => ['template_rate',          'Rate Template',             'Marketplace'],
            'templates.favorite'                    => ['template_favorite',      'Favorite Template',         'Marketplace'],
            'templates.purchase'                    => ['template_purchase',      'Purchase Template',         'Marketplace'],
            'templates.checkout'                    => ['template_checkout',      'Template Checkout',         'Marketplace'],
            'templates.checkout.confirm'            => ['template_checkout',      'Template Checkout',         'Marketplace'],
            'templates.export'                      => ['template_export',        'Export Template',           'Marketplace'],
            'templates.export-multiple'             => ['template_export',        'Export Template',           'Marketplace'],
            'templates.export-all'                  => ['template_export_all',    'Export All Templates',      'Marketplace'],
            'templates.import'                      => ['template_import',        'Import Template',           'Marketplace'],
            'templates.import-url'                  => ['template_import',        'Import Template',           'Marketplace'],

            //  Projects & Collaboration 
            'projects.index'                        => ['projects',               'Projects',                  'Projects & Collaboration'],
            'projects.create'                       => ['project_create',         'Create Project',            'Projects & Collaboration'],
            'projects.store'                        => ['project_create',         'Create Project',            'Projects & Collaboration'],
            'projects.show'                         => ['project_view',           'View Project',              'Projects & Collaboration'],
            'projects.edit'                         => ['project_edit',           'Edit Project',              'Projects & Collaboration'],
            'projects.update'                       => ['project_edit',           'Edit Project',              'Projects & Collaboration'],
            'projects.destroy'                      => ['project_delete',         'Delete Project',            'Projects & Collaboration'],
            'projects.collaboration.index'          => ['collaboration',          'Team Collaboration',        'Projects & Collaboration'],
            'projects.collaboration.invite'         => ['collab_invite',          'Invite Team Member',        'Projects & Collaboration'],
            'projects.collaboration.update-role'    => ['collab_role',            'Update Member Role',        'Projects & Collaboration'],
            'projects.collaboration.remove-member'  => ['collab_remove',          'Remove Member',             'Projects & Collaboration'],
            'projects.collaboration.leave'          => ['collab_leave',           'Leave Project',             'Projects & Collaboration'],
            'projects.collaboration.workspace'      => ['collab_workspace',       'Team Workspace',            'Projects & Collaboration'],
            'projects.collaboration.activity'       => ['collab_activity',        'Activity Feed',             'Projects & Collaboration'],
            'projects.content.index'                => ['project_content',        'Project Content',           'Projects & Collaboration'],
            'projects.content.create'               => ['project_content_create', 'Create Project Content',    'Projects & Collaboration'],
            'projects.content.store'                => ['project_content_create', 'Create Project Content',    'Projects & Collaboration'],
            'projects.content.show'                 => ['project_content_view',   'View Project Content',      'Projects & Collaboration'],
            'projects.content.edit'                 => ['project_content_edit',   'Edit Project Content',      'Projects & Collaboration'],
            'projects.content.update'               => ['project_content_edit',   'Edit Project Content',      'Projects & Collaboration'],
            'projects.content.destroy'              => ['project_content_delete', 'Delete Project Content',    'Projects & Collaboration'],
            'projects.content.submit-review'        => ['project_content_review', 'Submit for Review',         'Projects & Collaboration'],
            'projects.content.approve'              => ['project_content_approve','Approve Content',           'Projects & Collaboration'],
            'projects.content.reject'               => ['project_content_reject', 'Reject Content',            'Projects & Collaboration'],
            'projects.content.comments.store'       => ['project_comment',        'Add Comment',               'Projects & Collaboration'],
            'projects.content.comments.resolve'     => ['project_comment_resolve','Resolve Comment',           'Projects & Collaboration'],
            'projects.content.versions'             => ['project_versions',       'Version History',           'Projects & Collaboration'],
            'projects.content.restore-version'      => ['project_restore',        'Restore Version',           'Projects & Collaboration'],
            'invitations.accept'                    => ['invitation_accept',      'Accept Invitation',         'Projects & Collaboration'],
            'invitations.decline'                   => ['invitation_decline',     'Decline Invitation',        'Projects & Collaboration'],

            //  Orders & Services 
            'orders.index'                          => ['orders',                 'Orders',                    'Orders & Services'],
            'orders.create'                         => ['order_create',           'Create Order',              'Orders & Services'],
            'orders.store'                          => ['order_create',           'Create Order',              'Orders & Services'],
            'orders.show'                           => ['order_view',             'View Order',                'Orders & Services'],
            'orders.revision'                       => ['order_revision',         'Request Revision',          'Orders & Services'],
            'orders.rate'                           => ['order_rate',             'Rate Order',                'Orders & Services'],
            'orders.approve'                        => ['order_approve',          'Approve Order',             'Orders & Services'],
            'orders.dispute'                        => ['order_dispute',          'Dispute Order',             'Orders & Services'],
            'browse.operators'                      => ['browse_operators',       'Browse Operators',          'Orders & Services'],
            'request.order'                         => ['request_order',          'Request Order',             'Orders & Services'],
            'payment.show'                          => ['payment_view',           'View Payment',              'Orders & Services'],
            'payment.submit-proof'                  => ['payment_proof',          'Submit Payment Proof',      'Orders & Services'],

            //  Copywriting 
            'copywriting.index'                     => ['copywriting',            'Copywriting Requests',      'Orders & Services'],
            'copywriting.create'                    => ['copywriting_create',     'Create Copywriting',        'Orders & Services'],
            'copywriting.store'                     => ['copywriting_create',     'Create Copywriting',        'Orders & Services'],
            'copywriting.show'                      => ['copywriting_view',       'View Copywriting',          'Orders & Services'],
            'copywriting.revision'                  => ['copywriting_revision',   'Copywriting Revision',      'Orders & Services'],
            'copywriting.rate'                      => ['copywriting_rate',       'Rate Copywriting',          'Orders & Services'],

            //  Caption History & ML 
            'caption-history.index'                 => ['caption_history',        'Caption History',           'History & ML'],
            'caption-history.show'                  => ['caption_history_view',   'View Caption History',      'History & ML'],
            'caption-history.destroy'               => ['caption_history_delete', 'Delete Caption History',    'History & ML'],
            'caption-history.clear-all'             => ['caption_history_clear',  'Clear Caption History',     'History & ML'],
            'my-stats'                              => ['my_stats',               'My ML Stats',               'History & ML'],
            'api.caption.rate'                      => ['caption_rating',         'Rate Caption',              'History & ML'],
            'api.ml.status'                         => ['ml_status',              'ML Status',                 'History & ML'],
            'api.ml.preview'                        => ['ml_preview',             'ML Suggestions Preview',    'History & ML'],
            'api.ml.weekly-trends'                  => ['ml_weekly_trends',       'ML Weekly Trends',          'History & ML'],
            'api.ml.refresh'                        => ['ml_refresh',             'Refresh ML Suggestions',    'History & ML'],
            'api.ml.cache-stats'                    => ['ml_cache_stats',         'ML Cache Stats',            'History & ML'],

            //  Brand Voice 
            'brand-voices.index'                    => ['brand_voice',            'Brand Voice',               'Personalization'],
            'brand-voices.store'                    => ['brand_voice_create',     'Create Brand Voice',        'Personalization'],
            'brand-voices.destroy'                  => ['brand_voice_delete',     'Delete Brand Voice',        'Personalization'],

            //  Subscription 
            'subscription.index'                    => ['subscription',           'Subscription',              'Account'],
            'subscription.trial'                    => ['subscription_trial',     'Start Trial',               'Account'],
            'subscription.checkout'                 => ['subscription_checkout',  'Subscription Checkout',     'Account'],
            'subscription.pay'                      => ['subscription_pay',       'Pay Subscription',          'Account'],
            'subscription.cancel'                   => ['subscription_cancel',    'Cancel Subscription',       'Account'],

            //  Referral 
            'client.referral.index'                 => ['referral',               'Referral Program',          'Account'],
            'client.referral.withdraw'              => ['referral_withdraw',      'Referral Withdrawal',       'Account'],
            'client.referral.withdraw.store'        => ['referral_withdraw',      'Referral Withdrawal',       'Account'],

            //  Feedback 
            'feedback.index'                        => ['feedback',               'Feedback',                  'Account'],
            'feedback.create'                       => ['feedback_create',        'Submit Feedback',           'Account'],
            'feedback.store'                        => ['feedback_create',        'Submit Feedback',           'Account'],
            'feedback.show'                         => ['feedback_view',          'View Feedback',             'Account'],
        ];
    }

    public static function resolveFeature(string $routeName): ?array
    {
        return self::featureMap()[$routeName] ?? null;
    }
}
