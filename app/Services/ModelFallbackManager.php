<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ModelFallbackManager
{
    /**
     * Current detected tier (auto-detected based on actual API responses)
     * Possible values: 'free', 'tier1', 'tier2', 'tier3'
     */
    protected $currentTier = null;
    
    /**
     * Model configurations with rate limits per tier
     * Tier detection is automatic based on API responses
     * 
     * Tiers:
     * - Free: 5-15 RPM, 100-1000 RPD
     * - Tier 1 (Paid): 150-300 RPM, unlimited RPD (after billing setup)
     * - Tier 2: 1000+ RPM (after $250 cumulative spend)
     * - Tier 3: 4000+ RPM (enterprise)
     */
    protected $modelsByTier = [
        'free' => [
            // Free Tier Models
            [
                'name' => 'gemini-2.5-flash',
                'display_name' => 'Gemini 2.5 Flash',
                'rpm' => 10,
                'rpd' => 250,
                'tpm' => 250000,
                'priority' => 1,
                'quality' => 'high',
                'speed' => 'fast',
                'tier' => 'free',
            ],
            [
                'name' => 'gemini-2.5-flash-lite',
                'display_name' => 'Gemini 2.5 Flash-Lite',
                'rpm' => 15,
                'rpd' => 1000,
                'tpm' => 250000,
                'priority' => 2,
                'quality' => 'good',
                'speed' => 'very_fast',
                'tier' => 'free',
            ],
            [
                'name' => 'gemini-3-flash-preview',
                'display_name' => 'Gemini 3 Flash Preview',
                'rpm' => 10,
                'rpd' => 250,
                'tpm' => 250000,
                'priority' => 3,
                'quality' => 'very_high',
                'speed' => 'fast',
                'tier' => 'free',
            ],
            [
                'name' => 'gemini-2.5-pro',
                'display_name' => 'Gemini 2.5 Pro',
                'rpm' => 5,
                'rpd' => 100,
                'tpm' => 250000,
                'priority' => 4,
                'quality' => 'very_high',
                'speed' => 'medium',
                'tier' => 'free',
            ],
            [
                'name' => 'gemini-2.0-flash',
                'display_name' => 'Gemini 2.0 Flash',
                'rpm' => 10,
                'rpd' => 250,
                'tpm' => 250000,
                'priority' => 5,
                'quality' => 'good',
                'speed' => 'fast',
                'tier' => 'free',
            ],
        ],
        
        'tier1' => [
            // Tier 1 (Paid) - After billing setup, much higher limits!
            [
                'name' => 'gemini-2.5-flash',
                'display_name' => 'Gemini 2.5 Flash (Paid)',
                'rpm' => 300,      // 30x increase!
                'rpd' => 999999,   // Unlimited
                'tpm' => 4000000,  // 16x increase!
                'priority' => 1,
                'quality' => 'high',
                'speed' => 'fast',
                'tier' => 'tier1',
            ],
            [
                'name' => 'gemini-2.5-flash-lite',
                'display_name' => 'Gemini 2.5 Flash-Lite (Paid)',
                'rpm' => 300,
                'rpd' => 999999,
                'tpm' => 4000000,
                'priority' => 2,
                'quality' => 'good',
                'speed' => 'very_fast',
                'tier' => 'tier1',
            ],
            [
                'name' => 'gemini-3-flash-preview',
                'display_name' => 'Gemini 3 Flash Preview (Paid)',
                'rpm' => 300,
                'rpd' => 999999,
                'tpm' => 4000000,
                'priority' => 3,
                'quality' => 'very_high',
                'speed' => 'fast',
                'tier' => 'tier1',
            ],
            [
                'name' => 'gemini-2.5-pro',
                'display_name' => 'Gemini 2.5 Pro (Paid)',
                'rpm' => 150,      // 30x increase!
                'rpd' => 999999,
                'tpm' => 4000000,
                'priority' => 4,
                'quality' => 'very_high',
                'speed' => 'medium',
                'tier' => 'tier1',
            ],
            [
                'name' => 'gemini-2.0-flash',
                'display_name' => 'Gemini 2.0 Flash (Paid)',
                'rpm' => 300,
                'rpd' => 999999,
                'tpm' => 4000000,
                'priority' => 5,
                'quality' => 'good',
                'speed' => 'fast',
                'tier' => 'tier1',
            ],
        ],
    ];
    
    /**
     * Get current active models based on detected tier
     */
    protected function getActiveModels(): array
    {
        $tier = $this->detectTier();
        return $this->modelsByTier[$tier] ?? $this->modelsByTier['free'];
    }
    
    /**
     * Detect current tier based on usage patterns and API responses
     * This is automatic - no configuration needed!
     */
    protected function detectTier(): string
    {
        // Check if tier is already cached
        if ($this->currentTier !== null) {
            return $this->currentTier;
        }
        
        // Check cache for detected tier (expires after 1 hour)
        $cachedTier = Cache::get('gemini_api_tier');
        if ($cachedTier) {
            $this->currentTier = $cachedTier;
            Log::info('Using cached tier', ['tier' => $cachedTier]);
            return $cachedTier;
        }
        
        // Check if we have successful high-volume requests (indicates paid tier)
        $recentHighVolumeSuccess = Cache::get('gemini_high_volume_success', false);
        if ($recentHighVolumeSuccess) {
            $this->currentTier = 'tier1';
            Cache::put('gemini_api_tier', 'tier1', now()->addHour());
            Log::info('Detected Tier 1 (Paid) based on high volume success');
            return 'tier1';
        }
        
        // Default to free tier
        $this->currentTier = 'free';
        Cache::put('gemini_api_tier', 'free', now()->addHour());
        Log::info('Using Free Tier (default)');
        return 'free';
    }
    
    /**
     * Upgrade tier detection when high volume succeeds
     * Called automatically when request succeeds despite high usage
     */
    public function markHighVolumeSuccess(): void
    {
        Cache::put('gemini_high_volume_success', true, now()->addHour());
        Cache::put('gemini_api_tier', 'tier1', now()->addHour());
        $this->currentTier = 'tier1';
        
        Log::info('🎉 Tier upgraded to Tier 1 (Paid)! Higher limits now available.');
    }
    
    /**
     * Downgrade tier detection when hitting free tier limits
     * Called automatically when rate limit suggests free tier
     */
    public function markFreeTierLimit(): void
    {
        Cache::put('gemini_api_tier', 'free', now()->addHour());
        $this->currentTier = 'free';
        
        Log::info('Detected Free Tier limits');
    }
    
    /**
     * Get the best available model based on current usage
     */
    public function getBestAvailableModel(): array
    {
        $models = $this->getActiveModels();
        
        foreach ($models as $model) {
            if ($this->isModelAvailable($model)) {
                Log::info('Selected model', [
                    'model' => $model['name'],
                    'priority' => $model['priority']
                ]);
                return $model;
            }
        }
        
        // If all models exhausted in current tier, try upgrading tier
        $currentTier = $this->detectTier();
        if ($currentTier === 'free') {
            Log::warning('All free tier models exhausted, attempting paid tier');
            // Force check paid tier
            $this->currentTier = 'tier1';
            $paidModels = $this->getActiveModels();
            
            foreach ($paidModels as $model) {
                if ($this->isModelAvailable($model)) {
                    Log::info('🎉 Switched to Paid Tier!', [
                        'model' => $model['name'],
                        'tier' => 'tier1',
                        'rpm' => $model['rpm']
                    ]);
                    $this->markHighVolumeSuccess();
                    return $model;
                }
            }
        }
        
        // If all models exhausted, return primary with warning
        Log::warning('All models exhausted across all tiers, using primary model anyway');
        $models = $this->getActiveModels();
        return $models[0];
    }
    
    /**
     * Check if model is available (not rate limited)
     */
    protected function isModelAvailable(array $model): bool
    {
        $modelName = $model['name'];
        
        // Check RPM (Requests Per Minute)
        $rpmKey = "model_rpm:{$modelName}";
        $currentRpm = Cache::get($rpmKey, 0);
        if ($currentRpm >= $model['rpm']) {
            Log::info('Model RPM limit reached', [
                'model' => $modelName,
                'current' => $currentRpm,
                'limit' => $model['rpm']
            ]);
            return false;
        }
        
        // Check RPD (Requests Per Day)
        $rpdKey = "model_rpd:{$modelName}";
        $currentRpd = Cache::get($rpdKey, 0);
        if ($currentRpd >= $model['rpd']) {
            Log::info('Model RPD limit reached', [
                'model' => $modelName,
                'current' => $currentRpd,
                'limit' => $model['rpd']
            ]);
            return false;
        }
        
        // Check TPM (Tokens Per Minute) - estimated 2000 tokens per request
        $tpmKey = "model_tpm:{$modelName}";
        $currentTpm = Cache::get($tpmKey, 0);
        $estimatedTokens = 2000; // Average tokens per copywriting request
        if (($currentTpm + $estimatedTokens) > $model['tpm']) {
            Log::info('Model TPM limit approaching', [
                'model' => $modelName,
                'current' => $currentTpm,
                'limit' => $model['tpm']
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Track model usage after successful request
     */
    public function trackUsage(string $modelName, int $tokensUsed = 2000): void
    {
        // Increment RPM (expires after 1 minute)
        $rpmKey = "model_rpm:{$modelName}";
        $currentRpm = Cache::get($rpmKey, 0);
        Cache::put($rpmKey, $currentRpm + 1, now()->addMinute());
        
        // Increment RPD (expires after 1 day)
        $rpdKey = "model_rpd:{$modelName}";
        $currentRpd = Cache::get($rpdKey, 0);
        Cache::put($rpdKey, $currentRpd + 1, now()->addDay());
        
        // Increment TPM (expires after 1 minute)
        $tpmKey = "model_tpm:{$modelName}";
        $currentTpm = Cache::get($tpmKey, 0);
        Cache::put($tpmKey, $currentTpm + $tokensUsed, now()->addMinute());
        
        Log::info('Model usage tracked', [
            'model' => $modelName,
            'rpm' => $currentRpm + 1,
            'rpd' => $currentRpd + 1,
            'tpm' => $currentTpm + $tokensUsed
        ]);
    }
    
    /**
     * Get model by name (searches across all tiers)
     */
    public function getModelByName(string $name): ?array
    {
        $models = $this->getActiveModels();
        
        foreach ($models as $model) {
            if ($model['name'] === $name) {
                return $model;
            }
        }
        
        // Search in other tiers
        foreach ($this->modelsByTier as $tier => $tierModels) {
            foreach ($tierModels as $model) {
                if ($model['name'] === $name) {
                    return $model;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Get all models (current tier only)
     */
    public function getAllModels(): array
    {
        return $this->getActiveModels();
    }
    
    /**
     * Get current tier
     */
    public function getCurrentTier(): string
    {
        return $this->detectTier();
    }
    
    /**
     * Get current usage statistics for all models
     */
    public function getUsageStats(): array
    {
        $stats = [];
        $currentTier = $this->detectTier();
        $models = $this->getActiveModels();
        
        foreach ($models as $model) {
            $modelName = $model['name'];
            
            $stats[$modelName] = [
                'display_name' => $model['display_name'],
                'rpm' => [
                    'current' => Cache::get("model_rpm:{$modelName}", 0),
                    'limit' => $model['rpm'],
                    'percentage' => round((Cache::get("model_rpm:{$modelName}", 0) / $model['rpm']) * 100, 1)
                ],
                'rpd' => [
                    'current' => Cache::get("model_rpd:{$modelName}", 0),
                    'limit' => $model['rpd'],
                    'percentage' => $model['rpd'] < 999999 ? round((Cache::get("model_rpd:{$modelName}", 0) / $model['rpd']) * 100, 1) : 0
                ],
                'tpm' => [
                    'current' => Cache::get("model_tpm:{$modelName}", 0),
                    'limit' => $model['tpm'],
                    'percentage' => round((Cache::get("model_tpm:{$modelName}", 0) / $model['tpm']) * 100, 1)
                ],
                'available' => $this->isModelAvailable($model),
                'priority' => $model['priority'],
                'quality' => $model['quality'],
                'tier' => $model['tier'],
            ];
        }
        
        // Add tier info
        $stats['_meta'] = [
            'current_tier' => $currentTier,
            'tier_name' => $this->getTierDisplayName($currentTier),
            'billing_status' => $currentTier === 'tier1' ? 'Active (Paid)' : 'Free Tier',
        ];
        
        return $stats;
    }
    
    /**
     * Get tier display name
     */
    protected function getTierDisplayName(string $tier): string
    {
        $names = [
            'free' => 'Free Tier',
            'tier1' => 'Tier 1 (Paid)',
            'tier2' => 'Tier 2 (High Volume)',
            'tier3' => 'Tier 3 (Enterprise)',
        ];
        
        return $names[$tier] ?? 'Unknown';
    }
    
    /**
     * Reset usage stats (for testing)
     */
    public function resetUsageStats(): void
    {
        // Reset all tiers
        foreach ($this->modelsByTier as $tier => $models) {
            foreach ($models as $model) {
                $modelName = $model['name'];
                Cache::forget("model_rpm:{$modelName}");
                Cache::forget("model_rpd:{$modelName}");
                Cache::forget("model_tpm:{$modelName}");
            }
        }
        
        // Reset tier detection
        Cache::forget('gemini_api_tier');
        Cache::forget('gemini_high_volume_success');
        $this->currentTier = null;
        
        Log::info('Model usage stats reset (all tiers)');
    }
    
    /**
     * Handle rate limit error and get fallback model
     */
    public function handleRateLimitError(string $currentModel): ?array
    {
        Log::warning('Rate limit error detected', ['current_model' => $currentModel]);
        
        // Mark current model as temporarily unavailable
        $model = $this->getModelByName($currentModel);
        if ($model) {
            // Force increment to max to trigger fallback
            $rpmKey = "model_rpm:{$currentModel}";
            Cache::put($rpmKey, $model['rpm'], now()->addMinute());
        }
        
        // Get next available model
        $fallbackModel = $this->getBestAvailableModel();
        
        if ($fallbackModel['name'] !== $currentModel) {
            Log::info('Switched to fallback model', [
                'from' => $currentModel,
                'to' => $fallbackModel['name']
            ]);
            return $fallbackModel;
        }
        
        return null;
    }
    
    /**
     * Get recommended model for specific use case
     */
    public function getRecommendedModel(string $useCase = 'general'): array
    {
        switch ($useCase) {
            case 'high_volume':
                // Prioritize Flash-Lite (highest limits)
                return $this->getModelByName('gemini-2.5-flash-lite') ?? $this->models[0];
                
            case 'high_quality':
                // Prioritize Pro or Gemini 3
                return $this->getModelByName('gemini-2.5-pro') ?? $this->models[0];
                
            case 'experimental':
                // Try Gemini 3
                return $this->getModelByName('gemini-3-flash-preview') ?? $this->models[0];
                
            case 'general':
            default:
                // Use smart selection
                return $this->getBestAvailableModel();
        }
    }
}
