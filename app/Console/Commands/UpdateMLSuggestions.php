<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MLSuggestionsService;
use Illuminate\Support\Facades\Log;

class UpdateMLSuggestions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ml:update-suggestions {--force : Force update even if cache exists}';

    /**
     * The console command description.
     */
    protected $description = 'Update ML suggestions with fresh trending data';

    protected $mlSuggestionsService;

    public function __construct(MLSuggestionsService $mlSuggestionsService)
    {
        parent::__construct();
        $this->mlSuggestionsService = $mlSuggestionsService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Starting ML Suggestions update...');
        
        $industries = ['fashion', 'food', 'beauty', 'printing', 'photography', 'catering', 'tiktok_shop', 'shopee_affiliate', 'home_decor', 'handmade', 'digital_service', 'automotive'];
        $platforms = ['instagram', 'tiktok', 'facebook', 'twitter', 'linkedin'];
        
        $force = $this->option('force');
        
        if ($force) {
            $this->info('🔄 Force mode: Clearing existing cache...');
            $this->mlSuggestionsService->clearCache();
        }

        $totalCombinations = count($industries) * count($platforms);
        $processed = 0;
        $errors = 0;

        $this->info("📊 Processing {$totalCombinations} industry-platform combinations...");
        
        $progressBar = $this->output->createProgressBar($totalCombinations);
        $progressBar->start();

        foreach ($industries as $industry) {
            foreach ($platforms as $platform) {
                try {
                    // Generate trending suggestions for each combination
                    $result = $this->mlSuggestionsService->getTrendingSuggestions($industry, $platform);
                    
                    if ($result['success']) {
                        $processed++;
                        Log::info("ML Suggestions updated for {$industry} on {$platform}");
                    } else {
                        $errors++;
                        Log::warning("Failed to update ML Suggestions for {$industry} on {$platform}");
                    }
                    
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Error updating ML Suggestions for {$industry} on {$platform}: {$e->getMessage()}");
                }
                
                $progressBar->advance();
                
                // Small delay to avoid overwhelming the API
                usleep(100000); // 0.1 second
            }
        }

        $progressBar->finish();
        $this->newLine();

        // Update weekly trend analysis for each industry
        $this->info('📈 Updating weekly trend analysis...');
        $weeklyProgressBar = $this->output->createProgressBar(count($industries));
        $weeklyProgressBar->start();

        foreach ($industries as $industry) {
            try {
                $result = $this->mlSuggestionsService->getWeeklyTrendAnalysis($industry);
                
                if ($result['success']) {
                    Log::info("Weekly trend analysis updated for {$industry}");
                } else {
                    Log::warning("Failed to update weekly trend analysis for {$industry}");
                }
                
            } catch (\Exception $e) {
                Log::error("Error updating weekly trend analysis for {$industry}: {$e->getMessage()}");
            }
            
            $weeklyProgressBar->advance();
            usleep(200000); // 0.2 second delay
        }

        $weeklyProgressBar->finish();
        $this->newLine();

        // Summary
        $this->info('✅ ML Suggestions update completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Combinations', $totalCombinations],
                ['Successfully Processed', $processed],
                ['Errors', $errors],
                ['Success Rate', round(($processed / $totalCombinations) * 100, 2) . '%'],
                ['Updated At', now()->format('Y-m-d H:i:s')],
            ]
        );

        if ($errors > 0) {
            $this->warn("⚠️  {$errors} errors occurred. Check logs for details.");
            return Command::FAILURE;
        }

        $this->info('🎉 All ML suggestions updated successfully!');
        return Command::SUCCESS;
    }
}