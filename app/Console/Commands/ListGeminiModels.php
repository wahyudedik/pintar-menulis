<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ListGeminiModels extends Command
{
    protected $signature = 'gemini:list-models';
    protected $description = 'List available Gemini models';

    public function handle()
    {
        $apiKey = config('services.gemini.api_key');
        
        $this->info('Fetching available Gemini models...');
        
        try {
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey
            ])->get('https://generativelanguage.googleapis.com/v1beta/models');
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['models'])) {
                    $this->info('Available models:');
                    foreach ($data['models'] as $model) {
                        $name = $model['name'] ?? 'Unknown';
                        $displayName = $model['displayName'] ?? '';
                        $this->line("- $name ($displayName)");
                    }
                } else {
                    $this->error('No models found in response');
                    $this->line(json_encode($data, JSON_PRETTY_PRINT));
                }
            } else {
                $this->error('Failed to fetch models');
                $this->line('Status: ' . $response->status());
                $this->line('Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
        
        return 0;
    }
}
