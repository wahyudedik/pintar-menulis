# 📊 Post-Deployment Monitoring Guide

## 🎯 First 24 Hours Monitoring

Setelah deploy, monitor sistem untuk memastikan semuanya berjalan lancar.

---

## ⏰ Monitoring Schedule

### Hour 0-1 (Immediately After Deploy)
```bash
# Check for immediate errors
tail -n 100 storage/logs/laravel.log | grep ERROR

# Verify hashtags loaded
php artisan tinker --execute="echo 'Total: ' . App\Models\TrendingHashtag::count();"

# Test caption generation
# Go to: https://your-domain.com/ai-generator
# Generate 2-3 captions with different categories
```

### Hour 1-6 (First Few Hours)
```bash
# Monitor logs every hour
watch -n 3600 'tail -n 50 storage/logs/laravel.log | grep ERROR'

# Check user activity
php artisan tinker
>>> App\Models\CaptionHistory::whereDate('created_at', today())->count()
```

### Hour 6-24 (Rest of Day)
```bash
# Check logs every 6 hours
tail -n 100 storage/logs/laravel.log | grep ERROR

# Monitor server resources
htop
```

---

## 🔍 What to Monitor

### 1. Error Logs
```bash
# Real-time monitoring
tail -f storage/logs/laravel.log

# Check for specific errors
grep "ERROR" storage/logs/laravel.log | tail -n 20
grep "CRITICAL" storage/logs/laravel.log | tail -n 20
grep "hashtag" storage/logs/laravel.log | tail -n 20
```

### 2. Database Health
```bash
php artisan tinker

# Check hashtags
>>> TrendingHashtag::count()
=> 96

# Check recent captions
>>> CaptionHistory::whereDate('created_at', today())->count()
=> [number]

# Check active users
>>> User::where('last_login_at', '>=', now()->subHour())->count()
=> [number]
```

### 3. Performance Metrics
```bash
# Server resources
htop

# Disk space
df -h

# Memory usage
free -m

# PHP-FPM status (if using)
sudo systemctl status php8.2-fpm

# Nginx/Apache status
sudo systemctl status nginx
# or
sudo systemctl status apache2
```

### 4. Feature Testing
- [ ] Simple Mode works (23 categories)
- [ ] Advanced Mode works
- [ ] Hashtag generation works
- [ ] Caption generation works
- [ ] All platforms selectable
- [ ] All categories selectable

---

## 📈 Key Metrics to Track

### Success Metrics
```bash
# Caption generation success rate
php artisan tinker
>>> $total = CaptionHistory::whereDate('created_at', today())->count();
>>> $success = CaptionHistory::whereDate('created_at', today())->whereNotNull('caption')->count();
>>> echo "Success Rate: " . round(($success / $total) * 100, 2) . "%";
```

### Hashtag Usage
```bash
# Check which hashtags are being used
php artisan tinker
>>> TrendingHashtag::orderBy('usage_count', 'desc')->take(10)->pluck('hashtag', 'usage_count')
```

### User Activity
```bash
# Active users today
php artisan tinker
>>> User::whereDate('last_login_at', today())->count()

# Captions generated today
>>> CaptionHistory::whereDate('created_at', today())->count()
```

---

## 🚨 Red Flags (Immediate Action Required)

### Critical Issues
- ❌ Error rate > 5%
- ❌ No captions generated in last hour
- ❌ Database connection errors
- ❌ Server CPU > 90% for 10+ minutes
- ❌ Disk space < 10%

### Warning Signs
- ⚠️ Error rate 1-5%
- ⚠️ Slow response time (>3 seconds)
- ⚠️ Memory usage > 80%
- ⚠️ Hashtag update failed

---

## 🔧 Common Issues & Quick Fixes

### Issue 1: Hashtags Not Showing
```bash
# Check hashtag count
php artisan tinker --execute="echo TrendingHashtag::count();"

# If 0, reseed
php artisan db:seed --class=TrendingHashtagSeeder --force

# Clear cache
php artisan cache:clear
```

### Issue 2: Simple Mode Not Working
```bash
# Clear view cache
php artisan view:clear

# Check logs
tail -n 50 storage/logs/laravel.log | grep "simple"
```

### Issue 3: Slow Performance
```bash
# Clear all cache
php artisan optimize:clear

# Rebuild cache
php artisan optimize

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Issue 4: Schedule Not Running
```bash
# Check schedule
php artisan schedule:list

# Run manually
php artisan schedule:run

# Check cron
crontab -l
# Should have: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Daily Health Check (After 24 Hours)

```bash
# === DAILY HEALTH CHECK SCRIPT ===

echo "=== Pintar Menulis Health Check ==="
echo ""

# 1. Check errors
echo "1. Checking errors..."
ERROR_COUNT=$(grep -c "ERROR" storage/logs/laravel.log | tail -n 100)
echo "   Errors in last 100 lines: $ERROR_COUNT"

# 2. Check hashtags
echo "2. Checking hashtags..."
php artisan tinker --execute="echo '   Total hashtags: ' . App\Models\TrendingHashtag::count();"

# 3. Check captions today
echo "3. Checking captions today..."
php artisan tinker --execute="echo '   Captions today: ' . App\Models\CaptionHistory::whereDate('created_at', today())->count();"

# 4. Check disk space
echo "4. Checking disk space..."
df -h | grep -E '^/dev/' | awk '{print "   " $1 ": " $5 " used"}'

# 5. Check memory
echo "5. Checking memory..."
free -m | grep Mem | awk '{print "   Memory: " $3 "MB / " $2 "MB (" int($3/$2*100) "%)"}'

echo ""
echo "=== Health Check Complete ==="
```

Save as `health-check.sh` and run daily:
```bash
bash health-check.sh
```

---

## 📅 Weekly Maintenance

### Every Sunday (After Hashtag Update)
```bash
# Check hashtag update log
tail -n 50 storage/logs/hashtags-update.log

# Verify hashtags updated
php artisan tinker
>>> TrendingHashtag::orderBy('last_updated', 'desc')->first()->last_updated
=> "2026-03-16 04:00:00"  # Should be recent Sunday 4 AM
```

### Every Monday (Weekly Review)
```bash
# Review last week's stats
php artisan tinker
>>> CaptionHistory::whereBetween('created_at', [now()->subWeek(), now()])->count()
>>> User::whereBetween('last_login_at', [now()->subWeek(), now()])->count()
```

---

## 📈 Monthly Maintenance

### First of Month (After Force Update)
```bash
# Check monthly update log
tail -n 50 storage/logs/hashtags-monthly.log

# Review monthly stats
php artisan tinker
>>> CaptionHistory::whereBetween('created_at', [now()->subMonth(), now()])->count()
```

### Mid-Month Review
```bash
# Check for any blacklisted hashtags
php artisan tinker
>>> App\Models\HashtagBlacklist::where('is_active', true)->count()

# Review trending hashtags performance
>>> TrendingHashtag::orderBy('trend_score', 'desc')->take(10)->pluck('hashtag', 'trend_score')
```

---

## 🎯 Success Indicators (After 7 Days)

After 1 week, system is stable if:
- ✅ No critical errors
- ✅ Caption generation working consistently
- ✅ Hashtag updates running on schedule
- ✅ User activity normal or increased
- ✅ No performance degradation
- ✅ Server resources stable

---

## 📞 When to Contact Developer

Contact developer if:
- 🚨 Critical errors persist after troubleshooting
- 🚨 Data loss or corruption
- 🚨 Security issues detected
- 🚨 Performance degradation >50%
- 🚨 Schedule not running after fixes

---

## 📝 Monitoring Checklist

### Daily (First Week)
- [ ] Check error logs
- [ ] Verify hashtag count (96)
- [ ] Test caption generation
- [ ] Check server resources
- [ ] Review user activity

### Weekly (Ongoing)
- [ ] Review hashtag update logs
- [ ] Check weekly stats
- [ ] Test all features
- [ ] Review performance metrics

### Monthly (Ongoing)
- [ ] Review monthly stats
- [ ] Check for blacklisted hashtags
- [ ] Review trending hashtags
- [ ] Plan improvements

---

**Remember**: First 24 hours are critical. Monitor closely! 👀

**Status**: Ready for monitoring
**Date**: 2026-03-11
