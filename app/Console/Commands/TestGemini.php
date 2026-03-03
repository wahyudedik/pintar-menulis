<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiService;

class TestGemini extends Command
{
    protected $signature = 'test:gemini';
    protected $description = 'Test Gemini API connection';

    public function handle()
    {
        $this->info('Testing Gemini API...');
        
        try {
            $service = app(GeminiService::class);
            
            $result = $service->generateCopywriting([
                'type' => 'instagram_caption',
                'brief' => 'Kopi arabica premium dari Aceh dengan cita rasa yang khas',
                'tone' => 'casual',
                'platform' => 'instagram',
                'keywords' => 'kopi, arabica'
            ]);
            
            $this->info('SUCCESS!');
            $this->line('Result: ' . substr($result, 0, 200));
            
            return 0;
        } catch (\Exception $e) {
            $this->error('FAILED!');
            $this->error('Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            
            return 1;
        }
    }
}
