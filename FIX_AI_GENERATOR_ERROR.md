# Fix AI Generator Error 500 - Production

## 🚨 Problem

AI Generator mengembalikan error 500 di production:
```
POST /api/ai/generate 500 (Internal Server Error)
Server returned non-JSON response
```

## 🔍 Root Cause Analysis

Berdasarkan code review:

1. **GeminiService.php** (line 15-17):
   ```php
   $this->apiKey = config('services.gemini.api_key');
   $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
   ```

2. **config/services.php** (line 42-44):
   ```php
   'gemini' => [
       'api_key' => env('GEMINI_API_KEY'),
   ],
   ```

3. **Validation** (GeminiService.php line 26-29):
   ```php
   if (empty($this->apiKey)) {
       Log::error('Gemini API Key not configured');
       throw new \Exception('API Key tidak dikonfigurasi. Hubungi administrator.');
   }
   ```

**Kesimpulan:** Error 500 terjadi karena `GEMINI_API_KEY` tidak dikonfigurasi atau tidak valid di production `.env` file.

## ✅ Solution - Step by Step

### Step 1: Upload Health Check Script

Upload file `check-production.sh` ke VPS dan jalankan untuk diagnosa lengkap:

```bash
cd /www/wwwroot/pintar-menulis
chmod +x check-production.sh
./check-production.sh
```

Script ini akan check:
- ✅ PHP version
- ✅ .env configuration
- ✅ GEMINI_API_KEY status
- ✅ Database connection
- ✅ File permissions
- ✅ Assets build
- ✅ Gemini API connectivity
- ✅ Recent errors in logs

### Step 2: Generate New Gemini API Key

1. **Buka:** https://aistudio.google.com/app/apikey
2. **Login** dengan Google Account
3. **Click:** "Create API Key"
4. **Select:** "Create API key in new project" (atau pilih existing project)
5. **Copy** API key (format: `AIzaSy...` dengan panjang ~39 karakter)

⚠️ **PENTING:** Simpan API key dengan aman!

### Step 3: Update .env File di VPS

**Via aaPanel File Manager:**
1. Klik **"Files"** di sidebar
2. Navigate ke `/www/wwwroot/pintar-menulis`
3. Klik kanan `.env` → **"Edit"**
4. Cari line `GEMINI_API_KEY=`
5. Update dengan API key baru:
   ```env
   GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   ```
6. **Save** (Ctrl+S)

**Via Terminal SSH:**
```bash
cd /www/wwwroot/pintar-menulis
nano .env
```

Update line:
```env
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

Save: `Ctrl+X`, `Y`, `Enter`

### Step 4: Clear Config Cache (CRITICAL!)

Setelah update .env, **WAJIB** clear cache agar perubahan diterapkan:

```bash
cd /www/wwwroot/pintar-menulis

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild config cache
php artisan config:cache
```

**Via aaPanel Terminal:**
1. Files → Navigate ke `/www/wwwroot/pintar-menulis`
2. Klik icon **"Terminal"** di toolbar
3. Paste commands di atas

### Step 5: Test API Key

**Test via Tinker:**
```bash
cd /www/wwwroot/pintar-menulis
php artisan tinker
```

Dalam tinker, jalankan:
```php
// 1. Check if API key is loaded
config('services.gemini.api_key')
// Expected: "AIzaSy..." (your API key)

// 2. Test actual API call
$service = app(\App\Services\GeminiService::class);
$result = $service->generateCopywriting([
    'category' => 'social_media',
    'subcategory' => 'caption',
    'brief' => 'Test produk makanan enak dan murah',
    'tone' => 'casual',
    'platform' => 'instagram'
]);
echo $result;
// Expected: Generated caption text
```

Jika berhasil, akan muncul caption yang di-generate!

Exit tinker: `exit` atau `Ctrl+C`

### Step 6: Test di Browser

1. Buka: `https://noteds.com/ai-generator`
2. Isi form dengan data test:
   - **Jenis usaha:** Jasa Digital
   - **Apa yang mau dijual:** jasa pembuatan website
   - **Berapa harganya:** mulai 100rb
   - **Mau dijual ke siapa:** Umum (Semua kalangan)
   - **Tujuannya apa:** Biar Viral & Banyak Share
   - **Mau posting di mana:** Instagram
3. Click **"Sedang bikin..."**
4. **Should work now!** ✅

## 🔧 Troubleshooting

### If Still Getting Error 500:

#### 1. Check Laravel Logs
```bash
tail -50 /www/wwwroot/pintar-menulis/storage/logs/laravel.log
```

Look for:
- "Gemini API Key not configured"
- "API key not valid"
- "Model not found"
- Any other error messages

#### 2. Verify API Key Format
- Must start with `AIzaSy`
- Length: ~39 characters
- No spaces, no quotes in .env file
- Example: `GEMINI_API_KEY=AIzaSyCTO4GttKIPOhnZ7m_3vO5UbnVoClnCHT8`

#### 3. Test API Key with cURL
```bash
curl -X POST \
  "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"contents":[{"parts":[{"text":"Hello"}]}]}'
```

Should return JSON response, not error.

#### 4. Check Internet Connection
```bash
# Test connection to Google API
curl -I https://generativelanguage.googleapis.com

# Expected: HTTP/2 200
```

#### 5. Check PHP Extensions
```bash
php -m | grep -E 'curl|json|mbstring'

# Should show:
# curl
# json
# mbstring
```

#### 6. Restart PHP-FPM

**Via aaPanel:**
1. App Store → PHP 8.2 → **Restart**

**Via Terminal:**
```bash
systemctl restart php8.2-fpm
```

#### 7. Check File Permissions
```bash
cd /www/wwwroot/pintar-menulis
chown -R www:www .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

## 📊 Verification Checklist

- [ ] API key generated from Google AI Studio
- [ ] API key added to .env file (correct format)
- [ ] Config cache cleared (`php artisan config:clear`)
- [ ] Config cache rebuilt (`php artisan config:cache`)
- [ ] Test in tinker successful (returns API key)
- [ ] Test API call in tinker successful (returns caption)
- [ ] Test in browser successful (generates 5 variations)
- [ ] No errors in laravel.log

## 🎯 Expected Result

When working correctly:
1. ✅ Form submission shows loading animation
2. ✅ Results appear in ~5-10 seconds
3. ✅ 5 variations of caption generated
4. ✅ Each variation includes hashtags
5. ✅ Content in Indonesian language
6. ✅ Tone matches selection (casual/formal/etc)
7. ✅ Platform optimized (Instagram/Facebook/etc)

## 📝 Common Error Messages

| Error Message | Cause | Solution |
|---------------|-------|----------|
| "API Key not configured" | GEMINI_API_KEY not in .env | Add to .env file |
| "API key not valid" | Invalid/expired key | Generate new key |
| "Model not found" | Wrong model or invalid key | Check model name & key |
| "Quota exceeded" | API limit reached | Wait or upgrade plan |
| "Connection timeout" | Network issue | Check VPS internet |
| "Non-JSON response" | Server error (usually key issue) | Check key & logs |

## 🆘 Still Not Working?

### Run Full Diagnostic:
```bash
cd /www/wwwroot/pintar-menulis
./check-production.sh
```

### Check All Logs:
```bash
# Laravel log
tail -100 storage/logs/laravel.log

# Nginx error log
tail -100 /var/log/nginx/pintar-menulis-error.log

# PHP-FPM log
tail -100 /www/server/php/82/var/log/php-fpm.log
```

### Enable Debug Mode (Temporarily):
In `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Then try again and check detailed error message.

**IMPORTANT:** Set back to `false` after debugging!

## 💡 Pro Tips

### 1. Monitor API Usage
- Check usage: https://aistudio.google.com/app/apikey
- Free tier: 15 requests/minute, 1500 requests/day
- If quota exceeded, wait or upgrade

### 2. Keep API Key Secure
- Never commit .env to git
- Never share API key publicly
- Rotate key if compromised

### 3. Test Before Deploy
Always test API key locally before deploying:
```bash
# Local test
php artisan tinker
>>> config('services.gemini.api_key')
```

### 4. Use Health Check Script
Run `check-production.sh` regularly to catch issues early.

## 📚 Related Documentation

- `check-production.sh` - Automated health check script
- `QUICK_FIX_GUIDE.md` - Quick reference guide
- `TROUBLESHOOTING_PRODUCTION.md` - Comprehensive troubleshooting
- `README.production.aapanel.md` - Full deployment guide

## ✅ Success Indicators

You'll know it's fixed when:
1. ✅ `./check-production.sh` shows all green checks
2. ✅ Tinker test returns API key
3. ✅ Tinker API call returns generated content
4. ✅ Browser test generates 5 variations
5. ✅ No errors in browser console
6. ✅ No errors in laravel.log

---

**Status:** Ready to Deploy ✅
**Last Updated:** March 6, 2026
**Tested:** Yes

**Quick Command Reference:**
```bash
# 1. Run health check
./check-production.sh

# 2. Clear cache
php artisan config:clear && php artisan config:cache

# 3. Test API
php artisan tinker
>>> config('services.gemini.api_key')

# 4. Check logs
tail -f storage/logs/laravel.log
```
