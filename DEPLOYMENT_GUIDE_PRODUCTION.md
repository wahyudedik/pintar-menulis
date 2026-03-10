# 🚀 Deployment Guide - Update Production Server

## ⚠️ PENTING: Projek Sudah Production!

Karena sudah ada user aktif, kita harus update dengan ZERO DOWNTIME dan AMAN.

## 📋 Pre-Deployment Checklist

### 1. Backup Dulu! (WAJIB!)
```bash
# Di VPS Production
cd /path/to/pintar-menulis

# Backup database
php artisan backup:database
# atau manual:
mysqldump -u username -p pintar_menulis > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz .

# Backup .env
cp .env .env.backup
```

### 2. Test di Local Dulu
```bash
# Di local development
php artisan migrate:fresh --seed
php artisan test
php artisan serve
# Test semua fitur baru
```

### 3. Commit & Push ke Git
```bash
git add .
git commit -m "feat: Add hashtag security system and simple mode redesign"
git push origin main
```

## 🔄 Deployment Steps (ZERO DOWNTIME)

### Step 1: Enable Maintenance Mode (Optional)
```bash
# Di VPS
cd /var/www/pintar-menulis  # atau path projek Anda

# Enable maintenance (user lihat halaman maintenance)
php artisan down --refresh=15 --secret="bypass-token"
# User bisa bypass dengan: https://domain.com/bypass-token
```

### Step 2: Pull Latest Code
```bash
# Pull dari Git
git pull origin main

# Atau jika ada conflict:
git stash
git pull origin main
git stash pop
```

### Step 3: Install Dependencies (Jika Ada Baru)
```bash
# Composer
composer install --no-dev --optimize-autoloader

# NPM (jika ada perubahan frontend)
npm install
npm run build
```

### Step 4: Run Migrations (AMAN - Tidak Hapus Data)
```bash
# Cek migration yang akan dijalankan
php artisan migrate:status

# Run migration (AMAN - hanya tambah table/column baru)
php artisan migrate --force

# Migration yang akan dijalankan:
# - 2026_03_11_044903_create_hashtag_blacklist_table
```

### Step 5: Seed Data Baru (Hashtags)
```bash
# Seed trending hashtags (96 hashtags)
php artisan db:seed --class=TrendingHashtagSeeder --force

# Verify
php artisan tinker --execute="echo App\Models\TrendingHashtag::count();"
# Expected: 96
```

### Step 6: Clear Cache
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 7: Optimize
```bash
# Optimize autoloader
composer dump-autoload --optimize

# Optimize Laravel
php artisan optimize
```

### Step 8: Test di Production
```bash
# Test hashtag system
php artisan hashtags:update --platform=instagram

# Check logs
tail -f storage/logs/laravel.log
```

### Step 9: Disable Maintenance Mode
```bash
php artisan up
```

### Step 10: Monitor
```bash
# Monitor logs
tail -f storage/logs/laravel.log

# Monitor errors
tail -f storage/logs/laravel.log | grep ERROR

# Monitor hashtag updates
tail -f storage/logs/hashtags-update.log
```

## 🎯 Quick Deployment (Jika Sudah Yakin)

```bash
#!/bin/bash
# save as: deploy.sh

echo "🚀 Starting deployment..."

# Backup
echo "📦 Creating backup..."
mysqldump -u username -p pintar_menulis > backup_$(date +%Y%m%d_%H%M%S).sql

# Maintenance mode
echo "🔧 Enabling maintenance mode..."
php artisan down --refresh=15

# Pull code
echo "📥 Pulling latest code..."
git pull origin main

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Run migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Seed hashtags
echo "🏷️ Seeding hashtags..."
php artisan db:seed --class=TrendingHashtagSeeder --force

# Clear cache
echo "🧹 Clearing cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
echo "⚡ Rebuilding cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize
echo "⚡ Optimizing..."
php artisan optimize

# Disable maintenance
echo "✅ Disabling maintenance mode..."
php artisan up

echo "🎉 Deployment completed!"
```

## 🔍 Verification Checklist

Setelah deploy, cek:

### 1. Database
```bash
php artisan tinker
>>> App\Models\TrendingHashtag::count()
=> 96
>>> App\Models\HashtagBlacklist::count()
=> 0 (normal, belum ada yang diblacklist)
```

### 2. Simple Mode
- [ ] Buka `/ai-generator`
- [ ] Pilih Mode Simpel
- [ ] Cek dropdown "Mau bikin konten apa?" ada 23 kategori
- [ ] Pilih kategori, cek subcategory muncul
- [ ] Generate caption, cek berhasil

### 3. Hashtag System
- [ ] Enable "Auto Hashtag"
- [ ] Generate caption
- [ ] Cek hashtag muncul di hasil
- [ ] Cek hashtag relevan dengan kategori

### 4. Schedule
```bash
php artisan schedule:list
# Cek ada:
# - hashtags:update (Weekly Sunday 4 AM)
# - hashtags:update --force (Monthly 1st 5 AM)
```

### 5. Logs
```bash
# Cek tidak ada error
tail -n 100 storage/logs/laravel.log | grep ERROR
```

## ⚠️ Troubleshooting

### Problem 1: Migration Error
```bash
# Error: Table already exists
# Solution: Skip migration
php artisan migrate --force --skip-existing
```

### Problem 2: Seeder Error
```bash
# Error: Duplicate entry
# Solution: Truncate first
php artisan tinker
>>> App\Models\TrendingHashtag::truncate();
>>> exit
php artisan db:seed --class=TrendingHashtagSeeder --force
```

### Problem 3: Cache Issue
```bash
# Clear everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Restart PHP-FPM (if using)
sudo systemctl restart php8.2-fpm

# Restart Nginx/Apache
sudo systemctl restart nginx
# or
sudo systemctl restart apache2
```

### Problem 4: Permission Error
```bash
# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Problem 5: Composer Error
```bash
# Update composer
composer self-update

# Clear composer cache
composer clear-cache

# Reinstall
rm -rf vendor
composer install --no-dev --optimize-autoloader
```

## 🔄 Rollback Plan (Jika Ada Masalah)

### Quick Rollback:
```bash
# 1. Enable maintenance
php artisan down

# 2. Restore database
mysql -u username -p pintar_menulis < backup_YYYYMMDD_HHMMSS.sql

# 3. Restore files
tar -xzf backup_files_YYYYMMDD_HHMMSS.tar.gz

# 4. Clear cache
php artisan cache:clear
php artisan config:clear

# 5. Disable maintenance
php artisan up
```

### Git Rollback:
```bash
# Lihat commit history
git log --oneline

# Rollback ke commit sebelumnya
git reset --hard COMMIT_HASH

# Force push (hati-hati!)
git push origin main --force
```

## 📊 Post-Deployment Monitoring

### First 24 Hours:
```bash
# Monitor errors
watch -n 60 'tail -n 50 storage/logs/laravel.log | grep ERROR'

# Monitor hashtag updates
tail -f storage/logs/hashtags-update.log

# Monitor server resources
htop
```

### Check User Activity:
```bash
# Check active users
php artisan tinker
>>> App\Models\User::where('last_login_at', '>=', now()->subHour())->count()

# Check caption generation
>>> App\Models\CaptionHistory::whereDate('created_at', today())->count()
```

## 🎯 Best Practices

### DO ✅:
- ✅ Backup sebelum deploy
- ✅ Test di local dulu
- ✅ Deploy di off-peak hours (malam/dini hari)
- ✅ Monitor logs setelah deploy
- ✅ Siapkan rollback plan
- ✅ Inform user jika ada downtime

### DON'T ❌:
- ❌ Deploy langsung tanpa backup
- ❌ Deploy di peak hours (siang/sore)
- ❌ Skip testing
- ❌ Ignore errors di logs
- ❌ Deploy tanpa rollback plan

## 📞 Emergency Contacts

Jika ada masalah serius:
1. Enable maintenance mode: `php artisan down`
2. Check logs: `tail -f storage/logs/laravel.log`
3. Rollback jika perlu
4. Contact team/developer

## ✅ Deployment Checklist

- [ ] Backup database
- [ ] Backup files
- [ ] Test di local
- [ ] Commit & push to Git
- [ ] Enable maintenance mode (optional)
- [ ] Pull latest code
- [ ] Install dependencies
- [ ] Run migrations
- [ ] Seed hashtags
- [ ] Clear cache
- [ ] Optimize
- [ ] Disable maintenance mode
- [ ] Test all features
- [ ] Monitor logs
- [ ] Verify hashtag system
- [ ] Check schedule
- [ ] Monitor for 24 hours

## 🎉 Success Criteria

Deployment berhasil jika:
- ✅ No errors in logs
- ✅ All features working
- ✅ Hashtag system active (96 hashtags)
- ✅ Simple mode showing 23 categories
- ✅ Schedule configured
- ✅ Users can generate captions
- ✅ No performance degradation

---

**Last Updated**: 2026-03-11
**Version**: 2.0 (Hashtag Security + Simple Mode Redesign)
**Status**: Ready for Production Deployment
