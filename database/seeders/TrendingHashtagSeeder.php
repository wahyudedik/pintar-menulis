<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrendingHashtag;
use Carbon\Carbon;

class TrendingHashtagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Clear existing data
        TrendingHashtag::truncate();
        
        $hashtags = $this->getHashtagData();
        
        foreach ($hashtags as $hashtag) {
            TrendingHashtag::create([
                'hashtag' => $hashtag['hashtag'],
                'platform' => $hashtag['platform'],
                'trend_score' => $hashtag['trend_score'],
                'usage_count' => $hashtag['usage_count'],
                'engagement_rate' => $hashtag['engagement_rate'],
                'category' => $hashtag['category'],
                'country' => 'ID',
                'last_updated' => $now,
            ]);
        }
        
        $this->command->info('✅ Seeded ' . count($hashtags) . ' trending hashtags');
    }
    
    /**
     * Get hashtag data for all platforms and categories
     */
    private function getHashtagData(): array
    {
        return array_merge(
            $this->getInstagramHashtags(),
            $this->getTikTokHashtags(),
            $this->getFacebookHashtags(),
            $this->getYouTubeHashtags(),
            $this->getTwitterHashtags(),
            $this->getLinkedInHashtags()
        );
    }
    
    /**
     * Instagram Trending Hashtags
     */
    private function getInstagramHashtags(): array
    {
        return [
            // Fashion & Beauty
            ['hashtag' => '#ootd', 'platform' => 'instagram', 'trend_score' => 95, 'usage_count' => 500000, 'engagement_rate' => 4.5, 'category' => 'fashion'],
            ['hashtag' => '#ootdindo', 'platform' => 'instagram', 'trend_score' => 92, 'usage_count' => 350000, 'engagement_rate' => 4.8, 'category' => 'fashion'],
            ['hashtag' => '#fashionindo', 'platform' => 'instagram', 'trend_score' => 88, 'usage_count' => 280000, 'engagement_rate' => 4.2, 'category' => 'fashion'],
            ['hashtag' => '#bajumurah', 'platform' => 'instagram', 'trend_score' => 85, 'usage_count' => 250000, 'engagement_rate' => 5.1, 'category' => 'fashion'],
            ['hashtag' => '#fashionhijab', 'platform' => 'instagram', 'trend_score' => 90, 'usage_count' => 320000, 'engagement_rate' => 4.6, 'category' => 'fashion'],
            ['hashtag' => '#hijabstyle', 'platform' => 'instagram', 'trend_score' => 87, 'usage_count' => 290000, 'engagement_rate' => 4.4, 'category' => 'fashion'],
            ['hashtag' => '#makeupindo', 'platform' => 'instagram', 'trend_score' => 89, 'usage_count' => 310000, 'engagement_rate' => 4.7, 'category' => 'beauty'],
            ['hashtag' => '#skincareindonesia', 'platform' => 'instagram', 'trend_score' => 91, 'usage_count' => 340000, 'engagement_rate' => 4.9, 'category' => 'beauty'],
            ['hashtag' => '#beautyblogger', 'platform' => 'instagram', 'trend_score' => 86, 'usage_count' => 270000, 'engagement_rate' => 4.3, 'category' => 'beauty'],
            ['hashtag' => '#skincarerutin', 'platform' => 'instagram', 'trend_score' => 84, 'usage_count' => 240000, 'engagement_rate' => 4.1, 'category' => 'beauty'],
            
            // Food & Beverage
            ['hashtag' => '#kuliner', 'platform' => 'instagram', 'trend_score' => 94, 'usage_count' => 480000, 'engagement_rate' => 5.2, 'category' => 'food'],
            ['hashtag' => '#kulinerindo', 'platform' => 'instagram', 'trend_score' => 93, 'usage_count' => 460000, 'engagement_rate' => 5.0, 'category' => 'food'],
            ['hashtag' => '#makananenak', 'platform' => 'instagram', 'trend_score' => 90, 'usage_count' => 380000, 'engagement_rate' => 4.8, 'category' => 'food'],
            ['hashtag' => '#jajanan', 'platform' => 'instagram', 'trend_score' => 88, 'usage_count' => 350000, 'engagement_rate' => 4.6, 'category' => 'food'],
            ['hashtag' => '#cemilan', 'platform' => 'instagram', 'trend_score' => 86, 'usage_count' => 320000, 'engagement_rate' => 4.4, 'category' => 'food'],
            ['hashtag' => '#kulinerindonesia', 'platform' => 'instagram', 'trend_score' => 92, 'usage_count' => 420000, 'engagement_rate' => 4.9, 'category' => 'food'],
            ['hashtag' => '#makananpedas', 'platform' => 'instagram', 'trend_score' => 85, 'usage_count' => 280000, 'engagement_rate' => 4.2, 'category' => 'food'],
            ['hashtag' => '#kopi', 'platform' => 'instagram', 'trend_score' => 89, 'usage_count' => 360000, 'engagement_rate' => 4.7, 'category' => 'food'],
            ['hashtag' => '#kopiindonesia', 'platform' => 'instagram', 'trend_score' => 87, 'usage_count' => 330000, 'engagement_rate' => 4.5, 'category' => 'food'],
            ['hashtag' => '#minuman', 'platform' => 'instagram', 'trend_score' => 84, 'usage_count' => 260000, 'engagement_rate' => 4.1, 'category' => 'food'],
            
            // UMKM & Business
            ['hashtag' => '#umkm', 'platform' => 'instagram', 'trend_score' => 96, 'usage_count' => 520000, 'engagement_rate' => 5.3, 'category' => 'business'],
            ['hashtag' => '#umkmindonesia', 'platform' => 'instagram', 'trend_score' => 94, 'usage_count' => 490000, 'engagement_rate' => 5.1, 'category' => 'business'],
            ['hashtag' => '#umkmlokal', 'platform' => 'instagram', 'trend_score' => 91, 'usage_count' => 410000, 'engagement_rate' => 4.8, 'category' => 'business'],
            ['hashtag' => '#umkmnaik', 'platform' => 'instagram', 'trend_score' => 89, 'usage_count' => 370000, 'engagement_rate' => 4.6, 'category' => 'business'],
            ['hashtag' => '#bisnisrumahan', 'platform' => 'instagram', 'trend_score' => 87, 'usage_count' => 340000, 'engagement_rate' => 4.4, 'category' => 'business'],
            ['hashtag' => '#bisnisindo', 'platform' => 'instagram', 'trend_score' => 85, 'usage_count' => 310000, 'engagement_rate' => 4.2, 'category' => 'business'],
            ['hashtag' => '#jualanonline', 'platform' => 'instagram', 'trend_score' => 92, 'usage_count' => 440000, 'engagement_rate' => 4.9, 'category' => 'business'],
            ['hashtag' => '#olshopindo', 'platform' => 'instagram', 'trend_score' => 90, 'usage_count' => 390000, 'engagement_rate' => 4.7, 'category' => 'business'],
            ['hashtag' => '#onlineshop', 'platform' => 'instagram', 'trend_score' => 88, 'usage_count' => 360000, 'engagement_rate' => 4.5, 'category' => 'business'],
            ['hashtag' => '#bisnislokal', 'platform' => 'instagram', 'trend_score' => 86, 'usage_count' => 330000, 'engagement_rate' => 4.3, 'category' => 'business'],
            
            // General Indonesia
            ['hashtag' => '#indonesia', 'platform' => 'instagram', 'trend_score' => 98, 'usage_count' => 600000, 'engagement_rate' => 5.5, 'category' => 'general'],
            ['hashtag' => '#jakarta', 'platform' => 'instagram', 'trend_score' => 95, 'usage_count' => 510000, 'engagement_rate' => 5.2, 'category' => 'general'],
            ['hashtag' => '#bandung', 'platform' => 'instagram', 'trend_score' => 90, 'usage_count' => 400000, 'engagement_rate' => 4.8, 'category' => 'general'],
            ['hashtag' => '#surabaya', 'platform' => 'instagram', 'trend_score' => 88, 'usage_count' => 370000, 'engagement_rate' => 4.6, 'category' => 'general'],
            ['hashtag' => '#bali', 'platform' => 'instagram', 'trend_score' => 93, 'usage_count' => 470000, 'engagement_rate' => 5.0, 'category' => 'general'],
            ['hashtag' => '#yogyakarta', 'platform' => 'instagram', 'trend_score' => 87, 'usage_count' => 350000, 'engagement_rate' => 4.5, 'category' => 'general'],
            ['hashtag' => '#medan', 'platform' => 'instagram', 'trend_score' => 85, 'usage_count' => 320000, 'engagement_rate' => 4.3, 'category' => 'general'],
            ['hashtag' => '#semarang', 'platform' => 'instagram', 'trend_score' => 83, 'usage_count' => 290000, 'engagement_rate' => 4.1, 'category' => 'general'],
        ];
    }
    
    /**
     * TikTok Trending Hashtags
     */
    private function getTikTokHashtags(): array
    {
        return [
            // Viral & Trending
            ['hashtag' => '#fyp', 'platform' => 'tiktok', 'trend_score' => 99, 'usage_count' => 800000, 'engagement_rate' => 6.5, 'category' => 'general'],
            ['hashtag' => '#foryou', 'platform' => 'tiktok', 'trend_score' => 98, 'usage_count' => 750000, 'engagement_rate' => 6.3, 'category' => 'general'],
            ['hashtag' => '#foryoupage', 'platform' => 'tiktok', 'trend_score' => 97, 'usage_count' => 720000, 'engagement_rate' => 6.2, 'category' => 'general'],
            ['hashtag' => '#viral', 'platform' => 'tiktok', 'trend_score' => 96, 'usage_count' => 680000, 'engagement_rate' => 6.0, 'category' => 'general'],
            ['hashtag' => '#trending', 'platform' => 'tiktok', 'trend_score' => 95, 'usage_count' => 650000, 'engagement_rate' => 5.9, 'category' => 'general'],
            ['hashtag' => '#tiktokindo', 'platform' => 'tiktok', 'trend_score' => 94, 'usage_count' => 620000, 'engagement_rate' => 5.8, 'category' => 'general'],
            ['hashtag' => '#tiktokindonesia', 'platform' => 'tiktok', 'trend_score' => 93, 'usage_count' => 590000, 'engagement_rate' => 5.7, 'category' => 'general'],
            
            // Fashion & Beauty
            ['hashtag' => '#ootdtiktok', 'platform' => 'tiktok', 'trend_score' => 91, 'usage_count' => 480000, 'engagement_rate' => 5.5, 'category' => 'fashion'],
            ['hashtag' => '#fashiontiktok', 'platform' => 'tiktok', 'trend_score' => 89, 'usage_count' => 450000, 'engagement_rate' => 5.3, 'category' => 'fashion'],
            ['hashtag' => '#makeuptutorial', 'platform' => 'tiktok', 'trend_score' => 92, 'usage_count' => 510000, 'engagement_rate' => 5.6, 'category' => 'beauty'],
            ['hashtag' => '#skincareroutine', 'platform' => 'tiktok', 'trend_score' => 90, 'usage_count' => 470000, 'engagement_rate' => 5.4, 'category' => 'beauty'],
            ['hashtag' => '#beautytips', 'platform' => 'tiktok', 'trend_score' => 88, 'usage_count' => 440000, 'engagement_rate' => 5.2, 'category' => 'beauty'],
            
            // Food
            ['hashtag' => '#foodtiktok', 'platform' => 'tiktok', 'trend_score' => 93, 'usage_count' => 560000, 'engagement_rate' => 5.7, 'category' => 'food'],
            ['hashtag' => '#resepmasakan', 'platform' => 'tiktok', 'trend_score' => 91, 'usage_count' => 490000, 'engagement_rate' => 5.5, 'category' => 'food'],
            ['hashtag' => '#masakantiktok', 'platform' => 'tiktok', 'trend_score' => 89, 'usage_count' => 460000, 'engagement_rate' => 5.3, 'category' => 'food'],
            ['hashtag' => '#mukbang', 'platform' => 'tiktok', 'trend_score' => 90, 'usage_count' => 480000, 'engagement_rate' => 5.4, 'category' => 'food'],
            
            // Business & Tips
            ['hashtag' => '#bisnistiktok', 'platform' => 'tiktok', 'trend_score' => 87, 'usage_count' => 420000, 'engagement_rate' => 5.1, 'category' => 'business'],
            ['hashtag' => '#tipsusaha', 'platform' => 'tiktok', 'trend_score' => 86, 'usage_count' => 400000, 'engagement_rate' => 5.0, 'category' => 'business'],
            ['hashtag' => '#jualanonlinetiktok', 'platform' => 'tiktok', 'trend_score' => 88, 'usage_count' => 430000, 'engagement_rate' => 5.2, 'category' => 'business'],
            ['hashtag' => '#tiktokshop', 'platform' => 'tiktok', 'trend_score' => 92, 'usage_count' => 520000, 'engagement_rate' => 5.6, 'category' => 'business'],
            ['hashtag' => '#tiktokaffiliate', 'platform' => 'tiktok', 'trend_score' => 85, 'usage_count' => 380000, 'engagement_rate' => 4.9, 'category' => 'business'],
        ];
    }
    
    /**
     * Facebook Trending Hashtags
     */
    private function getFacebookHashtags(): array
    {
        return [
            // General
            ['hashtag' => '#facebook', 'platform' => 'facebook', 'trend_score' => 90, 'usage_count' => 400000, 'engagement_rate' => 3.8, 'category' => 'general'],
            ['hashtag' => '#facebookindonesia', 'platform' => 'facebook', 'trend_score' => 88, 'usage_count' => 370000, 'engagement_rate' => 3.6, 'category' => 'general'],
            ['hashtag' => '#indonesiaku', 'platform' => 'facebook', 'trend_score' => 87, 'usage_count' => 350000, 'engagement_rate' => 3.5, 'category' => 'general'],
            
            // Business
            ['hashtag' => '#umkmfacebook', 'platform' => 'facebook', 'trend_score' => 89, 'usage_count' => 380000, 'engagement_rate' => 3.7, 'category' => 'business'],
            ['hashtag' => '#bisnisfacebook', 'platform' => 'facebook', 'trend_score' => 86, 'usage_count' => 340000, 'engagement_rate' => 3.4, 'category' => 'business'],
            ['hashtag' => '#jualanonlinefb', 'platform' => 'facebook', 'trend_score' => 85, 'usage_count' => 320000, 'engagement_rate' => 3.3, 'category' => 'business'],
            ['hashtag' => '#marketplacefb', 'platform' => 'facebook', 'trend_score' => 84, 'usage_count' => 310000, 'engagement_rate' => 3.2, 'category' => 'business'],
            
            // Fashion & Food
            ['hashtag' => '#fashionfb', 'platform' => 'facebook', 'trend_score' => 83, 'usage_count' => 290000, 'engagement_rate' => 3.1, 'category' => 'fashion'],
            ['hashtag' => '#kulinerfb', 'platform' => 'facebook', 'trend_score' => 85, 'usage_count' => 330000, 'engagement_rate' => 3.3, 'category' => 'food'],
            ['hashtag' => '#makananenak', 'platform' => 'facebook', 'trend_score' => 84, 'usage_count' => 300000, 'engagement_rate' => 3.2, 'category' => 'food'],
        ];
    }
    
    /**
     * YouTube Trending Hashtags
     */
    private function getYouTubeHashtags(): array
    {
        return [
            // General
            ['hashtag' => '#youtube', 'platform' => 'youtube', 'trend_score' => 92, 'usage_count' => 450000, 'engagement_rate' => 4.2, 'category' => 'general'],
            ['hashtag' => '#youtubeindonesia', 'platform' => 'youtube', 'trend_score' => 90, 'usage_count' => 420000, 'engagement_rate' => 4.0, 'category' => 'general'],
            ['hashtag' => '#shorts', 'platform' => 'youtube', 'trend_score' => 94, 'usage_count' => 500000, 'engagement_rate' => 4.5, 'category' => 'general'],
            ['hashtag' => '#youtubeshorts', 'platform' => 'youtube', 'trend_score' => 93, 'usage_count' => 480000, 'engagement_rate' => 4.3, 'category' => 'general'],
            
            // Content Types
            ['hashtag' => '#tutorial', 'platform' => 'youtube', 'trend_score' => 89, 'usage_count' => 390000, 'engagement_rate' => 3.9, 'category' => 'education'],
            ['hashtag' => '#tips', 'platform' => 'youtube', 'trend_score' => 88, 'usage_count' => 370000, 'engagement_rate' => 3.8, 'category' => 'education'],
            ['hashtag' => '#review', 'platform' => 'youtube', 'trend_score' => 87, 'usage_count' => 350000, 'engagement_rate' => 3.7, 'category' => 'general'],
            ['hashtag' => '#vlog', 'platform' => 'youtube', 'trend_score' => 86, 'usage_count' => 330000, 'engagement_rate' => 3.6, 'category' => 'general'],
            
            // Business
            ['hashtag' => '#bisnisyoutube', 'platform' => 'youtube', 'trend_score' => 85, 'usage_count' => 310000, 'engagement_rate' => 3.5, 'category' => 'business'],
            ['hashtag' => '#motivasi', 'platform' => 'youtube', 'trend_score' => 87, 'usage_count' => 340000, 'engagement_rate' => 3.7, 'category' => 'business'],
        ];
    }
    
    /**
     * Twitter/X Trending Hashtags
     */
    private function getTwitterHashtags(): array
    {
        return [
            // General
            ['hashtag' => '#Indonesia', 'platform' => 'twitter', 'trend_score' => 95, 'usage_count' => 520000, 'engagement_rate' => 4.8, 'category' => 'general'],
            ['hashtag' => '#TwitterIndonesia', 'platform' => 'twitter', 'trend_score' => 91, 'usage_count' => 440000, 'engagement_rate' => 4.4, 'category' => 'general'],
            ['hashtag' => '#TrendingNow', 'platform' => 'twitter', 'trend_score' => 89, 'usage_count' => 400000, 'engagement_rate' => 4.2, 'category' => 'general'],
            
            // Business & News
            ['hashtag' => '#UMKM', 'platform' => 'twitter', 'trend_score' => 88, 'usage_count' => 380000, 'engagement_rate' => 4.1, 'category' => 'business'],
            ['hashtag' => '#BisnisOnline', 'platform' => 'twitter', 'trend_score' => 86, 'usage_count' => 350000, 'engagement_rate' => 3.9, 'category' => 'business'],
            ['hashtag' => '#Startup', 'platform' => 'twitter', 'trend_score' => 85, 'usage_count' => 330000, 'engagement_rate' => 3.8, 'category' => 'business'],
            ['hashtag' => '#TechNews', 'platform' => 'twitter', 'trend_score' => 87, 'usage_count' => 360000, 'engagement_rate' => 4.0, 'category' => 'technology'],
        ];
    }
    
    /**
     * LinkedIn Trending Hashtags
     */
    private function getLinkedInHashtags(): array
    {
        return [
            // Professional
            ['hashtag' => '#LinkedIn', 'platform' => 'linkedin', 'trend_score' => 90, 'usage_count' => 380000, 'engagement_rate' => 3.5, 'category' => 'professional'],
            ['hashtag' => '#LinkedInIndonesia', 'platform' => 'linkedin', 'trend_score' => 88, 'usage_count' => 350000, 'engagement_rate' => 3.3, 'category' => 'professional'],
            ['hashtag' => '#CareerDevelopment', 'platform' => 'linkedin', 'trend_score' => 87, 'usage_count' => 330000, 'engagement_rate' => 3.2, 'category' => 'professional'],
            ['hashtag' => '#JobOpportunity', 'platform' => 'linkedin', 'trend_score' => 86, 'usage_count' => 310000, 'engagement_rate' => 3.1, 'category' => 'professional'],
            ['hashtag' => '#Hiring', 'platform' => 'linkedin', 'trend_score' => 89, 'usage_count' => 370000, 'engagement_rate' => 3.4, 'category' => 'professional'],
            ['hashtag' => '#JobVacancy', 'platform' => 'linkedin', 'trend_score' => 85, 'usage_count' => 300000, 'engagement_rate' => 3.0, 'category' => 'professional'],
            
            // Business
            ['hashtag' => '#BusinessIndonesia', 'platform' => 'linkedin', 'trend_score' => 84, 'usage_count' => 280000, 'engagement_rate' => 2.9, 'category' => 'business'],
            ['hashtag' => '#Entrepreneurship', 'platform' => 'linkedin', 'trend_score' => 86, 'usage_count' => 320000, 'engagement_rate' => 3.1, 'category' => 'business'],
            ['hashtag' => '#Leadership', 'platform' => 'linkedin', 'trend_score' => 85, 'usage_count' => 290000, 'engagement_rate' => 3.0, 'category' => 'business'],
            ['hashtag' => '#Innovation', 'platform' => 'linkedin', 'trend_score' => 83, 'usage_count' => 270000, 'engagement_rate' => 2.8, 'category' => 'business'],
        ];
    }
}
