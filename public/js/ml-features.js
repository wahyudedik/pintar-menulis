/**
 * 🤖 ML Features for AI Generator - Dynamic & AI-Powered
 * 
 * Handles dynamic ML suggestions that update daily with real trending data
 */

// ML State Management
const mlState = {
    status: null,
    preview: null,
    weeklyTrends: null,
    showMLPreview: false,
    lastUpdated: null,
};

/**
 * Initialize ML features
 */
async function initMLFeatures() {
    try {
        // Get ML status
        const status = await fetchMLStatus();
        mlState.status = status;
        mlState.lastUpdated = new Date().toISOString();

        return status;
    } catch (error) {
        console.error('ML Init Error:', error);
        return null;
    }
}

/**
 * Fetch ML status from API
 */
async function fetchMLStatus() {
    const response = await fetch('/api/ml/status', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (!response.ok) {
        throw new Error('Failed to fetch ML status');
    }

    return await response.json();
}

/**
 * Fetch dynamic ML preview data (updates daily)
 */
async function fetchMLPreview(industry = 'fashion', platform = 'instagram') {
    const response = await fetch(`/api/ml/preview?industry=${industry}&platform=${platform}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (!response.ok) {
        throw new Error('Failed to fetch ML preview');
    }

    const data = await response.json();
    mlState.preview = data;
    return data;
}

/**
 * Fetch weekly trend analysis
 */
async function fetchWeeklyTrends(industry = 'fashion') {
    const response = await fetch(`/api/ml/weekly-trends?industry=${industry}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (!response.ok) {
        throw new Error('Failed to fetch weekly trends');
    }

    const data = await response.json();
    mlState.weeklyTrends = data;
    return data;
}

/**
 * Toggle ML preview panel with fresh data
 */
async function toggleMLPreview(industry, platform) {
    if (!mlState.showMLPreview) {
        // Always load fresh preview data
        await fetchMLPreview(industry, platform);
    }
    mlState.showMLPreview = !mlState.showMLPreview;
}

/**
 * Refresh ML suggestions (force update)
 */
async function refreshMLSuggestions() {
    try {
        const response = await fetch('/api/ml/refresh', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            // Clear local cache and reload
            mlState.preview = null;
            mlState.weeklyTrends = null;
            return true;
        }
        return false;
    } catch (error) {
        console.error('Refresh ML suggestions error:', error);
        return false;
    }
}

/**
 * Get ML data for generation with dynamic suggestions
 */
function getMLDataForGeneration(formData) {
    const industry = extractIndustry(formData.category, formData.subcategory);

    return {
        industry: industry,
        goal: formData.goal || 'closing',
        use_google_api: mlState.status?.google_api_enabled || false,
        trending_data: mlState.preview?.trending_hashtags || [],
        current_trends: mlState.preview?.current_trends || [],
    };
}

/**
 * Extract industry from category/subcategory
 */
function extractIndustry(category, subcategory) {
    // Map categories to industries
    const industryMap = {
        'fashion_pakaian': 'fashion',
        'makanan_minuman': 'food',
        'kecantikan_skincare': 'beauty',
        'jasa_printing': 'printing',
        'jasa_fotografi': 'photography',
        'catering': 'catering',
        'tiktok_shop': 'tiktok_shop',
        'affiliate_shopee': 'shopee_affiliate',
        'dekorasi_rumah': 'home_decor',
        'kerajinan_tangan': 'handmade',
        'jasa_digital': 'digital_service',
        'otomotif': 'automotive',
    };

    return industryMap[category] || 'general';
}

/**
 * Format ML data for display with freshness indicator
 */
function formatMLData(mlData) {
    if (!mlData) return null;

    const generatedAt = mlData.generated_at ? new Date(mlData.generated_at) : new Date();
    const hoursAgo = Math.floor((new Date() - generatedAt) / (1000 * 60 * 60));

    return {
        hashtags: mlData.trending_hashtags || [],
        keywords: mlData.seasonal_keywords || [],
        hooks: mlData.best_hooks || [],
        ctas: mlData.best_ctas || [],
        topics: mlData.trending_topics || [],
        tips: mlData.engagement_tips || [],
        trends: mlData.current_trends || [],
        content_ideas: mlData.content_ideas || [],
        posting_times: mlData.optimal_posting_times || [],
        source: 'ai-generated',
        personalized: mlData.personalized || false,
        freshness: hoursAgo < 24 ? 'fresh' : hoursAgo < 48 ? 'recent' : 'older',
        last_updated: generatedAt.toLocaleDateString('id-ID'),
    };
}

/**
 * Alpine.js component for ML features with dynamic updates
 */
function mlComponent() {
    return {
        // State
        mlStatus: null,
        mlPreview: {},
        weeklyTrends: {},
        showMLPreview: false,
        refreshing: false,

        // Initialize
        async initML() {
            try {
                const status = await fetchMLStatus();
                this.mlStatus = status;
            } catch (error) {
                console.error('ML Init Error:', error);
            }
        },

        // Toggle ML preview with fresh data
        async toggleMLPreview() {
            if (!this.showMLPreview) {
                // Get industry from form
                const industry = this.getIndustryFromForm();
                const platform = this.form?.platform || 'instagram';

                try {
                    const preview = await fetchMLPreview(industry, platform);
                    this.mlPreview = preview;

                    // Also load weekly trends
                    const trends = await fetchWeeklyTrends(industry);
                    this.weeklyTrends = trends;
                } catch (error) {
                    console.error('ML Preview Error:', error);
                }
            }
            this.showMLPreview = !this.showMLPreview;
        },

        // Refresh suggestions manually
        async refreshSuggestions() {
            this.refreshing = true;
            try {
                const success = await refreshMLSuggestions();
                if (success) {
                    // Reload preview data
                    const industry = this.getIndustryFromForm();
                    const platform = this.form?.platform || 'instagram';

                    this.mlPreview = await fetchMLPreview(industry, platform);
                    this.weeklyTrends = await fetchWeeklyTrends(industry);

                    alert('✅ ML Suggestions berhasil diperbarui dengan data terbaru!');
                } else {
                    alert('❌ Gagal memperbarui suggestions. Coba lagi nanti.');
                }
            } catch (error) {
                console.error('Refresh error:', error);
                alert('❌ Terjadi kesalahan saat memperbarui suggestions.');
            } finally {
                this.refreshing = false;
            }
        },

        // Get industry from form
        getIndustryFromForm() {
            if (this.mode === 'simple') {
                const businessTypeMap = {
                    'fashion_clothing': 'fashion',
                    'food_beverage': 'food',
                    'beauty_skincare': 'beauty',
                    'printing_service': 'printing',
                    'photography': 'photography',
                    'catering': 'catering',
                    'tiktok_shop': 'tiktok_shop',
                    'shopee_affiliate': 'shopee_affiliate',
                    'home_decor': 'home_decor',
                    'handmade_craft': 'handmade',
                    'digital_service': 'digital_service',
                    'automotive': 'automotive',
                };
                return businessTypeMap[this.simpleForm?.business_type] || 'fashion';
            } else {
                return extractIndustry(this.form?.category, this.form?.subcategory);
            }
        },

        // Add ML data to generation params
        addMLDataToParams(params) {
            const industry = this.getIndustryFromForm();
            return {
                ...params,
                industry: industry,
                goal: params.goal || 'closing',
                use_google_api: this.mlStatus?.google_api_enabled || false,
                ml_suggestions: this.mlPreview || {},
            };
        },

        // Get freshness indicator
        getFreshnessIndicator() {
            if (!this.mlPreview?.generated_at) return '';

            const generatedAt = new Date(this.mlPreview.generated_at);
            const hoursAgo = Math.floor((new Date() - generatedAt) / (1000 * 60 * 60));

            if (hoursAgo < 1) return '🟢 Baru saja diperbarui';
            if (hoursAgo < 6) return '🟢 Diperbarui ' + hoursAgo + ' jam lalu';
            if (hoursAgo < 24) return '🟡 Diperbarui hari ini';
            if (hoursAgo < 48) return '🟡 Diperbarui kemarin';
            return '🔴 Perlu diperbarui';
        },

        // Check if data is fresh (less than 24 hours)
        isDataFresh() {
            if (!this.mlPreview?.generated_at) return false;

            const generatedAt = new Date(this.mlPreview.generated_at);
            const hoursAgo = Math.floor((new Date() - generatedAt) / (1000 * 60 * 60));

            return hoursAgo < 24;
        },
    };
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initMLFeatures,
        fetchMLStatus,
        fetchMLPreview,
        toggleMLPreview,
        enableGoogleAPI,
        getMLDataForGeneration,
        formatMLData,
        mlComponent,
    };
}
