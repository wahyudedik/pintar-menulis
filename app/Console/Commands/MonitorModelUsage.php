<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GeminiService;

class MonitorModelUsage extends Command
{
    protected $signature = 'gemini:monitor-usage';
    protected $description = 'Monitor Gemini model usage and rate limits';

    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        parent::__construct();
        $this->geminiService = $geminiService;
    }

    public function handle()
    {
        $this->info('🤖 Gemini Model Usage Monitor');
        $this->line('');
        
        $stats = $this->geminiService->getModelUsageStats();
        $tierInfo = $stats['_meta'] ?? [];
        unset($stats['_meta']);
        
        // Show tier information
        if (!empty($tierInfo)) {
            $this->info("Current Tier: {$tierInfo['tier_name']}");
            $this->info("Billing Status: {$tierInfo['billing_status']}");
            $this->line('');
        }
        
        $currentModel = $this->geminiService->getCurrentModel();
        $this->info("Current Active Model: {$currentModel}");
        $this->line('');
        
        $headers = ['Model', 'RPM', 'RPD', 'TPM', 'Status', 'Priority', 'Quality', 'Tier'];
        $rows = [];
        
        foreach ($stats as $modelName => $stat) {
            $rpmStatus = $this->getUsageBar($stat['rpm']['percentage']);
            $rpdStatus = $this->getUsageBar($stat['rpd']['percentage']);
            $tpmStatus = $this->getUsageBar($stat['tpm']['percentage']);
            
            $status = $stat['available'] ? '✅ Available' : '❌ Limited';
            
            $tierBadge = $stat['tier'] === 'tier1' ? '💳' : '🆓';
            
            $rows[] = [
                $stat['display_name'] . ($modelName === $currentModel ? ' ⭐' : ''),
                "{$stat['rpm']['current']}/{$stat['rpm']['limit']} {$rpmStatus}",
                "{$stat['rpd']['current']}/{$stat['rpd']['limit']} {$rpdStatus}",
                number_format($stat['tpm']['current']) . '/' . number_format($stat['tpm']['limit']) . " {$tpmStatus}",
                $status,
                "P{$stat['priority']}",
                ucfirst($stat['quality']),
                $tierBadge . ' ' . ucfirst($stat['tier']),
            ];
        }
        
        $this->table($headers, $rows);
        
        $this->line('');
        $this->info('Legend:');
        $this->line('⭐ = Currently Active');
        $this->line('✅ = Available');
        $this->line('❌ = Rate Limited');
        $this->line('🆓 = Free Tier');
        $this->line('💳 = Paid Tier (Billing Active)');
        $this->line('RPM = Requests Per Minute');
        $this->line('RPD = Requests Per Day');
        $this->line('TPM = Tokens Per Minute');
        
        return 0;
    }
    
    protected function getUsageBar(float $percentage): string
    {
        if ($percentage >= 90) {
            return '🔴'; // Critical
        } elseif ($percentage >= 70) {
            return '🟡'; // Warning
        } elseif ($percentage >= 50) {
            return '🟢'; // Good
        } else {
            return '⚪'; // Low usage
        }
    }
}
