# 🤖 Sistem Machine Learning Hybrid - Smart Copy SMK

## 📋 Overview

Sistem ML hybrid yang menggabungkan:
1. **Data Free/Optimized** (default) - Data ML yang dioptimalkan dari analytics
2. **Google Places API** (optional) - Data real-time dari Google
3. **Self-Learning System** - Auto-update setiap hari dari performa caption

## 🎯 Konsep

### Flow Sistem

```
User Generate Caption
    ↓
Gunakan ML Data (Free)
    ↓
Caption Performance Tracking
    ↓
Engagement Rate < 2%?
    ↓ YES
Suggest Upgrade ke Google API
    ↓ NO
Continue dengan ML Data
    ↓
Daily Training (3 AM)
    ↓
ML Data Improved
```

### Keunggulan

1. **Free by Default**: User bisa langsung pakai tanpa API key
2. **Smart Suggestion**: Sistem otomatis suggest upgrade jika performa rendah
3. **Self-Improving**: ML belajar dari data real setiap hari
4. **Optional Upgrade**: User bisa upgrade kapan saja untuk data lebih akurat

## 🗄️ Database Schema

### Table: ml_optimized_data

Menyimpan data ML yang sudah dioptimalkan.

```sql
CREATE TABLE ml_optimized_data (
    id BIGINT PRIMARY KEY,
    type VARCHAR(255),              -- hashtag, keyword, topic, hook, cta
    industry VARCHAR(255),          -- fashion, food, beauty, etc
    platform VARCHAR(255),          -- instagram, facebook, tiktok
    data TEXT,                      -- actual data
    performance_score DECIMAL(8,2), -- 0-100
    usage_count INT,                -- berapa kali digunakan
    is_active BOOLEAN,              -- aktif atau tidak
    last_trained_at TIMESTAMP,      -- terakhir di-train
    metadata JSON,                  -- data tambahan
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table: ml_training_logs

Menyimpan log training harian.

```sql
CREATE TABLE ml_training_logs (
    id BIGINT PRIMARY KEY,
    trained_at TIMESTAMP,
    duration_seconds INT,
    hashtags_trained INT,
    keywords_trained INT,
    topics_trained INT,
    hooks_trained INT,
    ctas_trained INT,
    total_trained INT,
    errors JSON,
    status VARCHAR(255),            -- success, partial, failed
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🔧 Services

### 1. MLDataService

Service untuk manage data ML (free/optimized).

**Methods:**

```php
// Get trending hashtags
getTrendingHashtags(string $industry, string $platform, int $limit): array

// Get keyword suggestions
getKeywordSuggestions(string $query, string $industry, int $limit): array

// Get trending topics
getTrendingTopics(string $industry, int $limit): array

// Get best performing hooks
getBestHooks(string $industry, string $tone, int $limit): array

// Get best performing CTAs
getBestCTAs(string $industry, string $goal, int $limit): array

// Check if should suggest upgrade
shouldSuggestUpgrade(int $userId): bool

// Get upgrade suggestion message
getUpgradeSuggestion(): array
```

**Usage Example:**

```php
use App\Services\MLDataService;

$mlService = new MLDataService();

// Get hashtags
$hashtags = $mlService->getTrendingHashtags('fashion', 'instagram', 30);

// Get keywords
$keywords = $mlService->getKeywordSuggestions('baju', 'fashion', 10);

// Check if should upgrade
if ($mlService->shouldSuggestUpgrade($userId)) {
    $suggestion = $mlService->getUpgradeSuggestion();
    // Show upgrade modal
}
```

### 2. GooglePlacesService

Service untuk Google Places API (optional).

**Methods:**

```php
// Check if enabled
isEnabled(): bool

// Get trending places
getTrendingPlaces(string $industry, string $location, int $limit): array

// Get keyword suggestions from Google
getKeywordSuggestions(string $query, string $location): array

// Get place details
getPlaceDetails(string $placeId): ?array

// Get API usage info
getUsageInfo(): array
```

**Usage Example:**

```php
use App\Services\GooglePlacesService;

$googleService = new GooglePlacesService();

if ($googleService->isEnabled()) {
    // Get trending places
    $places = $googleService->getTrendingPlaces('food', 'Jakarta', 20);
    
    // Get keywords from Google
    $keywords = $googleService->getKeywordSuggestions('restoran', 'Jakarta');
}
```

### 3. MLTrainingService

Service untuk auto-training dari analytics.

**Methods:**

```php
// Run daily training
runDailyTraining(): array
```

**Usage Example:**

```php
use App\Services\MLTrainingService;

$trainingService = new MLTrainingService();

// Run training (biasanya via cron)
$results = $trainingService->runDailyTraining();
```

## ⚙️ Configuration

### .env Configuration

```env
# Google Places API (Optional - for real data)
# Get API key from: https://console.cloud.google.com/apis/credentials
# Enable: Places API, Geocoding API
GOOGLE_PLACES_API_KEY=
```

### config/services.php

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
    'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
],
```

## 🚀 Installation

### 1. Run Migrations

```bash
php artisan migrate
```

### 2. Seed Initial Data

```bash
php artisan db:seed --class=MLOptimizedDataSeeder
```

### 3. Setup Cron Job

Add to crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Test Training Command

```bash
php artisan ml:train-daily
```

## 📊 Daily Training Schedule

Training berjalan otomatis setiap hari jam 3 pagi:

```php
// routes/console.php
Schedule::command('ml:train-daily')
    ->dailyAt('03:00')
    ->name('ml-daily-training')
    ->withoutOverlapping()
    ->onOneServer();
```

## 🔄 Training Process

### 1. Extract Data dari Analytics

Training mengambil data dari `caption_analytics` table:
- Caption dengan engagement rate > 5%
- Data dari 30 hari terakhir

### 2. Train 5 Jenis Data

1. **Hashtags** - Extract dari caption sukses
2. **Keywords** - Word frequency analysis
3. **Topics** - First sentence extraction
4. **Hooks** - Opening line extraction
5. **CTAs** - Last line extraction

### 3. Update Performance Score

Score di-update dengan weighted average:
```php
newScore = (oldScore * 0.7) + (newScore * 0.3)
```

### 4. Clean Low-Performing Data

Data dengan score < 2.0 dan tidak digunakan 30 hari di-deactivate.

## 📈 Usage Flow

### Scenario 1: User Baru (Free Tier)

```php
// 1. User generate caption
$mlService = new MLDataService();
$hashtags = $mlService->getTrendingHashtags('fashion', 'instagram');

// 2. Caption generated dengan ML data
// 3. User track analytics
// 4. Engagement rate tracked

// 5. Check if should suggest upgrade
if ($mlService->shouldSuggestUpgrade($userId)) {
    // Show: "Engagement rate Anda rendah. Upgrade ke Google API?"
}
```

### Scenario 2: User Upgrade (Google API)

```php
// 1. User enable Google API
$googleService = new GooglePlacesService();

if ($googleService->isEnabled()) {
    // 2. Get real data from Google
    $places = $googleService->getTrendingPlaces('food', 'Jakarta');
    $keywords = $googleService->getKeywordSuggestions('restoran', 'Jakarta');
    
    // 3. Generate caption dengan data real
}
```

### Scenario 3: Daily Training

```php
// Runs automatically at 3 AM
// 1. Extract high-performing captions
// 2. Train hashtags, keywords, topics, hooks, CTAs
// 3. Update performance scores
// 4. Clean low-performing data
// 5. Log results
```

## 🎨 UI Implementation

### Upgrade Suggestion Modal

```html
<!-- Show when engagement rate < 2% -->
<div class="modal">
    <h3>💡 Tingkatkan Performa Caption Anda</h3>
    <p>Engagement rate Anda masih di bawah rata-rata.</p>
    <p>Gunakan data real dari Google untuk hasil lebih akurat!</p>
    
    <ul>
        <li>✅ Data trending real-time dari Google</li>
        <li>✅ Keyword research lebih akurat</li>
        <li>✅ Hashtag berdasarkan lokasi</li>
        <li>✅ Analisis kompetitor</li>
    </ul>
    
    <button>Upgrade ke Google API</button>
    <button>Tetap Gunakan Data Free</button>
</div>
```

### Settings Page

```html
<div class="settings">
    <h3>🤖 ML & Data Settings</h3>
    
    <!-- Google Places API -->
    <div class="setting-item">
        <label>Google Places API Key</label>
        <input type="text" name="google_places_api_key" />
        <p class="help">Optional. Untuk data real-time dari Google.</p>
    </div>
    
    <!-- ML Status -->
    <div class="ml-status">
        <h4>ML Training Status</h4>
        <p>Last trained: {{ $lastTrained }}</p>
        <p>Total data: {{ $totalData }}</p>
        <p>Next training: Tomorrow 3:00 AM</p>
    </div>
</div>
```

## 📊 Monitoring

### Check Training Logs

```php
use App\Models\MLTrainingLog;

// Get recent trainings
$logs = MLTrainingLog::recent(7)->get();

// Get last training
$lastTraining = MLTrainingLog::latest('trained_at')->first();

// Check success rate
$successRate = MLTrainingLog::successful()->count() / MLTrainingLog::count() * 100;
```

### Check ML Data Stats

```php
use App\Models\MLOptimizedData;

// Total active data
$totalActive = MLOptimizedData::active()->count();

// High performing data
$highPerforming = MLOptimizedData::highPerforming(8.0)->count();

// Data by type
$hashtags = MLOptimizedData::ofType('hashtag')->count();
$keywords = MLOptimizedData::ofType('keyword')->count();
```

## 🔍 Troubleshooting

### Training Tidak Jalan

```bash
# Check cron
php artisan schedule:list

# Run manual
php artisan ml:train-daily

# Check logs
tail -f storage/logs/laravel.log
```

### Data Tidak Muncul

```bash
# Check database
php artisan tinker
>>> App\Models\MLOptimizedData::count()

# Re-seed
php artisan db:seed --class=MLOptimizedDataSeeder
```

### Google API Error

```bash
# Check API key
php artisan tinker
>>> config('services.google.places_api_key')

# Test API
$service = new App\Services\GooglePlacesService();
$service->isEnabled(); // should return true
```

## 🎯 Best Practices

### 1. Cache Strategy

ML data di-cache 1 jam untuk performa:

```php
Cache::remember("ml_hashtags_{$industry}_{$platform}", 3600, function() {
    // Query database
});
```

### 2. Fallback Strategy

Selalu sediakan fallback data:

```php
$data = MLOptimizedData::where(...)->get();

if ($data->isEmpty()) {
    return $this->getDefaultData();
}
```

### 3. Performance Score

Score 0-100:
- 0-2: Low (akan di-deactivate)
- 2-5: Medium
- 5-8: Good
- 8-10: Excellent

### 4. Training Frequency

- Daily training: 3 AM
- Data retention: 30 hari
- Cleanup: Data < 2.0 score + 30 hari tidak digunakan

## 📝 Future Enhancements

### Phase 1 (Current)
- ✅ ML Data Service
- ✅ Google Places Service
- ✅ Daily Training
- ✅ Auto-suggest upgrade

### Phase 2 (Next)
- [ ] A/B Testing captions
- [ ] Sentiment analysis
- [ ] Competitor analysis
- [ ] Location-based hashtags

### Phase 3 (Future)
- [ ] Deep learning model
- [ ] Image analysis
- [ ] Video caption generation
- [ ] Multi-language support

## 🤝 Contributing

Untuk menambah fitur ML:

1. Tambah method di `MLDataService`
2. Tambah training logic di `MLTrainingService`
3. Update migration jika perlu
4. Update seeder
5. Test dengan `php artisan ml:train-daily`

## 📞 Support

Jika ada pertanyaan atau issue:
- Check logs: `storage/logs/laravel.log`
- Run diagnostics: `php artisan ml:train-daily`
- Check database: `php artisan tinker`

---

**Made with ❤️ for UMKM Indonesia**

*Sistem ML yang belajar dari data real untuk hasil lebih baik setiap hari!*
