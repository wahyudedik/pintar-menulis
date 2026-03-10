<?php

namespace Database\Seeders;

use App\Models\MLOptimizedData;
use Illuminate\Database\Seeder;

class MLOptimizedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedHashtags();
        $this->seedKeywords();
        $this->seedTopics();
        $this->seedHooks();
        $this->seedCTAs();
    }
    
    private function seedHashtags(): void
    {
        $hashtags = [
            // Fashion
            ['industry' => 'fashion', 'data' => '#fashion', 'score' => 85],
            ['industry' => 'fashion', 'data' => '#ootd', 'score' => 90],
            ['industry' => 'fashion', 'data' => '#fashionista', 'score' => 80],
            ['industry' => 'fashion', 'data' => '#bajumurah', 'score' => 88],
            ['industry' => 'fashion', 'data' => '#olshopindo', 'score' => 92],
            
            // Food
            ['industry' => 'food', 'data' => '#kuliner', 'score' => 90],
            ['industry' => 'food', 'data' => '#makananenak', 'score' => 88],
            ['industry' => 'food', 'data' => '#jajanan', 'score' => 85],
            ['industry' => 'food', 'data' => '#foodie', 'score' => 82],
            ['industry' => 'food', 'data' => '#makananhalal', 'score' => 87],
            
            // Beauty
            ['industry' => 'beauty', 'data' => '#skincare', 'score' => 92],
            ['industry' => 'beauty', 'data' => '#skincarerutin', 'score' => 88],
            ['industry' => 'beauty', 'data' => '#glowingskin', 'score' => 85],
            ['industry' => 'beauty', 'data' => '#beautytips', 'score' => 83],
            ['industry' => 'beauty', 'data' => '#bpom', 'score' => 90],
        ];
        
        foreach ($hashtags as $hashtag) {
            MLOptimizedData::create([
                'type' => 'hashtag',
                'industry' => $hashtag['industry'],
                'platform' => 'instagram',
                'data' => $hashtag['data'],
                'performance_score' => $hashtag['score'],
                'usage_count' => 0,
                'is_active' => true,
                'last_trained_at' => now(),
            ]);
        }
    }
    
    private function seedKeywords(): void
    {
        $keywords = [
            // Fashion
            ['industry' => 'fashion', 'data' => 'murah', 'score' => 90],
            ['industry' => 'fashion', 'data' => 'berkualitas', 'score' => 85],
            ['industry' => 'fashion', 'data' => 'trendy', 'score' => 88],
            
            // Food
            ['industry' => 'food', 'data' => 'enak', 'score' => 92],
            ['industry' => 'food', 'data' => 'halal', 'score' => 90],
            ['industry' => 'food', 'data' => 'fresh', 'score' => 87],
            
            // Beauty
            ['industry' => 'beauty', 'data' => 'glowing', 'score' => 88],
            ['industry' => 'beauty', 'data' => 'aman', 'score' => 92],
            ['industry' => 'beauty', 'data' => 'teruji', 'score' => 85],
        ];
        
        foreach ($keywords as $keyword) {
            MLOptimizedData::create([
                'type' => 'keyword',
                'industry' => $keyword['industry'],
                'platform' => 'instagram',
                'data' => $keyword['data'],
                'performance_score' => $keyword['score'],
                'usage_count' => 0,
                'is_active' => true,
                'last_trained_at' => now(),
                'metadata' => ['search_volume' => rand(1000, 10000)],
            ]);
        }
    }
    
    private function seedTopics(): void
    {
        $topics = [
            ['industry' => 'fashion', 'data' => 'Koleksi terbaru hadir dengan desain yang lebih modern', 'score' => 85],
            ['industry' => 'food', 'data' => 'Menu spesial hari ini yang wajib kamu coba', 'score' => 88],
            ['industry' => 'beauty', 'data' => 'Rahasia kulit glowing dalam 7 hari', 'score' => 90],
        ];
        
        foreach ($topics as $topic) {
            MLOptimizedData::create([
                'type' => 'topic',
                'industry' => $topic['industry'],
                'platform' => 'instagram',
                'data' => $topic['data'],
                'performance_score' => $topic['score'],
                'usage_count' => 0,
                'is_active' => true,
                'last_trained_at' => now(),
                'metadata' => ['engagement_rate' => rand(5, 15)],
            ]);
        }
    }
    
    private function seedHooks(): void
    {
        $hooks = [
            ['industry' => 'fashion', 'tone' => 'casual', 'data' => 'Kak, udah liat koleksi terbaru belum?', 'score' => 88],
            ['industry' => 'fashion', 'tone' => 'persuasive', 'data' => 'Jangan sampai kehabisan! Stock terbatas!', 'score' => 90],
            ['industry' => 'food', 'tone' => 'casual', 'data' => 'Laper? Cobain menu spesial kita yuk!', 'score' => 87],
            ['industry' => 'beauty', 'tone' => 'educational', 'data' => 'Tau gak sih rahasia kulit glowing?', 'score' => 92],
        ];
        
        foreach ($hooks as $hook) {
            MLOptimizedData::create([
                'type' => 'hook',
                'industry' => $hook['industry'],
                'platform' => 'instagram',
                'data' => $hook['data'],
                'performance_score' => $hook['score'],
                'usage_count' => 0,
                'is_active' => true,
                'last_trained_at' => now(),
                'metadata' => ['tone' => $hook['tone']],
            ]);
        }
    }
    
    private function seedCTAs(): void
    {
        $ctas = [
            ['industry' => 'fashion', 'goal' => 'closing', 'data' => 'Order sekarang via DM ya Kak! 💬', 'score' => 90],
            ['industry' => 'fashion', 'goal' => 'engagement', 'data' => 'Komen "READY" buat info lengkap!', 'score' => 88],
            ['industry' => 'food', 'goal' => 'closing', 'data' => 'Pesan sekarang sebelum kehabisan! 📱', 'score' => 92],
            ['industry' => 'beauty', 'goal' => 'branding', 'data' => 'Follow untuk tips kecantikan lainnya! ✨', 'score' => 85],
        ];
        
        foreach ($ctas as $cta) {
            MLOptimizedData::create([
                'type' => 'cta',
                'industry' => $cta['industry'],
                'platform' => 'instagram',
                'data' => $cta['data'],
                'performance_score' => $cta['score'],
                'usage_count' => 0,
                'is_active' => true,
                'last_trained_at' => now(),
                'metadata' => ['goal' => $cta['goal']],
            ]);
        }
    }
}
