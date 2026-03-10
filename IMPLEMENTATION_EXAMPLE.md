# 💻 Contoh Implementasi ML System

## 1. Integrasi di AI Generator Controller

### Update AIGeneratorController

```php
<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Services\MLDataService;
use App\Services\GooglePlacesService;
use Illuminate\Http\Request;

class AIGeneratorController extends Controller
{
    protected $aiService;
    protected $mlService;
    protected $googleService;
    
    public function __construct(
        AIService $aiService,
        MLDataService $mlService,
        GooglePlacesService $googleService
    ) {
        $this->aiService = $aiService;
        $this->mlService = $mlService;
        $this->googleService = $googleService;
    }
    
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'industry' => 'required|string',
            'product_name' => 'required|string',
            'platform' => 'required|string',
            'tone' => 'required|string',
            'goal' => 'required|string',
            'use_google_api' => 'boolean',
        ]);
        
        // Get ML data (free) atau Google data (premium)
        $useGoogleAPI = $validated['use_google_api'] ?? false;
        
        if ($useGoogleAPI && $this->googleService->isEnabled()) {
            // Use Google API for real data
            $hashtags = $this->getGoogleHashtags($validated);
            $keywords = $this->getGoogleKeywords($validated);
        } else {
            // Use ML optimized data (free)
            $hashtags = $this->mlService->getTrendingHashtags(
                $validated['industry'],
                $validated['platform'],
                30
            );
            
            $keywords = $this->mlService->getKeywordSuggestions(
                $validated['product_name'],
                $validated['industry'],
                10
            );
        }
        
        // Get best hooks & CTAs from ML
        $hooks = $this->mlService->getBestHooks(
            $validated['industry'],
            $validated['tone'],
            5
        );
        
        $ctas = $this->mlService->getBestCTAs(
            $validated['industry'],
            $validated['goal'],
            5
        );
        
        // Generate caption dengan AI
        $captions = $this->aiService->generateCaption([
            ...$validated,
            'suggested_hashtags' => $hashtags,
            'suggested_keywords' => $keywords,
            'suggested_hooks' => $hooks,
            'suggested_ctas' => $ctas,
        ]);
        
        // Check if should suggest upgrade
        $shouldUpgrade = $this->mlService->shouldSuggestUpgrade(auth()->id());
        $upgradeSuggestion = $shouldUpgrade 
            ? $this->mlService->getUpgradeSuggestion() 
            : null;
        
        return view('ai-generator.result', [
            'captions' => $captions,
            'hashtags' => $hashtags,
            'upgrade_suggestion' => $upgradeSuggestion,
        ]);
    }
    
    private function getGoogleHashtags($data)
    {
        // Get trending places from Google
        $places = $this->googleService->getTrendingPlaces(
            $data['industry'],
            'Indonesia',
            20
        );
        
        // Extract hashtags from place names/types
        $hashtags = [];
        foreach ($places as $place) {
            $hashtags[] = '#' . str_replace(' ', '', strtolower($place['name']));
        }
        
        return array_slice($hashtags, 0, 30);
    }
    
    private function getGoogleKeywords($data)
    {
        // Get keyword suggestions from Google
        $suggestions = $this->googleService->getKeywordSuggestions(
            $data['product_name'],
            'Indonesia'
        );
        
        return array_map(function($item) {
            return [
                'keyword' => $item['keyword'],
                'score' => 100, // Google data = high score
                'search_volume' => 10000,
            ];
        }, $suggestions);
    }
}
```

## 2. Update View dengan Upgrade Modal

### resources/views/ai-generator/result.blade.php

```blade
<div class="container">
    <!-- Caption Results -->
    <div class="captions">
        @foreach($captions as $caption)
            <div class="caption-card">
                {{ $caption }}
            </div>
        @endforeach
    </div>
    
    <!-- Hashtags -->
    <div class="hashtags">
        <h3>Hashtag Suggestions</h3>
        @foreach($hashtags as $hashtag)
            <span class="badge">{{ $hashtag }}</span>
        @endforeach
    </div>
    
    <!-- Upgrade Modal (if needed) -->
    @if($upgrade_suggestion)
        <div id="upgradeModal" class="modal">
            <div class="modal-content">
                <h3>{{ $upgrade_suggestion['title'] }}</h3>
                <p>{{ $upgrade_suggestion['message'] }}</p>
                
                <ul>
                    @foreach($upgrade_suggestion['benefits'] as $benefit)
                        <li>✅ {{ $benefit }}</li>
                    @endforeach
                </ul>
                
                <div class="modal-actions">
                    <button onclick="enableGoogleAPI()">
                        {{ $upgrade_suggestion['cta'] }}
                    </button>
                    <button onclick="closeModal()">
                        Tetap Gunakan Data Free
                    </button>
                </div>
            </div>
        </div>
        
        <script>
            // Show modal after 3 seconds
            setTimeout(() => {
                document.getElementById('upgradeModal').style.display = 'block';
            }, 3000);
            
            function enableGoogleAPI() {
                window.location.href = '/settings/google-api';
            }
            
            function closeModal() {
                document.getElementById('upgradeModal').style.display = 'none';
            }
        </script>
    @endif
</div>
```

## 3. Settings Page untuk Google API

### routes/web.php

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/settings/google-api', [SettingsController::class, 'googleAPI'])->name('settings.google-api');
    Route::post('/settings/google-api', [SettingsController::class, 'saveGoogleAPI'])->name('settings.google-api.save');
});
```

### app/Http/Controllers/SettingsController.php

```php
<?php

namespace App\Http\Controllers;

use App\Services\GooglePlacesService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function googleAPI(GooglePlacesService $googleService)
    {
        $status = $googleService->getUsageInfo();
        
        return view('settings.google-api', [
            'status' => $status,
            'api_key' => config('services.google.places_api_key'),
        ]);
    }
    
    public function saveGoogleAPI(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string',
        ]);
        
        // Save to .env file
        $this->updateEnvFile('GOOGLE_PLACES_API_KEY', $validated['api_key']);
        
        // Clear config cache
        \Artisan::call('config:clear');
        
        return redirect()->back()->with('success', 'Google API key saved successfully!');
    }
    
    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            if (str_contains($content, $key)) {
                // Update existing
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
            } else {
                // Add new
                $content .= "\n{$key}={$value}\n";
            }
            
            file_put_contents($path, $content);
        }
    }
}
```

### resources/views/settings/google-api.blade.php

```blade
<div class="container">
    <h2>🔑 Google Places API Settings</h2>
    
    <!-- Status -->
    <div class="status-card">
        <h3>Status</h3>
        <p>
            @if($status['enabled'])
                ✅ Enabled
            @else
                ❌ Disabled
            @endif
        </p>
        
        <h4>Available Features:</h4>
        <ul>
            @foreach($status['features'] as $feature => $enabled)
                <li>
                    @if($enabled)
                        ✅ {{ ucfirst(str_replace('_', ' ', $feature)) }}
                    @else
                        ❌ {{ ucfirst(str_replace('_', ' ', $feature)) }}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    
    <!-- Form -->
    <form method="POST" action="{{ route('settings.google-api.save') }}">
        @csrf
        
        <div class="form-group">
            <label>Google Places API Key</label>
            <input 
                type="text" 
                name="api_key" 
                value="{{ old('api_key', $api_key) }}"
                placeholder="AIzaSy..."
                required
            />
            <small>
                Get your API key from: 
                <a href="https://console.cloud.google.com/apis/credentials" target="_blank">
                    Google Cloud Console
                </a>
            </small>
        </div>
        
        <button type="submit">Save API Key</button>
    </form>
    
    <!-- Instructions -->
    <div class="instructions">
        <h3>📝 How to Get API Key</h3>
        <ol>
            <li>Go to <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a></li>
            <li>Create a new project (or select existing)</li>
            <li>Enable these APIs:
                <ul>
                    <li>Places API</li>
                    <li>Geocoding API</li>
                </ul>
            </li>
            <li>Create API Key in Credentials</li>
            <li>Copy and paste here</li>
        </ol>
    </div>
</div>
```

## 4. Update Analytics untuk Training

### Saat Save Analytics

```php
use App\Models\CaptionAnalytics;

// Save analytics dengan industry
CaptionAnalytics::create([
    'user_id' => auth()->id(),
    'caption_text' => $caption,
    'industry' => $industry,        // ⭐ Important for training
    'platform' => $platform,        // ⭐ Important for training
    'category' => $category,
    'subcategory' => $subcategory,
    'tone' => $tone,
    'likes' => $likes,
    'comments' => $comments,
    'shares' => $shares,
    'engagement_rate' => $engagementRate,
    'posted_at' => now(),
]);
```

## 5. Dashboard untuk Monitoring

### app/Http/Controllers/Admin/MLDashboardController.php

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MLOptimizedData;
use App\Models\MLTrainingLog;

class MLDashboardController extends Controller
{
    public function index()
    {
        // Stats
        $stats = [
            'total_data' => MLOptimizedData::active()->count(),
            'high_performing' => MLOptimizedData::highPerforming(8.0)->count(),
            'last_training' => MLTrainingLog::latest('trained_at')->first(),
            'success_rate' => $this->getSuccessRate(),
        ];
        
        // Data by type
        $dataByType = [
            'hashtags' => MLOptimizedData::ofType('hashtag')->active()->count(),
            'keywords' => MLOptimizedData::ofType('keyword')->active()->count(),
            'topics' => MLOptimizedData::ofType('topic')->active()->count(),
            'hooks' => MLOptimizedData::ofType('hook')->active()->count(),
            'ctas' => MLOptimizedData::ofType('cta')->active()->count(),
        ];
        
        // Recent trainings
        $recentTrainings = MLTrainingLog::recent(7)->get();
        
        return view('admin.ml-dashboard', compact('stats', 'dataByType', 'recentTrainings'));
    }
    
    private function getSuccessRate()
    {
        $total = MLTrainingLog::count();
        if ($total === 0) return 0;
        
        $successful = MLTrainingLog::successful()->count();
        return round(($successful / $total) * 100, 2);
    }
}
```

## 6. API Endpoint (Optional)

### routes/api.php

```php
Route::middleware('auth:sanctum')->group(function () {
    // Get ML suggestions
    Route::get('/ml/hashtags/{industry}/{platform}', function($industry, $platform) {
        $ml = new \App\Services\MLDataService();
        return $ml->getTrendingHashtags($industry, $platform, 30);
    });
    
    Route::get('/ml/keywords/{query}/{industry}', function($query, $industry) {
        $ml = new \App\Services\MLDataService();
        return $ml->getKeywordSuggestions($query, $industry, 10);
    });
    
    // Check upgrade suggestion
    Route::get('/ml/should-upgrade', function() {
        $ml = new \App\Services\MLDataService();
        return [
            'should_upgrade' => $ml->shouldSuggestUpgrade(auth()->id()),
            'suggestion' => $ml->getUpgradeSuggestion(),
        ];
    });
});
```

## 7. Testing

### tests/Feature/MLSystemTest.php

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\MLDataService;
use App\Services\GooglePlacesService;
use App\Models\MLOptimizedData;

class MLSystemTest extends TestCase
{
    public function test_ml_data_service_returns_hashtags()
    {
        $ml = new MLDataService();
        $hashtags = $ml->getTrendingHashtags('fashion', 'instagram', 10);
        
        $this->assertIsArray($hashtags);
        $this->assertNotEmpty($hashtags);
    }
    
    public function test_google_service_checks_enabled()
    {
        $google = new GooglePlacesService();
        $enabled = $google->isEnabled();
        
        $this->assertIsBool($enabled);
    }
    
    public function test_ml_data_seeded()
    {
        $count = MLOptimizedData::count();
        $this->assertGreaterThan(0, $count);
    }
}
```

---

## Summary

Sistem sudah siap digunakan dengan:

1. ✅ ML Data Service (free)
2. ✅ Google Places Service (optional)
3. ✅ Auto-training setiap hari
4. ✅ Upgrade suggestion otomatis
5. ✅ Settings page untuk API key
6. ✅ Dashboard monitoring
7. ✅ API endpoints

**Next**: Integrate ke UI dan test dengan user real! 🚀
