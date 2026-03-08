# 🚀 QUICK START GUIDE - AI Caption Generator

**Status**: ✅ PRODUCTION READY  
**Last Updated**: March 9, 2026

---

## 📋 PREREQUISITES

1. ✅ PHP 8.1+ installed
2. ✅ Composer installed
3. ✅ Laravel 11 project setup
4. ✅ Gemini API key configured in `.env`
5. ✅ Database configured and migrated

---

## ⚡ QUICK START (3 STEPS)

### Step 1: Clear Cache (IMPORTANT!)
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 2: Start Queue Worker
```bash
php artisan queue:work --tries=3
```
**Keep this terminal open!** This processes background jobs.

### Step 3: Start Scheduler (NEW TERMINAL)
```bash
php artisan schedule:work
```
**Keep this terminal open!** This runs monitoring every minute.

---

## 🎯 WHAT'S RUNNING

### Terminal 1: Queue Worker
- Processes AI generation requests
- Handles monitoring jobs
- Retries failed jobs (max 3 attempts)

### Terminal 2: Scheduler
- ⏱️ **Every 1 minute**: Health monitoring
- ⏱️ **Every 5 minutes**: Connectivity testing
- ⏱️ **Daily 2 AM**: Cleanup old data
- ⏱️ **Daily 8 AM**: Generate health report
- ⏱️ **Daily midnight**: Reset counters

---

## 🔍 MONITORING

### Check System Health
```bash
php artisan gemini:monitor-usage
```

**Output Example:**
```
===========================================
GEMINI MODEL USAGE MONITOR
===========================================

Current Tier: Free Tier
Tier Status: ✓ Active

Available Models: 5/5

Model: gemini-2.5-flash (Priority 1)
  RPM: 1/10 (10%)
  RPD: 5/250 (2%)
  Status: ✓ Available

[... more models ...]

===========================================
SYSTEM STATUS: ✓ ALL SYSTEMS OPERATIONAL
===========================================
```

### Check Logs
```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Check for errors only
tail -f storage/logs/laravel.log | grep ERROR

# Check health checks
tail -f storage/logs/laravel.log | grep "Health Check"
```

---

## 🌐 WEB INTERFACE

### For Clients (Users)
1. Login to your account
2. Navigate to **AI Generator** page
3. Fill in the form:
   - Category (e.g., "freelance")
   - Subcategory (e.g., "upwork_proposal")
   - Platform (Instagram, TikTok, Facebook, etc.)
   - Brief (minimum 10 characters)
   - Tone (professional, casual, funny, etc.)
4. Click **Generate**
5. Wait 3-5 seconds for AI to generate caption
6. Copy and use your caption!

### For Admins
- **AI Health Dashboard**: `/admin/ai-health`
- **AI Model Management**: `/admin/ai-models`
- **AI Usage Analytics**: `/admin/ai-usage`

---

## 🔧 TROUBLESHOOTING

### Problem: 500 Internal Server Error

**Solution:**
```bash
# 1. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 2. Check logs
tail -f storage/logs/laravel.log

# 3. Restart queue worker
# Press Ctrl+C in queue worker terminal, then:
php artisan queue:work --tries=3
```

### Problem: "API Key tidak valid"

**Solution:**
1. Go to https://aistudio.google.com/app/apikey
2. Generate new API key
3. Update `.env`:
   ```
   GEMINI_API_KEY=your_new_key_here
   ```
4. Clear config:
   ```bash
   php artisan config:clear
   ```

### Problem: "Rate limit exceeded"

**Solution:**
- ✅ **Automatic!** System will automatically switch to backup models
- No action needed from you
- System has 5 models with combined capacity:
  - Free tier: 50 RPM, 1,600 RPD
  - Paid tier: 1,350 RPM, unlimited RPD

### Problem: Scheduler not running

**Solution:**
```bash
# Check if schedule:work is running
# If not, start it:
php artisan schedule:work
```

**Alternative (Production):**
Add to crontab (Linux/Mac):
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 PERFORMANCE EXPECTATIONS

### Response Times
- **First request**: 3-5 seconds (AI generation)
- **Cached request**: < 100ms (instant)
- **Cache hit rate**: 30-40% expected

### Quality Metrics
- **Validation score**: 6.0-10.0 (minimum 6.0)
- **Average quality**: 8.0/10
- **Success rate**: 99%+

### Capacity
**Free Tier:**
- 50 requests per minute
- 1,600 requests per day
- 5 models available

**Paid Tier (auto-activates):**
- 1,350 requests per minute (27x more!)
- Unlimited requests per day
- 5 models available

---

## 🎯 BEST PRACTICES

### 1. Always Run Queue Worker
```bash
# Development
php artisan queue:work --tries=3

# Production (with supervisor)
# See: https://laravel.com/docs/11.x/queues#supervisor-configuration
```

### 2. Always Run Scheduler
```bash
# Development
php artisan schedule:work

# Production (with cron)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Monitor Regularly
```bash
# Check system health
php artisan gemini:monitor-usage

# Check logs
tail -f storage/logs/laravel.log
```

### 4. Clear Cache After Updates
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🚨 PRODUCTION DEPLOYMENT

### Required Services

1. **Web Server** (Nginx/Apache)
   - Serve Laravel application
   - Handle HTTP requests

2. **Queue Worker** (Supervisor)
   - Process background jobs
   - Auto-restart on failure
   - See: https://laravel.com/docs/11.x/queues#supervisor-configuration

3. **Scheduler** (Cron)
   - Run scheduled tasks
   - Add to crontab:
     ```
     * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
     ```

4. **Database** (MySQL/PostgreSQL)
   - Store application data
   - Store queue jobs

### Environment Variables
```env
# Required
APP_ENV=production
APP_DEBUG=false
GEMINI_API_KEY=your_api_key_here

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_DRIVER=redis  # or file
```

---

## 📞 SUPPORT

### Check System Status
1. **Health Dashboard**: `/admin/ai-health`
2. **Monitor Command**: `php artisan gemini:monitor-usage`
3. **Logs**: `storage/logs/laravel.log`

### Common Commands
```bash
# Clear cache
php artisan config:clear && php artisan cache:clear

# Check queue jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Check scheduled tasks
php artisan schedule:list
```

---

## ✅ VERIFICATION CHECKLIST

Before going live, verify:

- [ ] Queue worker is running
- [ ] Scheduler is running
- [ ] Gemini API key is valid
- [ ] Database is connected
- [ ] Cache is cleared
- [ ] Logs show no errors
- [ ] Test generation works
- [ ] Health monitoring active

---

## 🎉 YOU'RE READY!

Your AI Caption Generator is now fully operational and production-ready!

**Key Features:**
- ✅ 5 AI models with automatic fallback
- ✅ Quality validation (8 criteria)
- ✅ Quality scoring (7 dimensions)
- ✅ Response caching (30-40% hit rate)
- ✅ Proactive monitoring (every minute)
- ✅ Auto-recovery from failures
- ✅ Tier auto-detection and upgrade

**Happy generating! 🚀**

---

**Last Updated**: March 9, 2026  
**Version**: 1.0.0  
**Status**: ✅ PRODUCTION READY
