<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixApiKeysCommand extends Command
{
    protected $signature = 'api:fix-guide {platform?}';
    protected $description = 'Show step-by-step guide to fix API key issues';

    public function handle(): int
    {
        $platform = $this->argument('platform');

        if (!$platform) {
            $this->showMainMenu();
            return 0;
        }

        switch ($platform) {
            case 'youtube':
                $this->fixYouTubeAPI();
                break;
            case 'x':
                $this->fixXAPI();
                break;
            case 'instagram':
                $this->fixInstagramAPI();
                break;
            default:
                $this->error("Unknown platform: {$platform}");
                return 1;
        }

        return 0;
    }

    private function showMainMenu(): void
    {
        $this->info('🔧 API Key Fix Guide');
        $this->line('');
        $this->info('Available commands:');
        $this->line('php artisan api:fix-guide youtube');
        $this->line('php artisan api:fix-guide x');
        $this->line('php artisan api:fix-guide instagram');
        $this->line('');
        $this->info('Or test your APIs first:');
        $this->line('php artisan api:test-keys');
    }

    private function fixYouTubeAPI(): void
    {
        $this->info('🔧 YouTube API Fix Guide');
        $this->line('');
        
        $this->warn('Common Error: "Requests from referer <empty> are blocked"');
        $this->line('');
        
        $this->info('Step-by-step fix:');
        $this->line('1. Go to: https://console.cloud.google.com');
        $this->line('2. Select your project');
        $this->line('3. Navigate: APIs & Services → Credentials');
        $this->line('4. Click your API key to edit');
        $this->line('5. Under "Application restrictions" → Select "None"');
        $this->line('6. Under "API restrictions" → Select "Restrict key"');
        $this->line('7. Choose "YouTube Data API v3" only');
        $this->line('8. Click Save');
        $this->line('9. Wait 5-10 minutes');
        $this->line('');
        
        $this->info('Test after fix:');
        $this->line('php artisan api:test-keys --platform=youtube');
    }
}