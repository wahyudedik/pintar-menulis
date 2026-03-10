# 🚀 ML System Quick Start

## Konsep Sistem

Sistem ML hybrid yang:
1. **Default**: Pakai data free/optimized dari ML kita
2. **Jika user tidak puas**: Tawarkan upgrade ke Google Places API
3. **Self-learning**: Sistem belajar otomatis setiap hari dari analytics

## Flow Sederhana

```
User Generate Caption
    ↓
Pakai ML Data (Free) ✅
    ↓
Track Performance
    ↓
Engagement < 2%? 
    ↓ YES
"Mau pakai data real Google?" 💡
    ↓ NO  
Tetap pakai ML Data
    ↓
Training Otomatis (3 AM) 🤖
    ↓
ML Makin Pintar! 📈
```

## Setup (5 Menit)

### 1. Migration & Seeder

```bash
php artisan migrate
php artisan db:seed --class=MLOptimizedDataSeeder
```

### 2. Test Training

```bash
php artisan ml:train-daily
```

### 3. Setup Cron (Production)

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Done! ✅

## Cara Pakai di Code

### Get ML Data (Free)

```php
use App\Services\MLDataService;

$ml = new MLDataService();

// Hashtags
$hashtags = $ml->getTrendingHashtags('fashion', 'instagram', 30);

// Keywords
$keywords = $ml->getKeywordSuggestions('baju', 'fashion', 10);

// Hooks
$hooks = $ml->getBestHooks('fashion', 'casual', 5);

// CTAs
$ctas = $ml->getBestCTAs('fashion', 'closing', 5);
```

### Check Upgrade Suggestion

```php
if ($ml->shouldSuggestUpgrade($userId)) {
    $suggestion = $ml->getUpgradeSuggestion();
    // Show modal: "Engagement rendah, mau upgrade?"
}
```

### Use Google API (Optional)

```php
use App\Services\GooglePlacesService;

$google = new GooglePlacesService();

if ($google->isEnabled()) {
    $places = $google->getTrendingPlaces('food', 'Jakarta', 20);
    $keywords = $google->getKeywordSuggestions('restoran', 'Jakarta');
}
```

## Config Google API (Optional)

### .env

```env
GOOGLE_PLACES_API_KEY=your_api_key_here
```

### Get API Key

1. Go to: https://console.cloud.google.com/apis/credentials
2. Create API Key
3. Enable: Places API, Geocoding API
4. Copy key ke .env

## Training Schedule

- **Waktu**: Setiap hari jam 3 pagi
- **Proses**: 
  1. Ambil caption dengan engagement > 5%
  2. Extract hashtags, keywords, topics, hooks, CTAs
  3. Update performance score
  4. Clean data lama (score < 2.0)
- **Duration**: ~1-2 detik

## Monitoring

### Check Training Logs

```php
use App\Models\MLTrainingLog;

$lastTraining = MLTrainingLog::latest('trained_at')->first();
```

### Check ML Data

```php
use App\Models\MLOptimizedData;

$totalData = MLOptimizedData::active()->count();
$highPerforming = MLOptimizedData::highPerforming(8.0)->count();
```

## Troubleshooting

### Training Error?

```bash
# Check logs
tail -f storage/logs/laravel.log

# Run manual
php artisan ml:train-daily
```

### Data Kosong?

```bash
# Re-seed
php artisan db:seed --class=MLOptimizedDataSeeder
```

### Google API Error?

```bash
# Check config
php artisan tinker
>>> config('services.google.places_api_key')
```

## Next Steps

1. ✅ Integrate ke AI Generator
2. ✅ Add upgrade modal di UI
3. ✅ Track analytics dengan industry
4. ✅ Monitor training logs

## Files Created

```
app/Services/
├── MLDataService.php           # ML data management
├── GooglePlacesService.php     # Google API integration
└── MLTrainingService.php       # Auto-training logic

app/Models/
├── MLOptimizedData.php         # ML data model
└── MLTrainingLog.php           # Training log model

app/Console/Commands/
└── MLDailyTraining.php         # Training command

database/migrations/
├── 2026_03_10_045810_create_ml_optimized_data_table.php
├── 2026_03_10_045817_create_ml_training_logs_table.php
└── 2026_03_10_050306_add_industry_to_caption_analytics_table.php

database/seeders/
└── MLOptimizedDataSeeder.php   # Initial data

routes/
└── console.php                 # Schedule config
```

---

**Sistem siap digunakan!** 🎉

Data free sudah tersedia, Google API optional, training otomatis setiap hari!
