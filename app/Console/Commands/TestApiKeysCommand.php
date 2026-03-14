<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestApiKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'api:test-keys {--platform=all : Platform to test (all|youtube|x|instagram|facebook|linkedin|tiktok)}';

    /**
     * The console command description.
     */
    protected $description = 'Test all configured API keys for social media platforms';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $platform = $this->option('platform');

        $this->info('🔍 Testing Social Media API Keys');
        $this->line('');

        $results = [];

        if ($platform === 'all' || $platform === 'youtube') {
            $results['youtube'] = $this->testYouTubeAPI();
        }

        if ($platform === 'all' || $platform === 'x') {
            $results['x'] = $this->testXAPI();
        }

        if ($platform === 'all' || $platform === 'instagram') {
            $results['instagram'] = $this->testInstagramAPI();
        }

        if ($platform === 'all' || $platform === 'facebook') {
            $results['facebook'] = $this->testFacebookAPI();
        }

        if ($platform === 'all' || $platform === 'linkedin') {
            $results['linkedin'] = $this->testLinkedInAPI();
        }

        if ($platform === 'all' || $platform === 'tiktok') {
            $results['tiktok'] = $this->testTikTokAPI();
        }

        $this->displayResults($results);

        return 0;
    }

    /**
     * Test YouTube Data API v3
     */
    private function testYouTubeAPI(): array
    {
        $this->info('📺 Testing YouTube Data API v3...');

        $apiKey = config('services.youtube.api_key');
        
        if (!$apiKey) {
            $this->error('❌ YOUTUBE_API_KEY not configured');
            return ['status' => 'error', 'message' => 'API key not configured'];
        }

        try {
            // Test with a simple channel request
            $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'snippet,statistics',
                'id' => 'UCuAXFkgsw1L7xaCfnd5JJOw', // YouTube's own channel
                'key' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['items']) && count($data['items']) > 0) {
                    $this->info('✅ YouTube API: Working correctly');
                    return [
                        'status' => 'success',
                        'message' => 'API working correctly',
                        'quota_used' => $data['pageInfo']['totalResults'] ?? 1
                    ];
                }
            }

            $error = $response->json()['error'] ?? [];
            $this->error('❌ YouTube API: ' . ($error['message'] ?? 'Unknown error'));
            
            return [
                'status' => 'error',
                'message' => $error['message'] ?? 'API request failed',
                'code' => $error['code'] ?? $response->status()
            ];

        } catch (\Exception $e) {
            $this->error('❌ YouTube API: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Test X (Twitter) API v2
     */
    private function testXAPI(): array
    {
        $this->info('❌ Testing X (Twitter) API v2...');

        $bearerToken = config('services.twitter.bearer_token');
        
        if (!$bearerToken) {
            $this->error('❌ X_BEARER_TOKEN not configured');
            return ['status' => 'error', 'message' => 'Bearer token not configured'];
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$bearerToken}"
            ])->get('https://api.twitter.com/2/users/by/username/twitter', [
                'user.fields' => 'public_metrics,verified'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data'])) {
                    $this->info('✅ X API: Working correctly');
                    return [
                        'status' => 'success',
                        'message' => 'API working correctly',
                        'user' => $data['data']['name'] ?? 'Twitter'
                    ];
                }
            }

            $error = $response->json()['errors'][0] ?? [];
            $errorMessage = $error['message'] ?? 'API request failed';
            
            // Handle specific X API errors
            if ($response->status() === 402) {
                $errorMessage = 'Credits depleted - upgrade X API plan or wait for monthly reset';
            } elseif ($response->status() === 401) {
                $errorMessage = 'Invalid Bearer Token - check X API credentials';
            } elseif ($response->status() === 429) {
                $errorMessage = 'Rate limit exceeded - wait or upgrade plan';
            }
            
            $this->error('❌ X API: ' . $errorMessage);
            
            return [
                'status' => 'error',
                'message' => $errorMessage,
                'code' => $response->status()
            ];

        } catch (\Exception $e) {
            $this->error('❌ X API: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Test Instagram Basic Display API
     */
    private function testInstagramAPI(): array
    {
        $this->info('📷 Testing Instagram Basic Display API...');

        $accessToken = config('services.instagram.access_token');
        
        if (!$accessToken) {
            $this->error('❌ INSTAGRAM_ACCESS_TOKEN not configured');
            return ['status' => 'error', 'message' => 'Access token not configured'];
        }

        try {
            $response = Http::timeout(10)->get('https://graph.instagram.com/me', [
                'fields' => 'id,username,account_type',
                'access_token' => $accessToken
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['id'])) {
                    $this->info('✅ Instagram API: Working correctly');
                    return [
                        'status' => 'success',
                        'message' => 'API working correctly',
                        'username' => $data['username'] ?? 'Unknown'
                    ];
                }
            }

            $error = $response->json()['error'] ?? [];
            $this->error('❌ Instagram API: ' . ($error['message'] ?? 'Unknown error'));
            
            return [
                'status' => 'error',
                'message' => $error['message'] ?? 'API request failed',
                'code' => $error['code'] ?? $response->status()
            ];

        } catch (\Exception $e) {
            $this->error('❌ Instagram API: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Test Facebook Graph API
     */
    private function testFacebookAPI(): array
    {
        $this->info('👥 Testing Facebook Graph API...');

        $accessToken = config('services.facebook.access_token');
        
        if (!$accessToken) {
            $this->error('❌ FACEBOOK_ACCESS_TOKEN not configured');
            return ['status' => 'error', 'message' => 'Access token not configured'];
        }

        try {
            // Test with Facebook's own page
            $response = Http::timeout(10)->get('https://graph.facebook.com/facebook', [
                'fields' => 'id,name,fan_count',
                'access_token' => $accessToken
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['id'])) {
                    $this->info('✅ Facebook API: Working correctly');
                    return [
                        'status' => 'success',
                        'message' => 'API working correctly',
                        'page' => $data['name'] ?? 'Facebook'
                    ];
                }
            }

            $error = $response->json()['error'] ?? [];
            $this->error('❌ Facebook API: ' . ($error['message'] ?? 'Unknown error'));
            
            return [
                'status' => 'error',
                'message' => $error['message'] ?? 'API request failed',
                'code' => $error['code'] ?? $response->status()
            ];

        } catch (\Exception $e) {
            $this->error('❌ Facebook API: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Test LinkedIn API
     */
    private function testLinkedInAPI(): array
    {
        $this->info('💼 Testing LinkedIn API...');

        $accessToken = config('services.linkedin.access_token');
        
        if (!$accessToken) {
            $this->error('❌ LINKEDIN_ACCESS_TOKEN not configured');
            return ['status' => 'error', 'message' => 'Access token not configured'];
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$accessToken}"
            ])->get('https://api.linkedin.com/v2/me');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['id'])) {
                    $this->info('✅ LinkedIn API: Working correctly');
                    return [
                        'status' => 'success',
                        'message' => 'API working correctly',
                        'user_id' => $data['id']
                    ];
                }
            }

            $this->error('❌ LinkedIn API: Request failed');
            return [
                'status' => 'error',
                'message' => 'API request failed',
                'code' => $response->status()
            ];

        } catch (\Exception $e) {
            $this->error('❌ LinkedIn API: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Test TikTok API
     */
    private function testTikTokAPI(): array
    {
        $this->info('🎵 Testing TikTok for Developers API...');

        $accessToken = config('services.tiktok.access_token');
        
        if (!$accessToken) {
            $this->error('❌ TIKTOK_ACCESS_TOKEN not configured');
            return ['status' => 'error', 'message' => 'Access token not configured'];
        }

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$accessToken}"
            ])->get('https://open-api.tiktok.com/user/info/', [
                'fields' => 'open_id,display_name'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data'])) {
                    $this->info('✅ TikTok API: Working correctly');
                    return [
                        'status' => 'success',
                        'message' => 'API working correctly'
                    ];
                }
            }

            $this->error('❌ TikTok API: Request failed');
            return [
                'status' => 'error',
                'message' => 'API request failed',
                'code' => $response->status()
            ];

        } catch (\Exception $e) {
            $this->error('❌ TikTok API: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Display test results summary
     */
    private function displayResults(array $results): void
    {
        $this->line('');
        $this->info('📊 API Test Results Summary:');
        $this->line('');

        $tableData = [];
        $successCount = 0;
        $totalCount = count($results);

        foreach ($results as $platform => $result) {
            $status = $result['status'] === 'success' ? '✅ Working' : '❌ Failed';
            $message = $result['message'];
            
            if ($result['status'] === 'success') {
                $successCount++;
            }

            $tableData[] = [
                ucfirst($platform),
                $status,
                $message
            ];
        }

        $this->table(['Platform', 'Status', 'Message'], $tableData);

        $this->line('');
        $this->info("Success Rate: {$successCount}/{$totalCount} APIs working");

        if ($successCount === $totalCount) {
            $this->info('🎉 All APIs are working correctly!');
        } else {
            $this->warn('⚠️  Some APIs need attention. Check the errors above.');
        }
    }
}