# Panduan Deployment Smart Copy SMK

## Persiapan Server

### Minimum Requirements
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL/PostgreSQL atau SQLite
- Web server (Apache/Nginx)

### Recommended Hosting
1. **Shared Hosting** (untuk pemula):
   - Niagahoster (Rp 20.000/bulan)
   - Hostinger (Rp 25.000/bulan)
   - Rumahweb (Rp 30.000/bulan)

2. **VPS** (untuk scale):
   - DigitalOcean (mulai $6/bulan)
   - Vultr (mulai $5/bulan)
   - AWS Lightsail (mulai $3.5/bulan)

3. **Platform as a Service**:
   - Laravel Forge + DigitalOcean
   - Heroku (free tier available)
   - Railway.app

## Step-by-Step Deployment

### 1. Persiapan Repository

```bash
# Initialize git (jika belum)
git init
git add .
git commit -m "Initial commit"

# Push ke GitHub/GitLab
git remote add origin https://github.com/username/smart-copy-smk.git
git push -u origin main
```

### 2. Setup di Server (VPS)

#### A. Install Dependencies
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y

# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
```

#### B. Clone & Setup Project
```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/username/smart-copy-smk.git
cd smart-copy-smk

# Set permissions
sudo chown -R www-data:www-data /var/www/smart-copy-smk
sudo chmod -R 755 /var/www/smart-copy-smk/storage
sudo chmod -R 755 /var/www/smart-copy-smk/bootstrap/cache

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate
```

#### C. Configure Database
```bash
# Login ke MySQL
sudo mysql

# Buat database
CREATE DATABASE smart_copy_smk;
CREATE USER 'smartcopy'@'localhost' IDENTIFIED BY 'password_kuat_123';
GRANT ALL PRIVILEGES ON smart_copy_smk.* TO 'smartcopy'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_copy_smk
DB_USERNAME=smartcopy
DB_PASSWORD=password_kuat_123

OPENAI_API_KEY=sk-your-actual-api-key
```

#### D. Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --class=PackageSeeder
```

#### E. Configure Nginx
```bash
sudo nano /etc/nginx/sites-available/smart-copy-smk
```

Paste konfigurasi:
```nginx
server {
    listen 80;
    server_name smartcopysmk.com www.smartcopysmk.com;
    root /var/www/smart-copy-smk/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/smart-copy-smk /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### F. Setup SSL (HTTPS)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d smartcopysmk.com -d www.smartcopysmk.com
```

### 3. Deployment via Shared Hosting

#### A. Persiapan Lokal
```bash
# Build production assets
npm run build

# Optimize
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### B. Upload Files
1. Compress project: `zip -r smart-copy-smk.zip . -x "node_modules/*" ".git/*"`
2. Upload via FTP/cPanel File Manager
3. Extract di public_html atau subdomain folder

#### C. Setup di cPanel
1. Buat database MySQL via cPanel
2. Edit `.env` dengan kredensial database
3. Jalankan migrations via Terminal cPanel:
```bash
cd public_html
php artisan migrate --force
php artisan db:seed --class=PackageSeeder
```

4. Set document root ke folder `public`

### 4. Continuous Deployment (Optional)

#### Setup GitHub Actions
Buat file `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.SERVER_HOST }}
        username: ${{ secrets.SERVER_USER }}
        key: ${{ secrets.SSH_PRIVATE_KEY }}
        script: |
          cd /var/www/smart-copy-smk
          git pull origin main
          composer install --no-dev --optimize-autoloader
          npm install
          npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl restart php8.2-fpm
```

## Monitoring & Maintenance

### 1. Setup Logging
```bash
# Rotate logs
sudo nano /etc/logrotate.d/smart-copy-smk
```

```
/var/www/smart-copy-smk/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0640 www-data www-data
}
```

### 2. Setup Cron Jobs
```bash
sudo crontab -e
```

Add:
```
* * * * * cd /var/www/smart-copy-smk && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Backup Automation
```bash
# Buat script backup
sudo nano /usr/local/bin/backup-smart-copy.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/smart-copy-smk"

# Backup database
mysqldump -u smartcopy -p'password_kuat_123' smart_copy_smk > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/smart-copy-smk/storage/app

# Delete old backups (keep 7 days)
find $BACKUP_DIR -type f -mtime +7 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-smart-copy.sh

# Add to cron (daily at 2 AM)
sudo crontab -e
0 2 * * * /usr/local/bin/backup-smart-copy.sh
```

### 4. Performance Monitoring
```bash
# Install monitoring tools
sudo apt install htop iotop -y

# Monitor logs
tail -f /var/www/smart-copy-smk/storage/logs/laravel.log
```

## Security Checklist

- [ ] Set `APP_DEBUG=false` di production
- [ ] Set `APP_ENV=production`
- [ ] Gunakan HTTPS (SSL certificate)
- [ ] Set strong database password
- [ ] Disable directory listing
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Enable firewall (UFW)
- [ ] Regular security updates
- [ ] Backup rutin
- [ ] Monitor error logs

## Troubleshooting

### Error: 500 Internal Server Error
```bash
# Check logs
tail -f /var/www/smart-copy-smk/storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data /var/www/smart-copy-smk
sudo chmod -R 755 /var/www/smart-copy-smk/storage
```

### Error: Database Connection Failed
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Error: OpenAI API Failed
```bash
# Check API key
php artisan tinker
>>> config('services.openai.api_key');
```

## Cost Estimation

### Monthly Operational Cost
- VPS (DigitalOcean): $6/bulan (Rp 90.000)
- Domain (.com): $12/tahun (Rp 15.000/bulan)
- OpenAI API: ~$20/bulan (Rp 300.000) untuk 100 requests/hari
- SSL Certificate: FREE (Let's Encrypt)

**Total**: ~Rp 405.000/bulan

### Break Even Point
Dengan 10 client paket Basic (Rp 50.000):
- Revenue: Rp 500.000/bulan
- Cost: Rp 405.000/bulan
- Profit: Rp 95.000/bulan

Dengan 20 client mixed packages:
- Revenue: Rp 1.500.000/bulan
- Cost: Rp 405.000/bulan
- Profit: Rp 1.095.000/bulan

## Support & Documentation

- Laravel Docs: https://laravel.com/docs
- DigitalOcean Tutorials: https://www.digitalocean.com/community/tutorials
- Stack Overflow: https://stackoverflow.com/questions/tagged/laravel

## Contact untuk Bantuan Deployment

Jika siswa/guru mengalami kesulitan deployment, bisa konsultasi dengan:
- Komunitas Laravel Indonesia: https://t.me/laravelindonesia
- Forum Laravel: https://laracasts.com/discuss
- GitHub Issues: Buat issue di repository project
