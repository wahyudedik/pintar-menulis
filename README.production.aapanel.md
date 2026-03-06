# Smart Copy SMK - Production Deployment Guide (aaPanel)

## 📋 Table of Contents
1. [About aaPanel](#about-aapanel)
2. [System Requirements](#system-requirements)
3. [aaPanel Installation](#aapanel-installation)
4. [aaPanel Configuration](#aapanel-configuration)
5. [Application Deployment](#application-deployment)
6. [Database Setup](#database-setup)
7. [Environment Configuration](#environment-configuration)
8. [SSL Certificate Setup](#ssl-certificate-setup)
9. [Performance Optimization](#performance-optimization)
10. [Monitoring & Maintenance](#monitoring--maintenance)
11. [Backup Strategy](#backup-strategy)
12. [Troubleshooting](#troubleshooting) 

---

## 🎯 About aaPanel

aaPanel adalah control panel gratis untuk Linux yang mempermudah management server dengan GUI. Cocok untuk yang tidak familiar dengan command line.

**Keuntungan aaPanel:**
- ✅ GUI yang user-friendly
- ✅ One-click install LEMP/LAMP stack
- ✅ Built-in file manager
- ✅ Database management (phpMyAdmin)
- ✅ SSL certificate management (Let's Encrypt)
- ✅ Monitoring & logs viewer
- ✅ Backup & restore tools
- ✅ Gratis selamanya

**Official Website**: https://www.aapanel.com/

---

## 🖥️ System Requirements

### VPS Minimum Requirements
- **OS**: Ubuntu 20.04 LTS or higher (Fresh install recommended)
- **RAM**: 2GB minimum (4GB recommended)
- **Storage**: 20GB SSD minimum
- **CPU**: 2 cores minimum
- **Bandwidth**: Unlimited atau minimal 1TB/month

### Recommended VPS Providers
- **Vultr**: $6/month (2GB RAM)
- **DigitalOcean**: $12/month (2GB RAM)
- **Linode**: $12/month (2GB RAM)
- **Contabo**: €5/month (4GB RAM)
- **Niagahoster**: Rp 150.000/month (2GB RAM)

---

## 🚀 aaPanel Installation

### Step 1: Connect to VPS via SSH

**Windows (PowerShell/CMD):**
```bash
ssh root@YOUR_VPS_IP
```

**Mac/Linux (Terminal):**
```bash
ssh root@YOUR_VPS_IP
```

Masukkan password VPS Anda.

### Step 2: Update System
```bash
apt update && apt upgrade -y
```

### Step 3: Install aaPanel

**Ubuntu/Debian:**
```bash
wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh && sudo bash install.sh aapanel
```

**Proses instalasi memakan waktu 5-10 menit.**

### Step 4: Catat Informasi Login

Setelah instalasi selesai, Anda akan melihat:
```
==================================================================
Congratulations! Installed successfully!
==================================================================
aaPanel Internet Address: http://YOUR_VPS_IP:7800/xxxxxxxx
aaPanel Internal Address: http://YOUR_INTERNAL_IP:7800/xxxxxxxx
username: xxxxxxxx
password: xxxxxxxx
==================================================================
```

⚠️ **PENTING**: Simpan informasi ini dengan aman!

### Step 5: Login ke aaPanel

1. Buka browser
2. Akses: `http://YOUR_VPS_IP:7800/xxxxxxxx`
3. Login dengan username & password yang diberikan
4. **WAJIB**: Ganti username & password default!

---

## ⚙️ aaPanel Configuration

### Step 1: Install LNMP Stack

Setelah login pertama kali, aaPanel akan menampilkan dialog untuk install software.

**Pilih LNMP (Recommended):**
- ✅ Nginx 1.22+
- ✅ MySQL 8.0
- ✅ PHP 8.2
- ✅ phpMyAdmin 5.2
- ✅ Redis (Optional tapi recommended)

**Klik "One-click Install"** dan tunggu 10-20 menit.

### Step 2: Install PHP Extensions

Setelah LNMP terinstall:

1. Klik **"App Store"** di sidebar
2. Cari **"PHP 8.2"**
3. Klik **"Settings"**
4. Tab **"Install Extensions"**
5. Install extensions berikut:
   - ✅ opcache
   - ✅ redis
   - ✅ fileinfo
   - ✅ imagemagick
   - ✅ exif

### Step 3: Configure PHP Settings

Di PHP 8.2 Settings:

**Tab "Configuration File":**
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
```

**Tab "Performance Tuning":**
- Enable OPcache
- Set OPcache memory: 256MB

**Klik "Save"** dan **"Restart PHP"**

### Step 4: Install Composer

1. Klik **"App Store"**
2. Cari **"Composer"**
3. Klik **"Install"**

### Step 5: Install Supervisor (Optional tapi Recommended)

1. Klik **"App Store"**
2. Cari **"Supervisor"**
3. Klik **"Install"**

---

## 📦 Application Deployment

### Step 1: Create Website

1. Klik **"Website"** di sidebar
2. Klik **"Add Site"**
3. Isi form:
   - **Domain**: `your-domain.com` (atau IP jika belum ada domain)
   - **Root Directory**: `/www/wwwroot/pintar-menulis`
   - **PHP Version**: PHP-82
   - **Database**: Create (nama: `pintar_menulis`)
4. Klik **"Submit"**

### Step 2: Upload Application Files

**Opsi A: Via File Manager (Recommended untuk pemula)**

1. Klik **"Files"** di sidebar
2. Navigate ke `/www/wwwroot/pintar-menulis`
3. Delete semua file default (index.html, dll)
4. Klik **"Upload"**
5. Upload file ZIP project Anda
6. Klik kanan ZIP → **"Unzip"**
7. Pindahkan semua file dari folder hasil unzip ke root directory

**Opsi B: Via Git (Recommended untuk developer)**

1. Klik **"Files"** di sidebar
2. Navigate ke `/www/wwwroot/`
3. Klik **"Terminal"** (icon terminal di toolbar)
4. Jalankan:
```bash
cd /www/wwwroot/
rm -rf pintar-menulis/*
git clone https://github.com/your-repo/pintar-menulis.git pintar-menulis
cd pintar-menulis
```

### Step 3: Install Dependencies

Di Terminal aaPanel:

```bash
cd /www/wwwroot/pintar-menulis

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install
npm run build
```

### Step 4: Set Permissions

```bash
chown -R www:www /www/wwwroot/noteds.com
chmod -R 755 /www/wwwroot/noteds.com
chmod -R 775 /www/wwwroot/noteds.com/storage
chmod -R 775 /www/wwwroot/noteds.com/bootstrap/cache
```

### Step 5: Configure Website Root

1. Klik **"Website"** di sidebar
2. Cari website Anda → Klik **"Settings"**
3. Tab **"Site Directory"**
4. Set **"Running Directory"**: `/public`
5. Enable **"Prevent Cross-site Access"**
6. Klik **"Save"**

### Step 6: Configure Nginx Rewrite Rules

Di Website Settings → Tab **"Rewrite"**:

Pilih **"Laravel 5"** dari dropdown atau paste manual:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/tmp/php-cgi-82.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}

location ~ /\.(?!well-known).* {
    deny all;
}
```

Klik **"Save"**

---

## 🗄️ Database Setup

### Step 1: Access phpMyAdmin

1. Klik **"Database"** di sidebar
2. Klik **"phpMyAdmin"** button
3. Login dengan root credentials (lihat di Database page)

### Step 2: Create Database User

Di phpMyAdmin:

1. Klik **"User accounts"**
2. Klik **"Add user account"**
3. Isi:
   - **User name**: `pintar_user`
   - **Host name**: `localhost`
   - **Password**: Generate strong password
   - **Database for user account**: Check "Create database with same name and grant all privileges"
4. Klik **"Go"**

**ATAU via aaPanel Database Manager:**

1. Klik **"Database"** di sidebar
2. Database `pintar_menulis` sudah dibuat otomatis saat create website
3. Catat username & password yang diberikan

### Step 3: Run Migrations

Di Terminal aaPanel:

```bash
cd /www/wwwroot/pintar-menulis
php artisan migrate --force
```

### Step 4: Seed Initial Data (Optional)

```bash
php artisan db:seed --force
```

---

## ⚙️ Environment Configuration

### Step 1: Copy .env File

Di Terminal:
```bash
cd /www/wwwroot/pintar-menulis
cp .env.example .env
```

### Step 2: Edit .env

**Via File Manager:**
1. Klik **"Files"** di sidebar
2. Navigate ke `/www/wwwroot/pintar-menulis`
3. Klik kanan `.env` → **"Edit"**

**Atau via Terminal:**
```bash
nano .env
```

### Step 3: Configure .env

```env
APP_NAME="Smart Copy SMK"
APP_ENV=production
APP_KEY=base64:WILL_BE_GENERATED
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

# Database (sesuaikan dengan info dari aaPanel Database)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pintar_menulis
DB_USERNAME=pintar_menulis  # atau pintar_user
DB_PASSWORD=YOUR_DATABASE_PASSWORD

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Jika Redis terinstall (recommended):
# SESSION_DRIVER=redis
# CACHE_DRIVER=redis
# QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Google Gemini API
GEMINI_API_KEY=your_gemini_api_key_here

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Midtrans (Optional)
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Save file** (Ctrl+X, Y, Enter jika pakai nano)

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Create Storage Link

```bash
php artisan storage:link
```

### Step 6: Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔒 SSL Certificate Setup

### Option 1: Let's Encrypt (Free - Recommended)

1. Klik **"Website"** di sidebar
2. Cari website Anda → Klik **"Settings"**
3. Tab **"SSL"**
4. Pilih **"Let's Encrypt"**
5. Isi email Anda
6. Check domain yang ingin di-SSL
7. Klik **"Apply"**
8. Tunggu 1-2 menit
9. Enable **"Force HTTPS"**

### Option 2: Upload Custom SSL

Jika Anda punya SSL certificate sendiri:

1. Tab **"SSL"** → **"Other Certificate"**
2. Paste **Certificate (PEM format)**
3. Paste **Private Key (KEY)**
4. Klik **"Save"**
5. Enable **"Force HTTPS"**

---

## ⚡ Performance Optimization

### 1. Enable OPcache

Sudah enabled by default di aaPanel PHP 8.2.

**Verify:**
1. App Store → PHP 8.2 → Settings
2. Tab "Install Extensions"
3. Check "opcache" is installed

### 2. Setup Queue Workers (Supervisor)

**Via aaPanel Supervisor:**

1. Klik **"App Store"** → Install **"Supervisor"**
2. Klik **"Supervisor"** di sidebar
3. Klik **"Add Daemon"**
4. Isi form:
   - **Name**: `pintar-menulis-worker`
   - **Run Directory**: `/www/wwwroot/pintar-menulis`
   - **Start Command**: 
     ```bash
     /usr/bin/php /www/wwwroot/pintar-menulis/artisan queue:work --sleep=3 --tries=3 --max-time=3600
     ```
   - **Processes**: `2`
   - **User**: `www`
   - **Auto Start**: ✅ Yes
   - **Auto Restart**: ✅ Yes
5. Klik **"Submit"**

### 3. Setup Cron Jobs

1. Klik **"Cron"** di sidebar
2. Klik **"Add Cron"**
3. Isi form:
   - **Task Type**: Shell Script
   - **Task Name**: Laravel Scheduler
   - **Period**: Every minute (N Minutes → 1)
   - **Script Content**:
     ```bash
     cd /www/wwwroot/pintar-menulis && php artisan schedule:run >> /dev/null 2>&1
     ```
4. Klik **"Submit"**

### 4. Enable Redis Caching (Optional)

Jika Redis terinstall:

1. Update `.env`:
   ```env
   SESSION_DRIVER=redis
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

2. Clear cache:
   ```bash
   php artisan config:cache
   ```

### 5. Optimize Images

Di Terminal:
```bash
apt install jpegoptim optipng pngquant gifsicle -y

# Optimize existing images
find /www/wwwroot/pintar-menulis/public/storage -type f -name "*.jpg" -exec jpegoptim --strip-all {} \;
find /www/wwwroot/pintar-menulis/public/storage -type f -name "*.png" -exec optipng -o2 {} \;
```

---

## 📊 Monitoring & Maintenance

### 1. Monitor Server Resources

**Via aaPanel Dashboard:**
- CPU Usage
- Memory Usage
- Disk Usage
- Network Traffic

**Via Terminal:**
```bash
# Check disk space
df -h

# Check memory
free -m

# Check CPU
top
```

### 2. View Application Logs

**Via File Manager:**
1. Files → `/www/wwwroot/pintar-menulis/storage/logs`
2. Open `laravel.log`

**Via Terminal:**
```bash
tail -f /www/wwwroot/pintar-menulis/storage/logs/laravel.log
```

### 3. View Nginx Logs

**Via aaPanel:**
1. Website → Settings → Tab "Log"
2. View Access Log / Error Log

### 4. Monitor Queue Workers

**Via Supervisor:**
1. Supervisor → Check worker status
2. Should show "RUNNING"

**Via Terminal:**
```bash
supervisorctl status
```

### 5. Health Check

Create health check endpoint (already in routes):
```
https://your-domain.com/health
```

---

## 💾 Backup Strategy

### 1. Database Backup (via aaPanel)

**Manual Backup:**
1. Klik **"Database"** di sidebar
2. Cari database `pintar_menulis`
3. Klik **"Backup"**
4. File backup tersimpan di `/www/backup/database/`

**Scheduled Backup:**
1. Klik **"Cron"** di sidebar
2. Klik **"Add Cron"**
3. Isi form:
   - **Task Type**: Backup database
   - **Task Name**: Daily DB Backup
   - **Period**: Daily (2:00 AM)
   - **Database**: `pintar_menulis`
   - **Backup to**: Local
4. Klik **"Submit"**

### 2. Website Backup (via aaPanel)

**Manual Backup:**
1. Klik **"Website"** di sidebar
2. Cari website Anda
3. Klik **"Backup"**
4. File backup tersimpan di `/www/backup/site/`

**Scheduled Backup:**
1. Klik **"Cron"** di sidebar
2. Klik **"Add Cron"**
3. Isi form:
   - **Task Type**: Backup website
   - **Task Name**: Daily Site Backup
   - **Period**: Daily (3:00 AM)
   - **Website**: `pintar-menulis`
   - **Backup to**: Local
4. Klik **"Submit"**

### 3. Download Backups

**Via File Manager:**
1. Files → `/www/backup/`
2. Download backup files ke komputer lokal

**Via FTP:**
- Use FileZilla or WinSCP
- Connect to VPS
- Download from `/www/backup/`

### 4. Offsite Backup (Recommended)

Upload backups ke cloud storage:
- Google Drive
- Dropbox
- AWS S3
- Wasabi

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error

**Check Logs:**
1. Website → Settings → Tab "Log" → Error Log
2. Files → `/www/wwwroot/pintar-menulis/storage/logs/laravel.log`

**Fix Permissions:**
```bash
chown -R www:www /www/wwwroot/pintar-menulis
chmod -R 755 /www/wwwroot/pintar-menulis
chmod -R 775 /www/wwwroot/pintar-menulis/storage
chmod -R 775 /www/wwwroot/pintar-menulis/bootstrap/cache
```

**Clear Cache:**
```bash
cd /www/wwwroot/pintar-menulis
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 2. Database Connection Error

**Check Database Credentials:**
1. Database → Check username & password
2. Compare with `.env` file

**Test Connection:**
```bash
cd /www/wwwroot/pintar-menulis
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 3. CSS/JS Not Loading

**Check Storage Link:**
```bash
cd /www/wwwroot/pintar-menulis
php artisan storage:link
```

**Rebuild Assets:**
```bash
npm run build
```

#### 4. Queue Not Processing

**Check Supervisor:**
1. Supervisor → Check worker status
2. If stopped, click "Start"

**Restart Worker:**
```bash
supervisorctl restart pintar-menulis-worker:*
```

#### 5. SSL Certificate Error

**Renew Certificate:**
1. Website → Settings → SSL
2. Click "Renew" on Let's Encrypt

**Check Domain DNS:**
- Make sure domain points to VPS IP
- Wait for DNS propagation (up to 24 hours)

#### 6. High Memory Usage

**Check Processes:**
```bash
top
```

**Restart Services:**
1. App Store → PHP 8.2 → Restart
2. App Store → Nginx → Restart
3. App Store → MySQL → Restart

#### 7. Disk Space Full

**Check Disk Usage:**
```bash
df -h
```

**Clean Old Logs:**
```bash
cd /www/wwwroot/pintar-menulis/storage/logs
rm -f laravel-*.log
```

**Clean Old Backups:**
```bash
cd /www/backup
rm -rf old_backups
```

---

## 🚀 Deployment Workflow

### Initial Deployment Checklist

- [x] VPS ready with Ubuntu
- [x] aaPanel installed
- [x] LNMP stack installed
- [x] PHP extensions installed
- [x] Website created in aaPanel
- [x] Application files uploaded
- [x] Dependencies installed
- [x] Database created
- [x] .env configured
- [x] Migrations run
- [x] Storage link created
- [x] SSL certificate installed
- [x] Queue workers configured
- [x] Cron jobs configured
- [x] Backups scheduled
- [x] Application tested

### Subsequent Deployments

**Via Git (Recommended):**

```bash
cd /www/wwwroot/pintar-menulis

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear & cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
supervisorctl restart pintar-menulis-worker:*
```

**Via File Upload:**

1. Backup current files
2. Upload new files via File Manager
3. Run commands above (except git pull)

---

## 📝 Important Notes

### aaPanel Security

**Change Default Port:**
1. Settings → Panel Settings
2. Change Panel Port from 7800 to custom port
3. Update firewall rules

**Enable Two-Factor Authentication:**
1. Settings → Panel Settings
2. Enable 2FA

**Whitelist IP (Optional):**
1. Security → Panel Access
2. Add your IP to whitelist

### ESCROW System

Platform menggunakan sistem ESCROW:
1. Client wajib bayar sebelum order dikerjakan
2. Uang di-hold platform sampai order selesai
3. Client approve → Uang release ke operator (90%)
4. Platform commission: 10%

### Default Credentials

⚠️ **IMPORTANT**: Change all default passwords!
- aaPanel: Change after first login
- Database: Use strong password
- Admin app: admin@example.com / password

---

## 🎯 Production Checklist

Before going live:
- [ ] aaPanel secured (password changed, 2FA enabled)
- [ ] SSL certificate installed & working
- [ ] Database backed up
- [ ] .env configured correctly
- [ ] All API keys added
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] Backups scheduled
- [ ] Application tested thoroughly
- [ ] Error tracking enabled
- [ ] Monitoring setup
- [ ] Documentation updated

---

## 📞 Support

### aaPanel Support
- Forum: https://forum.aapanel.com/
- Documentation: https://doc.aapanel.com/
- Telegram: @aapanel

### Application Support
- Check logs in `/www/wwwroot/pintar-menulis/storage/logs/`
- Check Nginx logs in aaPanel
- Check PHP error logs in aaPanel

---

## 📚 Additional Resources

- [aaPanel Official Documentation](https://doc.aapanel.com/)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)
- [aaPanel Video Tutorials](https://www.youtube.com/c/aapanel)

---

**Last Updated**: March 2026
**Version**: 1.0.0
**Maintained By**: Smart Copy SMK Team

---

## 🎓 Quick Start Guide

### For Beginners (Step by Step):

1. **Beli VPS** (Vultr/DigitalOcean/Niagahoster)
2. **Install aaPanel** (copy-paste command di SSH)
3. **Login ke aaPanel** (buka browser, akses panel)
4. **Install LNMP** (klik one-click install)
5. **Create Website** (isi domain, create database)
6. **Upload Files** (via file manager atau git)
7. **Install Dependencies** (composer & npm via terminal)
8. **Configure .env** (edit via file manager)
9. **Run Migrations** (php artisan migrate via terminal)
10. **Install SSL** (klik apply di SSL tab)
11. **Setup Backups** (schedule via cron)
12. **Test Application** (buka domain di browser)

**Done! Your application is live! 🎉**
