# 🔑 Panduan Lengkap Setup Google API

## 📋 Daftar Isi

1. [Google Places API](#google-places-api) - Untuk ML System (Real-time data)
2. [Google Ads API](#google-ads-api) - Untuk Keyword Research
3. [Troubleshooting](#troubleshooting)

---

## 🗺️ Google Places API

### Untuk Apa?
- Mendapatkan data real-time trending places
- Keyword suggestions dari Google Autocomplete
- Location-based hashtags
- Competitor analysis

### Langkah-langkah Setup

#### 1. Buat Project di Google Cloud Console

1. **Buka Google Cloud Console**
   - URL: https://console.cloud.google.com/
   - Login dengan akun Google kamu

2. **Buat Project Baru**
   - Click dropdown project di atas (sebelah logo Google Cloud)
   - Click "NEW PROJECT"
   - Nama project: `smart-copy-smk` (atau nama lain)
   - Click "CREATE"
   - Tunggu beberapa detik sampai project dibuat

#### 2. Enable APIs

1. **Buka API Library**
   - URL: https://console.cloud.google.com/apis/library
   - Atau: Menu ☰ → APIs & Services → Library

2. **Enable Places API**
   - Search: "Places API"
   - Click "Places API"
   - Click "ENABLE"
   - Tunggu sampai enabled

3. **Enable Geocoding API**
   - Search: "Geocoding API"
   - Click "Geocoding API"
   - Click "ENABLE"
   - Tunggu sampai enabled

#### 3. Buat API Key

1. **Buka Credentials**
   - URL: https://console.cloud.google.com/apis/credentials
   - Atau: Menu ☰ → APIs & Services → Credentials

2. **Create Credentials**
   - Click "CREATE CREDENTIALS"
   - Pilih "API key"
   - API key akan dibuat otomatis
   - Copy API key (contoh: `AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`)

3. **Restrict API Key (Recommended)**
   - Click "RESTRICT KEY" atau edit API key
   - **Application restrictions:**
     - Pilih "HTTP referrers (web sites)"
     - Add: `http://localhost:8000/*` (development)
     - Add: `https://yourdomain.com/*` (production)
   - **API restrictions:**
     - Pilih "Restrict key"
     - Select APIs:
       - ✅ Places API
       - ✅ Geocoding API
   - Click "SAVE"

#### 4. Setup Billing (Required!)

⚠️ **PENTING**: Google Places API memerlukan billing account, tapi ada free tier!

1. **Enable Billing**
   - URL: https://console.cloud.google.com/billing
   - Click "LINK A BILLING ACCOUNT"
   - Atau "CREATE BILLING ACCOUNT"

2. **Masukkan Info Kartu Kredit**
   - Nama
   - Alamat
   - Kartu kredit (Visa/Mastercard)
   - ⚠️ Tidak akan di-charge otomatis!

3. **Free Tier Info**
   - $200 credit per bulan (gratis)
   - Places API: $17 per 1000 requests
   - Geocoding API: $5 per 1000 requests
   - Cukup untuk ~11,000 requests/bulan (gratis)

#### 5. Tambahkan ke .env

```env
# Google Places API (Optional - for real data)
GOOGLE_PLACES_API_KEY=your_api_key_here
```

#### 6. Test API Key

```bash
# Test Places API
curl "https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurant+in+Jakarta&key=YOUR_API_KEY"

# Test Geocoding API
curl "https://maps.googleapis.com/maps/api/geocode/json?address=Jakarta&key=YOUR_API_KEY"
```

Jika berhasil, akan return JSON dengan data places/geocoding.

---

## 📊 Google Ads API

### Untuk Apa?
- Keyword research (search volume, competition)
- Keyword ideas & suggestions
- CPC (Cost Per Click) data
- Trend analysis

### Langkah-langkah Setup

#### 1. Buat Google Ads Account

1. **Buka Google Ads**
   - URL: https://ads.google.com/
   - Login dengan akun Google kamu

2. **Buat Campaign (Skip)**
   - Google akan minta buat campaign
   - Click "Switch to Expert Mode"
   - Click "Create an account without a campaign"
   - Pilih negara: Indonesia
   - Timezone: (GMT+07:00) Jakarta
   - Currency: IDR - Indonesian Rupiah
   - Click "SUBMIT"

3. **Catat Customer ID**
   - Setelah account dibuat, lihat di pojok kanan atas
   - Format: `123-456-7890`
   - Ini adalah CUSTOMER_ID kamu

#### 2. Apply for API Access

⚠️ **PENTING**: Google Ads API memerlukan approval!

1. **Buka API Center**
   - URL: https://ads.google.com/aw/apicenter
   - Atau: Tools & Settings → Setup → API Center

2. **Request API Access**
   - Click "Request API Access"
   - Isi form:
     - **Company name**: Nama perusahaan/project
     - **Website**: URL website kamu
     - **Description**: "Keyword research for UMKM copywriting tool"
     - **Estimated API calls**: "< 10,000 per day"
   - Click "SUBMIT"

3. **Tunggu Approval**
   - Biasanya 1-2 hari kerja
   - Akan dapat email notifikasi
   - ⚠️ Bisa ditolak jika tidak jelas use case-nya

#### 3. Buat OAuth 2.0 Credentials

1. **Buka Google Cloud Console**
   - URL: https://console.cloud.google.com/
   - Pilih project yang sama dengan Places API

2. **Enable Google Ads API**
   - URL: https://console.cloud.google.com/apis/library
   - Search: "Google Ads API"
   - Click "ENABLE"

3. **Configure OAuth Consent Screen**
   - URL: https://console.cloud.google.com/apis/credentials/consent
   - User Type: "External"
   - Click "CREATE"
   - Isi form:
     - **App name**: Smart Copy SMK
     - **User support email**: email kamu
     - **Developer contact**: email kamu
   - Click "SAVE AND CONTINUE"
   - Scopes: Skip (click "SAVE AND CONTINUE")
   - Test users: Add email kamu
   - Click "SAVE AND CONTINUE"

4. **Create OAuth Client ID**
   - URL: https://console.cloud.google.com/apis/credentials
   - Click "CREATE CREDENTIALS" → "OAuth client ID"
   - Application type: "Web application"
   - Name: "Smart Copy SMK"
   - Authorized redirect URIs:
     - Add: `http://localhost:8000/auth/google/callback`
     - Add: `https://yourdomain.com/auth/google/callback`
   - Click "CREATE"
   - **Copy CLIENT_ID dan CLIENT_SECRET**

#### 4. Generate Refresh Token

⚠️ **Ini bagian paling tricky!**

1. **Install Google Ads API Client Library**
   ```bash
   composer require googleads/google-ads-php
   ```

2. **Buat Script Generate Token**
   
   Create file: `generate-google-ads-token.php`
   
   ```php
   <?php
   require 'vendor/autoload.php';
   
   $clientId = 'YOUR_CLIENT_ID';
   $clientSecret = 'YOUR_CLIENT_SECRET';
   $redirectUri = 'http://localhost:8000/auth/google/callback';
   
   // Step 1: Generate authorization URL
   $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
       'client_id' => $clientId,
       'redirect_uri' => $redirectUri,
       'response_type' => 'code',
       'scope' => 'https://www.googleapis.com/auth/adwords',
       'access_type' => 'offline',
       'prompt' => 'consent',
   ]);
   
   echo "1. Buka URL ini di browser:\n";
   echo $authUrl . "\n\n";
   echo "2. Login dan authorize\n";
   echo "3. Copy 'code' dari URL redirect\n";
   echo "4. Paste code di sini: ";
   
   $code = trim(fgets(STDIN));
   
   // Step 2: Exchange code for tokens
   $ch = curl_init('https://oauth2.googleapis.com/token');
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
       'code' => $code,
       'client_id' => $clientId,
       'client_secret' => $clientSecret,
       'redirect_uri' => $redirectUri,
       'grant_type' => 'authorization_code',
   ]));
   
   $response = curl_exec($ch);
   curl_close($ch);
   
   $tokens = json_decode($response, true);
   
   echo "\n=== TOKENS ===\n";
   echo "REFRESH_TOKEN: " . ($tokens['refresh_token'] ?? 'ERROR') . "\n";
   echo "ACCESS_TOKEN: " . ($tokens['access_token'] ?? 'ERROR') . "\n";
   ```

3. **Jalankan Script**
   ```bash
   php generate-google-ads-token.php
   ```

4. **Follow Instructions**
   - Buka URL yang muncul
   - Login & authorize
   - Copy code dari URL redirect
   - Paste ke terminal
   - **Copy REFRESH_TOKEN**

#### 5. Get Developer Token

1. **Buka API Center**
   - URL: https://ads.google.com/aw/apicenter
   - Setelah approved, akan ada "Developer token"
   - Copy token (format: `abcdefghijklmnop`)

#### 6. Tambahkan ke .env

```env
# Google Ads API (Optional - for keyword research)
GOOGLE_ADS_DEVELOPER_TOKEN=abcdefghijklmnop
GOOGLE_ADS_CLIENT_ID=123456789-abc.apps.googleusercontent.com
GOOGLE_ADS_CLIENT_SECRET=GOCSPX-abc123def456
GOOGLE_ADS_REFRESH_TOKEN=1//abc123def456...
GOOGLE_ADS_CUSTOMER_ID=123-456-7890
```

#### 7. Test API

```bash
php artisan tinker

>>> $service = app(\App\Services\GoogleAdsService::class);
>>> $keywords = $service->getKeywordIdeas('baju murah');
>>> dd($keywords);
```

---

## 🔧 Troubleshooting

### Google Places API

#### Error: "This API project is not authorized"
**Solution:**
- Check API key sudah benar
- Pastikan Places API sudah enabled
- Check API restrictions (jangan terlalu ketat)

#### Error: "REQUEST_DENIED"
**Solution:**
- Enable billing account
- Check quota limits
- Verify API key restrictions

#### Error: "OVER_QUERY_LIMIT"
**Solution:**
- Sudah exceed free tier ($200/month)
- Reduce API calls
- Implement caching
- Upgrade billing

### Google Ads API

#### Error: "Developer token not approved"
**Solution:**
- Tunggu approval dari Google (1-2 hari)
- Re-apply dengan use case yang jelas
- Gunakan test account sementara

#### Error: "Invalid refresh token"
**Solution:**
- Generate ulang refresh token
- Pastikan scope correct: `https://www.googleapis.com/auth/adwords`
- Check client ID & secret benar

#### Error: "Customer not found"
**Solution:**
- Check CUSTOMER_ID format: `123-456-7890`
- Pastikan account sudah aktif
- Verify account access

---

## 💰 Pricing & Limits

### Google Places API

**Free Tier:**
- $200 credit per bulan
- ~11,000 requests/bulan gratis

**Pricing:**
- Places API: $17 per 1000 requests
- Geocoding API: $5 per 1000 requests
- Autocomplete: $2.83 per 1000 requests

**Limits:**
- 100,000 requests per day (default)
- Bisa request increase

### Google Ads API

**Free Tier:**
- Unlimited requests (no charge)
- Hanya untuk keyword research
- Tidak untuk ads management

**Limits:**
- Basic access: 15,000 operations/day
- Standard access: 1,000,000 operations/day
- Perlu approval untuk standard

---

## 📝 Best Practices

### 1. Caching
```php
// Cache ML data 1 hour
Cache::remember("ml_hashtags_{$industry}", 3600, function() {
    return $this->mlService->getTrendingHashtags($industry);
});
```

### 2. Rate Limiting
```php
// Limit API calls
RateLimiter::attempt(
    'google-places:' . $userId,
    $perMinute = 10,
    function() {
        // API call
    }
);
```

### 3. Error Handling
```php
try {
    $data = $googleService->getTrendingPlaces($industry);
} catch (\Exception $e) {
    // Fallback to ML data
    $data = $mlService->getTrendingHashtags($industry);
}
```

### 4. Monitoring
```php
// Log API usage
Log::info('Google API Call', [
    'type' => 'places',
    'industry' => $industry,
    'cost' => 0.017, // $17 per 1000
]);
```

---

## 🎯 Rekomendasi

### Untuk Development (Local)
- ✅ Gunakan ML data (free)
- ✅ Setup Google Places API (free tier cukup)
- ⚠️ Skip Google Ads API (ribet setup)

### Untuk Production (Live)
- ✅ Gunakan ML data sebagai default
- ✅ Enable Google Places API (optional)
- ✅ Setup Google Ads API (jika butuh keyword research)
- ✅ Implement caching & rate limiting
- ✅ Monitor usage & costs

### Untuk UMKM Users
- ✅ Default: ML data (gratis, bagus)
- ✅ Upgrade: Google API (jika butuh data real-time)
- ✅ Transparent pricing
- ✅ User control (enable/disable)

---

## 📞 Support

### Google Cloud Support
- Documentation: https://cloud.google.com/docs
- Support: https://cloud.google.com/support
- Community: https://stackoverflow.com/questions/tagged/google-cloud-platform

### Google Ads API Support
- Documentation: https://developers.google.com/google-ads/api/docs
- Forum: https://groups.google.com/g/adwords-api
- Support: https://support.google.com/google-ads

---

## ✅ Checklist Setup

### Google Places API
- [ ] Buat project di Google Cloud Console
- [ ] Enable Places API
- [ ] Enable Geocoding API
- [ ] Buat API key
- [ ] Restrict API key
- [ ] Setup billing account
- [ ] Test API key
- [ ] Tambahkan ke .env
- [ ] Test di aplikasi

### Google Ads API
- [ ] Buat Google Ads account
- [ ] Apply for API access
- [ ] Tunggu approval
- [ ] Enable Google Ads API di Cloud Console
- [ ] Configure OAuth consent screen
- [ ] Create OAuth client ID
- [ ] Generate refresh token
- [ ] Get developer token
- [ ] Tambahkan ke .env
- [ ] Test di aplikasi

---

**Made with ❤️ for UMKM Indonesia**

*Setup sekali, pakai selamanya!*
