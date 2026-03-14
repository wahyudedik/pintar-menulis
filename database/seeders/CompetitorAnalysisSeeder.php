<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Competitor;
use App\Models\CompetitorAnalysisSummary;
use App\Models\CompetitorPost;
use App\Models\CompetitorPattern;
use App\Models\CompetitorTopContent;
use App\Models\CompetitorAlert;
use App\Models\CompetitorContentGap;

class CompetitorAnalysisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first client user
        $user = User::where('role', 'client')->first();
        
        if (!$user) {
            $this->command->info('No client user found. Skipping competitor analysis seeder.');
            return;
        }

        // Create sample competitors
        $competitors = [
            [
                'username' => 'fashionista_id',
                'platform' => 'instagram',
                'category' => 'Fashion',
                'profile_name' => 'Fashionista Indonesia',
                'followers_count' => 125000,
                'posts_count' => 1250,
            ],
            [
                'username' => 'kuliner_nusantara',
                'platform' => 'instagram', 
                'category' => 'Food',
                'profile_name' => 'Kuliner Nusantara',
                'followers_count' => 89000,
                'posts_count' => 890,
            ],
            [
                'username' => 'tech_reviewer_id',
                'platform' => 'tiktok',
                'category' => 'Technology',
                'profile_name' => 'Tech Reviewer Indonesia',
                'followers_count' => 67000,
                'posts_count' => 340,
            ]
        ];

        foreach ($competitors as $competitorData) {
            $competitor = Competitor::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'username' => $competitorData['username'],
                    'platform' => $competitorData['platform'],
                ],
                [
                    'profile_name' => $competitorData['profile_name'],
                    'category' => $competitorData['category'],
                    'followers_count' => $competitorData['followers_count'],
                    'posts_count' => $competitorData['posts_count'],
                    'is_active' => true,
                    'last_analyzed_at' => now(),
                ]
            );

            // Create analysis summary
            CompetitorAnalysisSummary::updateOrCreate(
                [
                    'competitor_id' => $competitor->id,
                    'analysis_date' => now()->toDateString(),
                ],
                [
                    'total_posts' => rand(20, 50),
                    'avg_engagement_rate' => rand(250, 850) / 100, // 2.5% - 8.5%
                    'avg_likes' => rand(500, 5000),
                    'avg_comments' => rand(20, 200),
                    'avg_shares' => rand(5, 50),
                    'top_hashtags' => [
                        '#fashion', '#ootd', '#style', '#indonesia', '#trending'
                    ],
                    'posting_times' => [
                        ['time' => '19:00', 'avg_engagement' => 4.2],
                        ['time' => '12:00', 'avg_engagement' => 3.8],
                        ['time' => '21:00', 'avg_engagement' => 5.1],
                    ],
                    'dominant_tone' => 'casual',
                    'content_types' => [
                        ['type' => 'image', 'count' => 15],
                        ['type' => 'video', 'count' => 8],
                        ['type' => 'carousel', 'count' => 5],
                    ],
                    'ai_insights' => 'Kompetitor ini aktif posting di jam prime time dan menggunakan tone casual yang engaging.',
                ]
            );

            // Create sample posts
            for ($i = 0; $i < 5; $i++) {
                $post = CompetitorPost::create([
                    'competitor_id' => $competitor->id,
                    'post_id' => 'post_' . $competitor->id . '_' . $i,
                    'caption' => 'Sample post caption #' . ($i + 1) . ' from ' . $competitor->username,
                    'post_type' => ['image', 'video', 'carousel'][rand(0, 2)],
                    'likes_count' => rand(100, 2000),
                    'comments_count' => rand(10, 100),
                    'shares_count' => rand(5, 50),
                    'engagement_rate' => rand(200, 800) / 100,
                    'hashtags' => json_encode(['#fashion', '#style', '#trending']),
                    'posted_at' => now()->subDays(rand(1, 30)),
                ]);

                // Create top content entry for some posts
                if ($i < 3) {
                    \App\Models\CompetitorTopContent::create([
                        'competitor_id' => $competitor->id,
                        'competitor_post_id' => $post->id,
                        'metric_type' => 'engagement',
                        'metric_value' => $post->engagement_rate,
                        'rank' => $i + 1,
                        'success_factors' => 'High engagement due to trending hashtags and optimal posting time.',
                        'analysis_date' => now()->toDateString(),
                    ]);
                }
            }

            // Create sample patterns
            CompetitorPattern::create([
                'competitor_id' => $competitor->id,
                'pattern_type' => 'posting_time',
                'pattern_data' => [
                    'peak_hours' => ['19:00', '21:00'],
                    'frequency' => 'daily',
                    'consistency' => 85
                ],
                'insights' => 'Kompetitor konsisten posting di jam 19:00-21:00 dengan engagement rate tertinggi.',
                'analysis_date' => now()->toDateString(),
            ]);

            // Create sample content gaps
            CompetitorContentGap::create([
                'competitor_id' => $competitor->id,
                'gap_type' => 'topic',
                'gap_title' => 'Tutorial Styling Tips',
                'gap_description' => 'Kompetitor belum banyak membuat konten tutorial styling yang detail.',
                'opportunity' => 'Peluang untuk membuat konten edukasi yang engaging.',
                'suggested_content' => [
                    'Tutorial mix and match outfit untuk pemula',
                    'Tips memilih warna yang cocok dengan skin tone',
                    'Cara styling outfit untuk berbagai acara'
                ],
                'priority' => 8,
                'identified_date' => now()->toDateString(),
            ]);

            // Create sample alerts
            CompetitorAlert::create([
                'user_id' => $user->id,
                'competitor_id' => $competitor->id,
                'alert_type' => 'new_post',
                'alert_title' => 'New Post Alert',
                'alert_message' => $competitor->username . ' just posted new content with high engagement potential.',
                'alert_data' => [
                    'post_type' => 'video',
                    'predicted_engagement' => 'high'
                ],
                'is_read' => false,
                'triggered_at' => now()->subHours(rand(1, 24)),
            ]);
        }

        $this->command->info('Competitor analysis sample data created successfully!');
    }
}