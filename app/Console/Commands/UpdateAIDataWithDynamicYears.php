<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DynamicDateService;
use App\Models\CaptionHistory;
use App\Models\CaptionAnalytics;
use Illuminate\Support\Facades\DB;

class UpdateAIDataWithDynamicYears extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:update-dynamic-years 
                            {--dry-run : Show what would be updated without making changes}
                            {--force : Force update even if data seems current}';

    /**
     * The console command description.
     */
    protected $description = 'Update all AI-generated data to use dynamic years instead of hardcoded ones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');
        $currentYear = DynamicDateService::getCurrentYear();
        
        $this->info("🤖 AI Data Dynamic Year Update Tool");
        $this->info("Current Year: {$currentYear}");
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN MODE - No changes will be made");
        }
        
        $this->line("");
        
        // Update Caption History
        $this->updateCaptionHistory($isDryRun, $isForce, $currentYear);
        
        // Update Caption Analytics
        $this->updateCaptionAnalytics($isDryRun, $isForce, $currentYear);
        
        // Update cached AI responses
        $this->updateCachedResponses($isDryRun, $currentYear);
        
        $this->line("");
        $this->info("✅ AI Data Dynamic Year Update Complete!");
        
        if ($isDryRun) {
            $this->info("💡 Run without --dry-run to apply changes");
        }
    }

    /**
     * Update caption history with dynamic years
     */
    private function updateCaptionHistory($isDryRun, $isForce, $currentYear)
    {
        $this->info("📝 Updating Caption History...");
        
        // Find captions with hardcoded years
        $outdatedCaptions = CaptionHistory::where(function($query) use ($currentYear) {
            $query->where('caption_text', 'LIKE', '%2024%')
                  ->orWhere('caption_text', 'LIKE', '%2023%')
                  ->orWhere('caption_text', 'LIKE', '%2022%')
                  ->orWhere('caption_text', 'LIKE', '%2025%');
        })->get();
        
        $this->line("Found {$outdatedCaptions->count()} captions with hardcoded years");
        
        if ($outdatedCaptions->count() > 0 && !$isDryRun) {
            $bar = $this->output->createProgressBar($outdatedCaptions->count());
            $bar->start();
            
            foreach ($outdatedCaptions as $caption) {
                $updatedText = DynamicDateService::replaceDatePlaceholders($caption->caption_text);
                
                if ($updatedText !== $caption->caption_text || $isForce) {
                    $caption->update([
                        'caption_text' => $updatedText,
                        'updated_at' => now()
                    ]);
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->line("");
        }
        
        $this->info("✅ Caption History updated");
    }

    /**
     * Update caption analytics with dynamic years
     */
    private function updateCaptionAnalytics($isDryRun, $isForce, $currentYear)
    {
        $this->info("📊 Updating Caption Analytics...");
        
        // Find analytics with hardcoded years
        $outdatedAnalytics = CaptionAnalytics::where(function($query) use ($currentYear) {
            $query->where('caption_text', 'LIKE', '%2024%')
                  ->orWhere('caption_text', 'LIKE', '%2023%')
                  ->orWhere('caption_text', 'LIKE', '%2022%')
                  ->orWhere('caption_text', 'LIKE', '%2025%')
                  ->orWhere('user_notes', 'LIKE', '%2024%')
                  ->orWhere('user_notes', 'LIKE', '%2023%');
        })->get();
        
        $this->line("Found {$outdatedAnalytics->count()} analytics with hardcoded years");
        
        if ($outdatedAnalytics->count() > 0 && !$isDryRun) {
            $bar = $this->output->createProgressBar($outdatedAnalytics->count());
            $bar->start();
            
            foreach ($outdatedAnalytics as $analytics) {
                $updatedCaption = DynamicDateService::replaceDatePlaceholders($analytics->caption_text);
                $updatedNotes = $analytics->user_notes ? 
                    DynamicDateService::replaceDatePlaceholders($analytics->user_notes) : 
                    $analytics->user_notes;
                
                if ($updatedCaption !== $analytics->caption_text || 
                    $updatedNotes !== $analytics->user_notes || 
                    $isForce) {
                    
                    $analytics->update([
                        'caption_text' => $updatedCaption,
                        'user_notes' => $updatedNotes,
                        'updated_at' => now()
                    ]);
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->line("");
        }
        
        $this->info("✅ Caption Analytics updated");
    }

    /**
     * Update cached AI responses
     */
    private function updateCachedResponses($isDryRun, $currentYear)
    {
        $this->info("🗄️ Clearing cached AI responses...");
        
        if (!$isDryRun) {
            // Clear AI-related caches
            $cacheKeys = [
                'gemini_*',
                'ai_generator_*',
                'competitor_analysis_*',
                'trend_content_*'
            ];
            
            foreach ($cacheKeys as $pattern) {
                \Cache::forget($pattern);
            }
            
            // Clear all cache tags related to AI
            if (method_exists(\Cache::getStore(), 'tags')) {
                \Cache::tags(['ai', 'gemini', 'competitor', 'trends'])->flush();
            }
        }
        
        $this->info("✅ Cached responses cleared");
    }

    /**
     * Show statistics about data that needs updating
     */
    private function showUpdateStatistics()
    {
        $currentYear = DynamicDateService::getCurrentYear();
        
        $this->table(
            ['Data Type', 'Records with Hardcoded Years', 'Status'],
            [
                [
                    'Caption History',
                    CaptionHistory::where('caption_text', 'LIKE', '%2024%')->count(),
                    '🔄 Needs Update'
                ],
                [
                    'Caption Analytics',
                    CaptionAnalytics::where('caption_text', 'LIKE', '%2024%')->count(),
                    '🔄 Needs Update'
                ],
                [
                    'Current Year',
                    $currentYear,
                    '✅ Dynamic'
                ]
            ]
        );
    }
}