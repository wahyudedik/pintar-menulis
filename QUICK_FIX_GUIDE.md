# Quick Fix Guide - AI Generator Error 500

## 🚨 Error yang Terjadi

```
POST /api/ai/generate 500 (Internal Server Error)
Server returned non-JSON response
```

## 🔍 Root Cause

Error 500 pada AI Generator disebabkan oleh salah satu dari:
1. ❌ **GEMINI_API_KEY tidak dikonfigurasi** (paling sering!)
2. ❌ **GEMINI_API_KEY tidak valid/expired**
3. ❌ **Config cache belum di-clear setelah update .env**
4. ❌ **Koneksi internet VPS bermasalah**

---

## ✅ SOLUTION - Step by Step

### Step 1: Upload & Run Health Check Script

**Via aaPanel File Manager:**
1. Upload file `check-production.sh` ke `/www/wwwroot/pintar-menulis/`
2. Klik kanan file → **Permissions** → Set `755`

**Via Terminal:**
```bash
cd /www/wwwroot/pintar-menulis
chmod +x check-production.sh
./check-production.sh
```

Script ini akan check semua yang perlu dicek dan kasih tau masalahnya.

---

### Step 2: Fix Gemini API Key (MOST IMPORTANT!)

#### A. Generate New API Key

1. **Buka:** https://aistudio.google.com/app/apikey
2. **Login** dengan Google Account
3. **Click:** "Create API Key"
4. **Select:** "Create API key in new project" (atau pilih project existing)
5. **Copy** API key yang muncul (format: `AIzaSy...`)

⚠️ **IMPORTANT:** Simpan API key ini dengan aman!

#### B. Update .env File

**Via aaPanel File Manager:**
1. Klik **"Files"** di sidebar
2. Navigate ke `/www/wwwroot/pintar-menulis`
3. Klik kanan `.env` → **"Edit"**
4. Cari line `GEMINI_API_KEY=`
5. Paste API key baru:
   ```env
   GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   ```
6. **Save** (Ctrl+S)

**Via Terminal:**
```bash
cd /www/wwwroot/pintar-menulis
nano .env
```

Update line:
```env
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

Save: `Ctrl+X`, `Y`, `Enter`

---

### Step 3: Clear Config Cache

**CRITICAL:** Setelah update .env, WAJIB clear cache!

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
1. Klik **"Files"** → Navigate ke `/www/wwwroot/pintar-menulis`
2. Klik **"Terminal"** icon di toolbar
3. Paste commands di atas

---

### Step 4: Test API Key

```bash
cd /www/wwwroot/pintar-menulis
php artisan tinker
```

Dalam tinker, test:
```php
// Check if key is loaded
config('services.gemini.api_key')
// Should return: "AIzaSy..."

// Test actual API call
$service = app(\App\Services\GeminiService::class);
$result = $service->generateCopywriting([
    'category' => 'social_media',
    'subcategory' => 'caption',
    'brief' => 'Test produk makanan enak dan murah',
    'tone' => 'casual',
    'platform' => 'instagram'
]);
echo $result;
// Should return generated caption
```

Jika berhasil, akan muncul caption yang di-generate!

Exit tinker: `exit` atau `Ctrl+C`

---

### Step 5: Test di Browser

1. Buka: `https://your-domain.com/ai-generator`
2. Isi form:
   - **Jenis usaha:** Jasa Digital
   - **Apa yang mau dijual:** jasa pembuatan website
   - **Berapa harganya:** mulai 100rb
   - **Mau dijual ke siapa:** Umum (Semua kalangan)
   - **Tujuannya apa:** Biar Viral & Banyak Share
   - **Mau posting di mana:** Instagram
3. Click **"Sedang bikin..."**
4. **Should work now!** ✅

---

## 🔧 Alternative Fixes

### If Still Error After API Key Fix:

#### 1. Check Internet Connection
```bash
# Test connection to Google API
curl -I https://generativelanguage.googleapis.com

# Should return: HTTP/2 200
```

#### 2. Check PHP Extensions
```bash
php -m | grep -E 'curl|json|mbstring'

# Should show:
# curl
# json
# mbstring
```

#### 3. Check Logs
```bash
tail -50 /www/wwwroot/pintar-menulis/storage/logs/laravel.log
```

Look for error messages like:
- "API Key not configured"
- "API key not valid"
- "Connection timeout"
- "Model not found"

#### 4. Restart PHP-FPM

**Via aaPanel:**
1. App Store → PHP 8.2 → **Restart**

**Via Terminal:**
```bash
systemctl restart php8.2-fpm
```

#### 5. Check File Permissions
```bash
cd /www/wwwroot/pintar-menulis
chown -R www:www .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

---

## 📊 Verify Everything Works

### Checklist:
- [ ] API key generated from Google AI Studio
- [ ] API key added to .env file
- [ ] Config cache cleared
- [ ] Test in tinker successful
- [ ] Test in browser successful
- [ ] No errors in laravel.log

### Expected Result:
When you submit the form, you should see:
```
✅ Generated 5 variations of caption
✅ Each with hashtags
✅ Indonesian language
✅ Casual tone
✅ Instagram optimized
```

---

## 🆘 Still Not Working?

### Check These:

1. **API Key Format:**
   - Should start with `AIzaSy`
   - Length: ~39 characters
   - No spaces or quotes

2. **Config Cache:**
   ```bash
   # Verify config is loaded
   php artisan tinker
   >>> config('services.gemini.api_key')
   ```
   Should return your API key, not null!

3. **Gemini API Status:**
   - Check: https://status.cloud.google.com/
   - Make sure Gemini API is operational

4. **VPS Firewall:**
   - Make sure outbound HTTPS (port 443) is allowed
   - Check with hosting provider

5. **View Full Error:**
   ```bash
   tail -100 storage/logs/laravel.log | grep -A 10 "Gemini"
   ```

---

## 💡 Pro Tips

### 1. Test API Key Before Using
Before adding to .env, test with curl:
```bash
curl -X POST \
  "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"contents":[{"parts":[{"text":"Hello"}]}]}'
```

Should return JSON response, not error.

### 2. Monitor API Usage
- Check usage: https://aistudio.google.com/app/apikey
- Free tier: 15 requests/minute
- If quota exceeded, wait or upgrade

### 3. Keep API Key Secure
- Never commit .env to git
- Never share API key publicly
- Rotate key if compromised

### 4. Enable Logging
In .env:
```env
LOG_LEVEL=debug
```

Then check logs for detailed error info.

---

## 📝 Common Error Messages & Solutions

| Error Message | Solution |
|---------------|----------|
| "API Key not configured" | Add GEMINI_API_KEY to .env |
| "API key not valid" | Generate new key from Google AI Studio |
| "Model not found" | API key might be invalid, generate new one |
| "Quota exceeded" | Wait for quota reset or upgrade plan |
| "Connection timeout" | Check VPS internet connection |
| "Non-JSON response" | Usually means API key issue |

---

## ✅ Success Indicators

You'll know it's working when:
1. ✅ No errors in browser console
2. ✅ "Sedang bikin..." shows loading animation
3. ✅ Results appear in ~5-10 seconds
4. ✅ 5 variations of caption generated
5. ✅ Hashtags included
6. ✅ Indonesian language used

---

## 🎯 Final Checklist

Before declaring victory:
- [ ] Run `./check-production.sh` - all green checks
- [ ] Test AI Generator in browser - works
- [ ] Check laravel.log - no errors
- [ ] Test with different categories - all work
- [ ] Test with different platforms - all work

---

**Last Updated:** March 2026
**Status:** Tested & Working ✅

**Need Help?**
- Check logs: `tail -f storage/logs/laravel.log`
- Run health check: `./check-production.sh`
- Review: `TROUBLESHOOTING_PRODUCTION.md`
