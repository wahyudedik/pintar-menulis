# Smart Copy SMK - Production Deployment Guide

## 📋 Table of Contents
1. [System Requirements](#system-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Server Setup](#server-setup)
4. [Application Deployment](#application-deployment)
5. [Database Setup](#database-setup)
6. [Environment Configuration](#environment-configuration)
7. [Security Hardening](#security-hardening)
8. [Performance Optimization](#performance-optimization)
9. [Monitoring & Maintenance](#monitoring--maintenance)
10. [Backup Strategy](#backup-strategy)
11. [Troubleshooting](#troubleshooting)

---

## 🖥️ System Requirements

### Minimum Requirements
- **OS**: Ubuntu 20.04 LTS or higher / CentOS 8+
- **PHP**: 8.2 or higher
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Database**: MySQL 8.0+ or MariaDB 10.5+
- **Memory**: 2GB RAM minimum (4GB recommended)
- **Storage**: 20GB SSD minimum
- **CPU**: 2 cores minimum (4 cores recommended)

### Required PHP Extensions
```bash
php8.2-cli
php8.2-fpm
php8.2-mysql
php8.2-mbstring
php8.2-xml
php8.2-curl
php8.2-zip
php8.2-gd
php8.2-bcmath
php8.2-intl
php8.2-redis (optional, for caching)
```

### Additional Software
- **Composer**: 2.x
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **Git**: 2.x
- **Supervisor**: For queue workers
- **Redis**: For caching & sessions (optional but recommended)

---

## ✅ Pre-Deployment Checklist

### Code Preparation
- [ ] All features tested locally
- [ ] Database migrations tested
- [ ] Environment variables documented
- [ ] API keys obtained (Google Gemini, Midtrans if used)
- [ ] SSL certificate ready
- [ ] Domain name configured

### Security
- [ ] All passwords changed from defaults
- [ ] API keys secured
- [ ] CORS configured properly
- [ ] Rate limiting enabled
- [ ] CSRF protection enabled
- [ ] XSS protection enabled

### Performance
- [ ] Assets compiled for production
- [ ] Images optimized
- [ ] Caching strategy defined
- [ ] CDN configured (if applicable)

---

## 🚀 Server Setup

### 1. Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 2. Install PHP 8.2
```bash
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl -y
```

### 3. Install Nginx
```bash
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 4. Install MySQL
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

### 5. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 6. Install Node.js & NPM
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

### 7. Install Redis (Optional but Recommended)
```bash
sudo apt install redis-server -y
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### 8. Install Supervisor
```bash
sudo apt install supervisor -y
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

---

## 📦 Application Deployment

### 1. Create Application Directory
```bash
sudo mkdir -p /var/www/pintar-menulis
sudo chown -R $USER:$USER /var/www/pintar-menulis
cd /var/www/pintar-menulis
```

### 2. Clone Repository
```bash
git clone https://github.com/your-repo/pintar-menulis.git .
# OR upload files via FTP/SFTP
```

### 3. Install Dependencies
```bash
# PHP dependencies
composer install --optimize-autoloader --no-dev

# Node dependencies
npm install
npm run build
```

### 4. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/pintar-menulis
sudo chmod -R 755 /var/www/pintar-menulis
sudo chmod -R 775 /var/www/pintar-menulis/storage
sudo chmod -R 775 /var/www/pintar-menulis/bootstrap/cache
```

---

## 🗄️ Database Setup

### 1. Create Database
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE pintar_menulis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pintar_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON pintar_menulis.* TO 'pintar_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Run Migrations
```bash
cd /var/www/pintar-menulis
php artisan migrate --force
```

### 3. Seed Initial Data (Optional)
```bash
php artisan db:seed --force
```

---

## ⚙️ Environment Configuration

### 1. Copy Environment File
```bash
cp .env.example .env
```

### 2. Configure .env
```bash
nano .env
```

### Essential Configuration:
```env
APP_NAME="Smart Copy SMK"
APP_ENV=production
APP_KEY=base64:GENERATE_THIS_WITH_php_artisan_key:generate
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pintar_menulis
DB_USERNAME=pintar_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Session & Cache (Use Redis for better performance)
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Google Gemini API
GEMINI_API_KEY=your_gemini_api_key_here

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Midtrans (Optional - for automatic payment)
MIDTRANS_SERVER_KEY=your_midtrans_server_key
MIDTRANS_CLIENT_KEY=your_midtrans_client_key
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Clear & Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔒 Security Hardening

### 1. Nginx Configuration
Create: `/etc/nginx/sites-available/pintar-menulis`

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/pintar-menulis/public;

    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';" always;

    # Logging
    access_log /var/log/nginx/pintar-menulis-access.log;
    error_log /var/log/nginx/pintar-menulis-error.log;

    # Max upload size
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/pintar-menulis /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 2. Install SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### 3. Configure Firewall
```bash
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

### 4. Secure MySQL
```bash
sudo mysql_secure_installation
```

### 5. Disable Directory Listing
Already handled in Nginx config above.

---

## ⚡ Performance Optimization

### 1. Enable OPcache
Edit: `/etc/php/8.2/fpm/php.ini`
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### 2. Configure Queue Workers
Create: `/etc/supervisor/conf.d/pintar-menulis-worker.conf`

```ini
[program:pintar-menulis-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pintar-menulis/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pintar-menulis/storage/logs/worker.log
stopwaitsecs=3600
```

Start workers:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pintar-menulis-worker:*
```

### 3. Setup Cron Jobs
```bash
sudo crontab -e -u www-data
```

Add:
```cron
* * * * * cd /var/www/pintar-menulis && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Enable Redis Caching
Already configured in `.env` above.

### 5. Optimize Images
```bash
# Install image optimization tools
sudo apt install jpegoptim optipng pngquant gifsicle -y

# Optimize existing images
find /var/www/pintar-menulis/public/storage -type f -name "*.jpg" -exec jpegoptim --strip-all {} \;
find /var/www/pintar-menulis/public/storage -type f -name "*.png" -exec optipng -o2 {} \;
```

---

## 📊 Monitoring & Maintenance

### 1. Setup Log Rotation
Create: `/etc/logrotate.d/pintar-menulis`

```
/var/www/pintar-menulis/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 2. Monitor Application
```bash
# Check application status
php artisan about

# Check queue workers
sudo supervisorctl status

# Check logs
tail -f /var/www/pintar-menulis/storage/logs/laravel.log

# Check Nginx logs
tail -f /var/log/nginx/pintar-menulis-error.log
```

### 3. Health Check Endpoint
Add to routes:
```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
    ]);
});
```

### 4. Setup Monitoring Tools (Optional)
- **Laravel Telescope**: For debugging (disable in production!)
- **Laravel Horizon**: For queue monitoring
- **New Relic / Datadog**: For APM
- **Sentry**: For error tracking

---

## 💾 Backup Strategy

### 1. Database Backup Script
Create: `/usr/local/bin/backup-pintar-menulis.sh`

```bash
#!/bin/bash

# Configuration
DB_NAME="pintar_menulis"
DB_USER="pintar_user"
DB_PASS="STRONG_PASSWORD_HERE"
BACKUP_DIR="/var/backups/pintar-menulis"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup storage files
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/pintar-menulis/storage/app/public

# Delete backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/backup-pintar-menulis.sh
```

### 2. Schedule Backups
```bash
sudo crontab -e
```

Add:
```cron
0 2 * * * /usr/local/bin/backup-pintar-menulis.sh >> /var/log/pintar-menulis-backup.log 2>&1
```

### 3. Offsite Backup (Recommended)
- Upload to AWS S3
- Upload to Google Cloud Storage
- Upload to Dropbox/Google Drive
- Use rsync to remote server

---

## 🔧 Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```bash
# Check logs
tail -f /var/www/pintar-menulis/storage/logs/laravel.log
tail -f /var/log/nginx/pintar-menulis-error.log

# Check permissions
sudo chown -R www-data:www-data /var/www/pintar-menulis/storage
sudo chmod -R 775 /var/www/pintar-menulis/storage

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

#### 2. Database Connection Error
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check MySQL status
sudo systemctl status mysql

# Check credentials in .env
```

#### 3. Queue Not Processing
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart pintar-menulis-worker:*

# Check queue
php artisan queue:work --once
```

#### 4. Storage Link Missing
```bash
php artisan storage:link
```

#### 5. Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/pintar-menulis
sudo chmod -R 755 /var/www/pintar-menulis
sudo chmod -R 775 /var/www/pintar-menulis/storage
sudo chmod -R 775 /var/www/pintar-menulis/bootstrap/cache
```

---

## 🚀 Deployment Workflow

### Initial Deployment
1. Setup server (follow Server Setup section)
2. Deploy application (follow Application Deployment section)
3. Configure environment (follow Environment Configuration section)
4. Setup database (follow Database Setup section)
5. Configure web server (follow Security Hardening section)
6. Setup SSL certificate
7. Configure monitoring & backups
8. Test application thoroughly

### Subsequent Deployments
```bash
# 1. Pull latest code
cd /var/www/pintar-menulis
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Clear & cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart services
sudo supervisorctl restart pintar-menulis-worker:*
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

# 6. Verify deployment
php artisan about
curl https://your-domain.com/health
```

---

## 📞 Support & Maintenance

### Regular Maintenance Tasks

**Daily:**
- Monitor error logs
- Check queue workers status
- Monitor disk space

**Weekly:**
- Review application logs
- Check backup integrity
- Update dependencies (security patches)

**Monthly:**
- Review performance metrics
- Optimize database
- Clean old logs and backups
- Security audit

### Performance Monitoring
```bash
# Check disk usage
df -h

# Check memory usage
free -m

# Check CPU usage
top

# Check MySQL performance
mysql -u root -p -e "SHOW PROCESSLIST;"

# Check slow queries
mysql -u root -p -e "SHOW VARIABLES LIKE 'slow_query_log';"
```

---

## 📝 Important Notes

### ESCROW System
Platform menggunakan sistem ESCROW untuk keamanan transaksi:
1. Client wajib bayar sebelum order dikerjakan
2. Uang di-hold platform sampai order selesai
3. Client approve → Uang release ke operator (90%)
4. Platform commission: 10%

### User Roles
- **Client**: Membuat order, bayar, approve hasil
- **Operator**: Mengerjakan order, terima pembayaran
- **Guru**: Training ML model (future feature)
- **Admin**: Verifikasi payment, mediasi dispute, manage users

### Default Credentials
⚠️ **IMPORTANT**: Change all default passwords!
- Admin: admin@example.com / password
- Test accounts: password

---

## 🎯 Production Checklist

Before going live:
- [ ] All environment variables configured
- [ ] SSL certificate installed
- [ ] Database backed up
- [ ] Firewall configured
- [ ] Monitoring setup
- [ ] Backup strategy implemented
- [ ] Error tracking enabled
- [ ] Performance optimized
- [ ] Security hardened
- [ ] Documentation updated
- [ ] Team trained
- [ ] Support plan ready

---

## 📚 Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Nginx Configuration Guide](https://nginx.org/en/docs/)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)
- [Let's Encrypt Documentation](https://letsencrypt.org/docs/)

---

**Last Updated**: March 2026
**Version**: 1.0.0
**Maintained By**: Smart Copy SMK Team
