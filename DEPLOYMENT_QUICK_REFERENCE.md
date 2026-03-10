# 🚀 Quick Deployment Reference Card

## ⚡ QUICK DEPLOY (Copy-Paste Ready)

```bash
# === STEP 1: BACKUP ===
mysqldump -u username -p pintar_menulis > backup_$(date +%Y%m%d_%H%M%S).sql

# === STEP 2: DEPLOY ===
cd /var/www/pintar-menulis
bash deploy.sh

# === STEP 3: VERIFY ===
php artisan tinker --execute="echo 'Hashtags: ' . App\Models\TrendingHashtag::count();"
tail -n 50 storage/logs/laravel.log | grep ERROR
```

---

## 📋 Pre-Deploy Checklist

- [ ] Backup database ✅
- [ ] Edit deploy.sh (DB_USER) ✅
- [ ] Test di local ✅
- [ ] Commit & push to Git ✅
- [ ] Inform users (optional) ✅

---

## 🔧 Manual Deploy Commands

```bash
# 1. Backup
mysqldump -u username -p pintar_menulis > backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Maintenance (optional)
php artisan down --refresh=15

# 3. Pull code
git pull origin main

# 4. Dependencies
composer install --no-dev --optimize-autoloader

# 5. Migrate
php artisan migrate --force

# 6. Seed hashtags
php artisan db:seed --class=TrendingHashtagSeeder --force

# 7. Clear cache
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# 8. Rebuild cache
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 9. Optimize
php artisan optimize

# 10. Up
php artisan up
```

---

## ✅ Quick Verification

```bash
# Check hashtags
php artisan tinker --execute="echo App\Models\TrendingHashtag::count();"
# Expected: 96

# Check errors
tail -n 50 storage/logs/laravel.log | grep ERROR
# Expected: (empty)

# Check schedule
php artisan schedule:list | grep hashtags
# Expected: 2 entries (weekly + monthly)
```

---

## 🚨 Quick Rollback

```bash
php artisan down
mysql -u username -p pintar_menulis < backup_YYYYMMDD_HHMMSS.sql
php artisan cache:clear
php artisan up
```

---

## 📊 Quick Test

```bash
# Test hashtag update
php artisan hashtags:update --platform=instagram

# Monitor logs
tail -f storage/logs/laravel.log
```

---

## 🔍 Quick Troubleshooting

### Migration Error
```bash
php artisan migrate --force --skip-existing
```

### Seeder Error
```bash
php artisan tinker --execute="App\Models\TrendingHashtag::truncate();"
php artisan db:seed --class=TrendingHashtagSeeder --force
```

### Cache Error
```bash
php artisan optimize:clear
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Permission Error
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 📞 Emergency Contacts

**If something goes wrong:**
1. `php artisan down` (maintenance mode)
2. Check logs: `tail -f storage/logs/laravel.log`
3. Rollback if needed
4. Contact developer

---

## ✅ Success Indicators

- ✅ No errors in logs
- ✅ 96 hashtags in database
- ✅ Simple Mode shows 23 categories
- ✅ Schedule configured
- ✅ Users can generate captions

---

**Print this card and keep it handy during deployment!** 📄
