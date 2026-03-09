# 🚀 Deployment Update Guide - Google OAuth

## 📦 Composer Update di VPS Production

Karena kita baru install package `laravel/socialite`, kamu perlu update composer di VPS.

---

## 🔄 Step-by-Step Deployment

### 1. Backup Database (PENTING!)
```bash
# SSH ke VPS
ssh user@your-vps-ip

# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Atau jika pakai PostgreSQL
pg_dump database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Pull Latest Code dari Git
```bash
# Masuk ke folder project
cd /var/www/pintar-menulis  # sesuaikan path kamu

# Pull latest code
git pull origin main  # atau branch production kamu
```

### 3. Install/Update Composer Dependencies
```bash
# Install dependencies baru (laravel/socialite)
composer install --optimize-autoloader --no-dev

# Atau jika sudah ada composer.lock
composer update --optimize-autoloader --no-dev
```

**Flags explanation:**
- `--optimize-autoloader`: Optimize autoloader untuk production
- `--no-dev`: Skip dev dependencies (phpunit, dll)

### 4. Run Database Migration
```bash
# Run migration untuk add google_id, avatar, provider
php artisan migrate --force

# Flag --force diperlukan di production
```

### 5. Update .env Production
```bash
# Edit .env
nano .env  # atau vim .env

# Tambahkan:
GOOGLE_CLIENT_ID=your-production-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-production-client-secret
GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback
```

### 6. Clear All Cache
```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Set Permissions (jika perlu)
```bash
# Set ownership
sudo chown -R www-data:www-data storage bootstrap/cache

# Set permissions
sudo chmod -R 775 storage bootstrap/cache
```

### 8. Restart Services
```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm  # sesuaikan versi PHP

# Restart Nginx
sudo systemctl restart nginx

# Atau jika pakai Apache
sudo systemctl restart apache2

# Restart Queue Worker (jika ada)
sudo supervisorctl restart laravel-worker:*
```

---

## 🔧 Google Cloud Console - Production Setup

### 1. Update Authorized Redirect URIs

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih project "Pintar Menulis"
3. Sidebar → "APIs & Services" → "Credentials"
4. Klik OAuth 2.0 Client ID yang sudah dibuat
5. Tambahkan production URL ke "Authorized redirect URIs":
   ```
   https://your-domain.com/auth/google/callback
   https://www.your-domain.com/auth/google/callback
   ```
6. Klik "Save"

### 2. Publish OAuth Consent Screen (Optional)

Jika mau buka untuk public (bukan hanya test users):

1. Sidebar → "APIs & Services" → "OAuth consent screen"
2. Klik "Publish App"
3. Confirm

---

## 🧪 Testing di Production

### 1. Test Google OAuth
```bash
# Test dari browser
https://your-domain.com/login
# Klik "Continue with Google"
# Pastikan redirect bekerja
```

### 2. Check Logs
```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Monitor Nginx error logs
sudo tail -f /var/log/nginx/error.log

# Monitor PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

### 3. Test Database
```bash
# Masuk ke MySQL
mysql -u username -p database_name

# Check migration
SHOW COLUMNS FROM users;
# Pastikan ada: google_id, avatar, provider

# Exit
exit
```

---

## 🔒 Security Checklist

- [ ] `.env` tidak ter-commit ke Git
- [ ] `GOOGLE_CLIENT_SECRET` aman (tidak di-share)
- [ ] HTTPS aktif di production
- [ ] SSL certificate valid
- [ ] Firewall configured
- [ ] Database backup otomatis
- [ ] File permissions correct (775 untuk storage)

---

## 📋 Deployment Checklist

### Pre-Deployment:
- [ ] Backup database
- [ ] Backup files
- [ ] Test di staging/local
- [ ] Update .env.example dengan variable baru

### Deployment:
- [ ] Pull latest code
- [ ] `composer install --no-dev`
- [ ] `php artisan migrate --force`
- [ ] Update .env production
- [ ] Clear all cache
- [ ] Rebuild cache
- [ ] Set permissions
- [ ] Restart services

### Post-Deployment:
- [ ] Test Google OAuth login
- [ ] Test Google OAuth register
- [ ] Test avatar display
- [ ] Test profile page
- [ ] Test set password
- [ ] Test disconnect Google
- [ ] Monitor logs for errors
- [ ] Check performance

---

## 🚨 Rollback Plan (Jika Ada Masalah)

### Quick Rollback:
```bash
# 1. Revert Git
git reset --hard HEAD~1  # atau commit hash sebelumnya

# 2. Restore Composer
composer install --no-dev

# 3. Rollback Migration
php artisan migrate:rollback --step=1

# 4. Restore Database (jika perlu)
mysql -u username -p database_name < backup_YYYYMMDD_HHMMSS.sql

# 5. Clear Cache
php artisan config:clear
php artisan cache:clear

# 6. Restart Services
sudo systemctl restart php8.2-fpm nginx
```

---

## 🔄 Alternative: Zero-Downtime Deployment

Jika mau deployment tanpa downtime:

### 1. Setup Deployment Script
```bash
# deploy.sh
#!/bin/bash

echo "🚀 Starting deployment..."

# Enable maintenance mode
php artisan down --message="Updating system, back in 2 minutes"

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear & rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm

# Disable maintenance mode
php artisan up

echo "✅ Deployment complete!"
```

### 2. Make Executable
```bash
chmod +x deploy.sh
```

### 3. Run Deployment
```bash
./deploy.sh
```

---

## 📊 Monitoring Post-Deployment

### 1. Check Application Health
```bash
# Check if app is running
curl -I https://your-domain.com

# Check response time
curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com
```

### 2. Monitor Error Logs
```bash
# Watch Laravel logs in real-time
tail -f storage/logs/laravel.log | grep ERROR
```

### 3. Check Database Connections
```bash
# Check active connections
mysql -u username -p -e "SHOW PROCESSLIST;"
```

### 4. Monitor Server Resources
```bash
# Check CPU & Memory
htop

# Check disk space
df -h

# Check PHP-FPM status
sudo systemctl status php8.2-fpm
```

---

## 🎯 Quick Commands Reference

```bash
# Full deployment in one command
git pull && composer install --no-dev && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && sudo systemctl restart php8.2-fpm nginx

# Clear all cache
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

# Rebuild cache
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Check Laravel version
php artisan --version

# Check installed packages
composer show

# Check if socialite installed
composer show laravel/socialite
```

---

## 📝 Notes

1. **Composer Lock**: Pastikan `composer.lock` ter-commit ke Git untuk consistency
2. **Environment**: Jangan lupa update `.env.example` dengan variable baru
3. **Testing**: Selalu test di staging dulu sebelum production
4. **Backup**: Backup database sebelum migration
5. **Monitoring**: Monitor logs minimal 1 jam setelah deployment

---

## 🆘 Troubleshooting

### Issue: Composer install gagal
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Install ulang
composer install --no-dev
```

### Issue: Migration gagal
```bash
# Check migration status
php artisan migrate:status

# Rollback & retry
php artisan migrate:rollback
php artisan migrate --force
```

### Issue: Google OAuth tidak bekerja
```bash
# Check .env
cat .env | grep GOOGLE

# Clear config cache
php artisan config:clear
php artisan config:cache

# Check routes
php artisan route:list | grep google
```

### Issue: 500 Error setelah deployment
```bash
# Check logs
tail -100 storage/logs/laravel.log

# Check permissions
ls -la storage bootstrap/cache

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## ✅ Success Indicators

Deployment berhasil jika:
- ✅ Website accessible tanpa error
- ✅ Google OAuth login bekerja
- ✅ Avatar muncul di navbar
- ✅ Profile page menampilkan Google account management
- ✅ No errors di Laravel logs
- ✅ No errors di Nginx/Apache logs
- ✅ Database migration success
- ✅ All cache rebuilt

---

## 🎉 Done!

Setelah semua step selesai, aplikasi production sudah update dengan fitur Google OAuth! 🚀
