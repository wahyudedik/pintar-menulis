# AI Generator Error 500 - Quick Fix

## 🎯 Problem
```
POST /api/ai/generate 500 (Internal Server Error)
```

## ⚡ Quick Solution (5 Minutes)

### 1️⃣ Generate API Key
Buka: https://aistudio.google.com/app/apikey
- Login dengan Google
- Click "Create API Key"
- Copy key (format: `AIzaSy...`)

### 2️⃣ Update .env di VPS
```bash
cd /www/wwwroot/pintar-menulis
nano .env
```

Update line:
```env
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

Save: `Ctrl+X`, `Y`, `Enter`

### 3️⃣ Clear Cache (WAJIB!)
```bash
php artisan config:clear
php artisan config:cache
```

### 4️⃣ Test
```bash
php artisan tinker
```

```php
config('services.gemini.api_key')
// Should return your API key

$service = app(\App\Services\GeminiService::class);
$result = $service->generateCopywriting([
    'category' => 'social_media',
    'subcategory' => 'caption',
    'brief' => 'Test produk',
    'tone' => 'casual',
    'platform' => 'instagram'
]);
echo $result;
// Should return generated caption
```

Exit: `exit`

### 5️⃣ Test di Browser
Buka: https://noteds.com/ai-generator
- Isi form
- Click "Sedang bikin..."
- **Should work!** ✅

---

## 🔍 Diagnostic Tool

Upload & run `check-production.sh`:
```bash
cd /www/wwwroot/pintar-menulis
chmod +x check-production.sh
./check-production.sh
```

Akan check semua dan kasih tau masalahnya.

---

## 🆘 Still Error?

Check logs:
```bash
tail -50 storage/logs/laravel.log
```

Look for:
- "API Key not configured" → Add to .env
- "API key not valid" → Generate new key
- "Model not found" → Check key validity

---

## ✅ Success = No Error + 5 Variations Generated

**Full Guide:** `FIX_AI_GENERATOR_ERROR.md`
