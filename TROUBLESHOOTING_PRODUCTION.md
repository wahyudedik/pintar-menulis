# Troubleshooting Production Issues

## 🔧 Common Production Errors & Solutions

### 1. Tailwind CDN Warning

**Error:**
```
cdn.tailwindcss.com should not be used in production
```

**Cause:** Tailwind CDN masih digunakan di production (tidak boleh!)

**Solution:**

✅ **SUDAH DIPERBAIKI** - Semua view sudah menggunakan compiled CSS via Vite.

**Verify Fix:**
1. Check bahwa tidak ada lagi `cdn.tailwindcss.com` di views
2. Pastikan assets sudah di-build: `npm run build`
3. Pastikan `public/build/manifest.json` exists

**Di VPS (aaPanel):**
```bash
cd /www/wwwroot/pintar-menulis
npm run build
```

---

### 2. AI Generator Error 500

**Error:**
```
Failed to load resource: the server responded with a status of 500
Server returned non-JSON response
```

**Possible Causes:**
1. ❌ Gemini API Key tidak dikonfigurasi
2. ❌ Gemini API Key tidak valid/expired
3. ❌ Koneksi internet VPS bermasalah
4. ❌ Quota API habis

**Solution:**

#### A. Check API Key Configuration

**Via aaPanel:**
1. Files → `/www/wwwroot/pintar-menulis/.env`
2. Check line:
   ```env
   GEMINI_API_KEY=your_actual_api_key_here
   ```

**Via Terminal:**
```bash
cd /www/wwwroot/pintar-menulis
grep GEMINI_API_KEY .env
```

#### B. Generate New API Key

1. Buka: https://aistudio.google.com/app/apikey
2. Login dengan Google Account
3. Click "Create API Key"
4. Copy API key yang baru
5. Update `.env`:
   ```env
   GEMINI_API_KEY=AIzaSy...your_new_key_here
   ```

#### C. Clear Config Cache

```bash
cd /www/wwwroot/pintar-menulis
php artisan config:clear
php artisan config:cache
```

#### D. Test API Key

```bash
cd /www/wwwroot/pintar-menulis
php artisan tinker
```

Dalam tinker:
```php
>>> config('services.gemini.api_key')
// Should return your API key

>>> $service = app(\App\Services\GeminiService::class);
>>> $result = $service->generateCopywriting([
    'category' => 'social_media',
    'subcategory' => 'caption',
    'brief' => 'Test brief untuk produk makanan',
    'tone' => 'casual',
    'platform' => 'instagram'
]);
>>> echo $result;
// Should return generated content
```

#### E. Check Logs

**Via aaPanel:**
1. Files → `/www/wwwroot/pintar-menulis/storage/logs/laravel.log`
2. Look for "Gemini API" errors

**Via Terminal:**
```bash
tail -f /www/wwwroot/pintar-menulis/storage/logs/laravel.log
```

**Common Error Messages:**

**"API Key not configured"**
- Solution: Add GEMINI_API_KEY to .env

**"API key not valid"**
- Solution: Generate new API key from Google AI Studio

**"Model not found" or "not supported"**
- Solution: API key might be invalid or expired, generate new one

**"Quota exceeded"**
- Solution: Wait for quota reset or upgrade API plan

**"Connection timeout"**
- Solution: Check VPS internet connection

#### F. Verify Internet Connection

```bash
# Test connection to Google API
curl -I https://generativelanguage.googleapis.com

# Should return: HTTP/2 200
```

---

### 3. Assets Not Loading (CSS/JS 404)

**Error:**
```
GET /build/assets/app-xxx.css 404 Not Found
GET /build/assets/app-xxx.js 404 Not Found
```

**Cause:** Assets belum di-build atau build folder tidak ada

**Solution:**

```bash
cd /www/wwwroot/pintar-menulis

# Build assets
npm install
npm run build

# Verify build folder exists
ls -la public/build/

# Should see:
# - manifest.json
# - assets/ folder with CSS & JS files
```

---

### 4. Storage Link Missing

**Error:**
```
Uploaded images not showing
404 on /storage/... URLs
```

**Cause:** Storage link belum dibuat

**Solution:**

```bash
cd /www/wwwroot/pintar-menulis
php artisan storage:link
```

**Verify:**
```bash
ls -la public/storage
# Should be a symlink to ../storage/app/public
```

---

### 5. Permission Denied Errors

**Error:**
```
file_put_contents(...): failed to open stream: Permission denied
```

**Cause:** File permissions salah

**Solution:**

```bash
cd /www/wwwroot/pintar-menulis

# Fix ownership
chown -R www:www /www/wwwroot/pintar-menulis

# Fix permissions
chmod -R 755 /www/wwwroot/pintar-menulis
chmod -R 775 /www/wwwroot/pintar-menulis/storage
chmod -R 775 /www/wwwroot/pintar-menulis/bootstrap/cache
```

---

### 6. Database Connection Error

**Error:**
```
SQLSTATE[HY000] [1045] Access denied for user
SQLSTATE[HY000] [2002] Connection refused
```

**Cause:** Database credentials salah atau MySQL tidak running

**Solution:**

#### A. Check MySQL Status

**Via aaPanel:**
1. App Store → MySQL → Check status (should be "Running")

**Via Terminal:**
```bash
systemctl status mysql
```

#### B. Verify Database Credentials

**Via aaPanel:**
1. Database → Check database name, username, password
2. Compare with `.env` file

**Via Terminal:**
```bash
cd /www/wwwroot/pintar-menulis
grep DB_ .env

# Should match with database info in aaPanel
```

#### C. Test Connection

```bash
cd /www/wwwroot/pintar-menulis
php artisan tinker
```

```php
>>> DB::connection()->getPdo();
// Should return PDO object, not error
```

---

### 7. Queue Workers Not Processing

**Error:**
- Jobs stuck in queue
- Emails not sending
- Background tasks not running

**Cause:** Supervisor workers not running

**Solution:**

**Via aaPanel:**
1. Supervisor → Check worker status
2. If stopped, click "Start"
3. If error, click "Restart"

**Via Terminal:**
```bash
# Check status
supervisorctl status

# Restart workers
supervisorctl restart pintar-menulis-worker:*

# View worker logs
tail -f /www/wwwroot/pintar-menulis/storage/logs/worker.log
```

---

### 8. SSL Certificate Error

**Error:**
```
NET::ERR_CERT_AUTHORITY_INVALID
Your connection is not private
```

**Cause:** SSL certificate expired atau tidak valid

**Solution:**

**Via aaPanel:**
1. Website → Settings → SSL
2. Check certificate expiry date
3. If expired, click "Renew"
4. If Let's Encrypt, click "Apply" again

**Via Terminal:**
```bash
# Check certificate expiry
openssl x509 -in /etc/letsencrypt/live/your-domain.com/fullchain.pem -noout -dates

# Renew Let's Encrypt
certbot renew
```

---

### 9. 500 Internal Server Error (General)

**Error:**
```
500 Internal Server Error
Whoops, something went wrong
```

**Cause:** Various reasons

**Solution:**

#### Step 1: Check Laravel Logs

**Via aaPanel:**
1. Files → `/www/wwwroot/pintar-menulis/storage/logs/laravel.log`
2. Look for latest error

**Via Terminal:**
```bash
tail -50 /www/wwwroot/pintar-menulis/storage/logs/laravel.log
```

#### Step 2: Check Nginx Error Log

**Via aaPanel:**
1. Website → Settings → Log → Error Log

**Via Terminal:**
```bash
tail -50 /var/log/nginx/pintar-menulis-error.log
```

#### Step 3: Check PHP Error Log

**Via aaPanel:**
1. App Store → PHP 8.2 → Settings → Error Log

#### Step 4: Common Fixes

```bash
cd /www/wwwroot/pintar-menulis

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
chown -R www:www /www/wwwroot/pintar-menulis
chmod -R 775 /www/wwwroot/pintar-menulis/storage
chmod -R 775 /www/wwwroot/pintar-menulis/bootstrap/cache
```

---

### 10. High Memory Usage / Server Slow

**Symptoms:**
- Website loading very slow
- Server unresponsive
- Out of memory errors

**Solution:**

#### A. Check Resource Usage

**Via aaPanel Dashboard:**
- Check CPU, Memory, Disk usage

**Via Terminal:**
```bash
# Check memory
free -m

# Check CPU
top

# Check disk
df -h

# Check processes
ps aux --sort=-%mem | head -10
```

#### B. Restart Services

**Via aaPanel:**
1. App Store → PHP 8.2 → Restart
2. App Store → Nginx → Restart
3. App Store → MySQL → Restart

**Via Terminal:**
```bash
systemctl restart php8.2-fpm
systemctl restart nginx
systemctl restart mysql
```

#### C. Clear Old Logs

```bash
cd /www/wwwroot/pintar-menulis/storage/logs
rm -f laravel-*.log

# Keep only latest log
ls -t | tail -n +2 | xargs rm -f
```

#### D. Optimize Database

**Via aaPanel:**
1. Database → phpMyAdmin
2. Select database
3. Check all tables
4. "With selected" → "Optimize table"

---

## 🔍 Debugging Checklist

When encountering errors, check in this order:

1. **Laravel Logs**
   - `/www/wwwroot/pintar-menulis/storage/logs/laravel.log`

2. **Nginx Error Log**
   - Website → Settings → Log → Error Log

3. **PHP Error Log**
   - App Store → PHP 8.2 → Error Log

4. **Environment Variables**
   - Check `.env` file is configured correctly

5. **Permissions**
   - `storage/` and `bootstrap/cache/` must be writable

6. **Services Status**
   - PHP-FPM, Nginx, MySQL, Redis, Supervisor all running

7. **Disk Space**
   - Check if disk is full: `df -h`

8. **Memory**
   - Check if out of memory: `free -m`

---

## 📞 Getting Help

### Check Logs First!

Always check logs before asking for help:

```bash
# Laravel log
tail -100 /www/wwwroot/pintar-menulis/storage/logs/laravel.log

# Nginx error log
tail -100 /var/log/nginx/pintar-menulis-error.log

# PHP error log
tail -100 /www/server/php/82/var/log/php-fpm.log
```

### Provide Information

When asking for help, provide:
1. Error message (exact text)
2. Laravel log excerpt
3. Nginx error log excerpt
4. What you were doing when error occurred
5. What you've tried to fix it

---

## 🎯 Quick Fixes Summary

| Error | Quick Fix |
|-------|-----------|
| Tailwind CDN Warning | `npm run build` |
| AI Generator 500 | Check GEMINI_API_KEY in .env |
| Assets 404 | `npm run build` |
| Storage 404 | `php artisan storage:link` |
| Permission Denied | `chown -R www:www` + `chmod -R 775 storage` |
| Database Error | Check .env DB credentials |
| Queue Not Working | Restart Supervisor workers |
| SSL Error | Renew certificate in aaPanel |
| 500 Error | Clear caches + check logs |
| Slow Server | Restart services + clear logs |

---

**Last Updated**: March 2026
**Version**: 1.0.0
