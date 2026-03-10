<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleAdsService;

class TestKeywordResearch extends Command
{
    protected $signature = 'test:keyword-research {keyword?}';
    protected $description = 'Test keyword research functionality';

    public function handle()
    {
        $googleAds = app(GoogleAdsService::class);
        
        $testKeywords = $this->argument('keyword') 
            ? [$this->argument('keyword')]
            : [
                'sepatu sneakers',
                'nasi goreng',
                'jual baju anak',
                'kopi arabica',
                'tas wanita murah',
            ];

        $this->info('🔍 Testing Keyword Research...');
        $this->newLine();

        foreach ($testKeywords as $keyword) {
            $this->info("Testing: {$keyword}");
            $this->line(str_repeat('-', 60));

            try {
                $result = $googleAds->getKeywordIdeas($keyword);

                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Keyword', $result['keyword']],
                        ['Search Volume', number_format($result['search_volume']) . '/bulan'],
                        ['Competition', $result['competition']],
                        ['CPC Low', 'Rp ' . number_format($result['cpc_low'])],
                        ['CPC High', 'Rp ' . number_format($result['cpc_high'])],
                        ['Trend', $result['trend_direction']],
                        ['Data Source', $result['data_source'] ?? 'estimated'],
                    ]
                );

                if (!empty($result['related_keywords'])) {
                    $this->info('Related Keywords:');
                    foreach (array_slice($result['related_keywords'], 0, 5) as $related) {
                        $this->line("  • {$related}");
                    }
                }

                $this->newLine();

                // Test extract keywords from caption
                $caption = "Jual {$keyword} original murah! Kualitas premium, harga terjangkau. Order sekarang!";
                $extracted = $googleAds->extractKeywordsFromCaption($caption);
                
                $this->info('Extracted Keywords from Caption:');
                foreach (array_slice($extracted, 0, 5) as $kw) {
                    $this->line("  • {$kw}");
                }

                $this->newLine(2);

            } catch (\Exception $e) {
                $this->error("Error: " . $e->getMessage());
                $this->newLine();
            }
        }

        $this->info('✅ Test completed!');
    }
}
