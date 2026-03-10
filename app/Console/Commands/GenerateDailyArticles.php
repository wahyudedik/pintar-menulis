<?php

namespace App\Console\Commands;

use App\Services\ArticleGeneratorService;
use Illuminate\Console\Command;

class GenerateDailyArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate 7 daily articles (2 captions, 2 quotes, 2 tips, 1 mixed)';

    /**
     * Execute the console command.
     */
    public function handle(ArticleGeneratorService $generatorService)
    {
        $this->info('Starting daily article generation...');

        try {
            $results = $generatorService->generateDailyArticles();

            $this->info("✓ Successfully generated {$results['success']} articles");
            
            if ($results['failed'] > 0) {
                $this->warn("✗ Failed to generate {$results['failed']} articles");
            }

            if (!empty($results['errors'])) {
                $this->error('Errors encountered:');
                foreach ($results['errors'] as $error) {
                    $this->error("  - {$error}");
                }
            }

            // Log results
            \Log::info('Daily articles generated', [
                'success' => $results['success'],
                'failed' => $results['failed'],
                'articles' => $results['articles'],
            ]);

            $this->info('Daily article generation completed!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error generating articles: {$e->getMessage()}");
            \Log::error('Daily article generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
