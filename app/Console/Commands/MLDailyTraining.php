<?php

namespace App\Console\Commands;

use App\Services\MLTrainingService;
use Illuminate\Console\Command;

class MLDailyTraining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml:train-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run daily ML training from analytics data to improve AI performance';

    /**
     * Execute the console command.
     */
    public function handle(MLTrainingService $trainingService)
    {
        $this->info('🤖 Starting ML Daily Training...');
        $this->newLine();
        
        $startTime = now();
        
        // Run training
        $results = $trainingService->runDailyTraining();
        
        // Display results
        $this->info('✅ Training completed!');
        $this->newLine();
        
        $this->table(
            ['Type', 'Count'],
            [
                ['Hashtags', $results['hashtags']],
                ['Keywords', $results['keywords']],
                ['Topics', $results['topics']],
                ['Hooks', $results['hooks']],
                ['CTAs', $results['ctas']],
            ]
        );
        
        $this->newLine();
        $this->info('⏱️  Duration: ' . now()->diffInSeconds($startTime) . ' seconds');
        
        // Show errors if any
        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('⚠️  Errors occurred:');
            foreach ($results['errors'] as $error) {
                $this->error('  - ' . $error);
            }
        }
        
        return Command::SUCCESS;
    }
}

