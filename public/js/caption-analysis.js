// Caption Analysis Module
const captionAnalysis = {
    // Analyze caption quality
    async analyzeQuality(caption, platform = 'instagram', industry = null) {
        try {
            const response = await fetch('/api/analysis/score-caption', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    caption,
                    platform,
                    industry,
                }),
            });

            if (!response.ok) throw new Error('Failed to analyze quality');
            return await response.json();
        } catch (error) {
            console.error('Quality analysis error:', error);
            return { success: false, error: error.message };
        }
    },

    // Analyze sentiment
    async analyzeSentiment(caption) {
        try {
            const response = await fetch('/api/analysis/sentiment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ caption }),
            });

            if (!response.ok) throw new Error('Failed to analyze sentiment');
            return await response.json();
        } catch (error) {
            console.error('Sentiment analysis error:', error);
            return { success: false, error: error.message };
        }
    },

    // Get recommendations
    async getRecommendations(caption, platform = 'instagram', targetAudience = null) {
        try {
            const response = await fetch('/api/analysis/recommendations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    caption,
                    platform,
                    target_audience: targetAudience,
                }),
            });

            if (!response.ok) throw new Error('Failed to get recommendations');
            return await response.json();
        } catch (error) {
            console.error('Recommendations error:', error);
            return { success: false, error: error.message };
        }
    },

    // Analyze image
    async analyzeImage(file) {
        try {
            const formData = new FormData();
            formData.append('image', file);

            const response = await fetch('/api/analysis/image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });

            if (!response.ok) throw new Error('Failed to analyze image');
            return await response.json();
        } catch (error) {
            console.error('Image analysis error:', error);
            return { success: false, error: error.message };
        }
    },

    // Get analysis dashboard
    async getDashboard() {
        try {
            const response = await fetch('/api/analysis/dashboard', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            if (!response.ok) throw new Error('Failed to get dashboard');
            return await response.json();
        } catch (error) {
            console.error('Dashboard error:', error);
            return { success: false, error: error.message };
        }
    },

    // Get analysis history
    async getHistory(type = 'quality') {
        try {
            const response = await fetch(`/api/analysis/history?type=${type}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            if (!response.ok) throw new Error('Failed to get history');
            return await response.json();
        } catch (error) {
            console.error('History error:', error);
            return { success: false, error: error.message };
        }
    },

    // Analyze campaign
    async analyzeCampaign(captions, ratings, campaignName = null) {
        try {
            const response = await fetch('/api/analysis/campaign', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    captions,
                    ratings,
                    campaign_name: campaignName,
                }),
            });

            if (!response.ok) throw new Error('Failed to analyze campaign');
            return await response.json();
        } catch (error) {
            console.error('Campaign analysis error:', error);
            return { success: false, error: error.message };
        }
    },

    // Analyze article
    async analyzeArticle(title, content, keywords = null) {
        try {
            const response = await fetch('/api/analysis/article', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    title,
                    content,
                    keywords,
                }),
            });

            if (!response.ok) throw new Error('Failed to analyze article');
            return await response.json();
        } catch (error) {
            console.error('Article analysis error:', error);
            return { success: false, error: error.message };
        }
    },
};

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = captionAnalysis;
}
