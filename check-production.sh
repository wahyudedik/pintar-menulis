#!/bin/bash

# Smart Copy SMK - Production Health Check Script
# Run this script on your VPS to diagnose issues

echo "=========================================="
echo "Smart Copy SMK - Production Health Check"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root or www user
if [ "$EUID" -ne 0 ] && [ "$(whoami)" != "www" ]; then 
    echo -e "${YELLOW}Warning: Not running as root or www user. Some checks may fail.${NC}"
    echo ""
fi

# 1. Check PHP Version
echo "1. Checking PHP Version..."
PHP_VERSION=$(php -v | head -n 1)
echo "   $PHP_VERSION"
if php -v | grep -q "PHP 8"; then
    echo -e "   ${GREEN}✓ PHP 8.x detected${NC}"
else
    echo -e "   ${RED}✗ PHP 8.x not found${NC}"
fi
echo ""

# 2. Check if in correct directory
echo "2. Checking Application Directory..."
if [ -f "artisan" ]; then
    echo -e "   ${GREEN}✓ Laravel application found${NC}"
    APP_PATH=$(pwd)
    echo "   Path: $APP_PATH"
else
    echo -e "   ${RED}✗ Not in Laravel root directory${NC}"
    echo "   Please cd to /www/wwwroot/pintar-menulis"
    exit 1
fi
echo ""

# 3. Check .env file
echo "3. Checking .env Configuration..."
if [ -f ".env" ]; then
    echo -e "   ${GREEN}✓ .env file exists${NC}"
    
    # Check APP_KEY
    if grep -q "APP_KEY=base64:" .env; then
        echo -e "   ${GREEN}✓ APP_KEY is set${NC}"
    else
        echo -e "   ${RED}✗ APP_KEY not set${NC}"
        echo "   Run: php artisan key:generate"
    fi
    
    # Check GEMINI_API_KEY
    if grep -q "GEMINI_API_KEY=" .env && ! grep -q "GEMINI_API_KEY=$" .env && ! grep -q "GEMINI_API_KEY=your" .env; then
        GEMINI_KEY=$(grep "GEMINI_API_KEY=" .env | cut -d '=' -f2)
        if [ ${#GEMINI_KEY} -gt 20 ]; then
            echo -e "   ${GREEN}✓ GEMINI_API_KEY is configured (${#GEMINI_KEY} chars)${NC}"
        else
            echo -e "   ${YELLOW}⚠ GEMINI_API_KEY seems too short${NC}"
        fi
    else
        echo -e "   ${RED}✗ GEMINI_API_KEY not configured${NC}"
        echo "   Get API key from: https://aistudio.google.com/app/apikey"
    fi
    
    # Check DB credentials
    if grep -q "DB_DATABASE=" .env && ! grep -q "DB_DATABASE=$" .env; then
        echo -e "   ${GREEN}✓ Database configured${NC}"
    else
        echo -e "   ${RED}✗ Database not configured${NC}"
    fi
    
    # Check APP_ENV
    APP_ENV=$(grep "APP_ENV=" .env | cut -d '=' -f2)
    if [ "$APP_ENV" = "production" ]; then
        echo -e "   ${GREEN}✓ APP_ENV=production${NC}"
    else
        echo -e "   ${YELLOW}⚠ APP_ENV=$APP_ENV (should be 'production')${NC}"
    fi
    
    # Check APP_DEBUG
    APP_DEBUG=$(grep "APP_DEBUG=" .env | cut -d '=' -f2)
    if [ "$APP_DEBUG" = "false" ]; then
        echo -e "   ${GREEN}✓ APP_DEBUG=false${NC}"
    else
        echo -e "   ${YELLOW}⚠ APP_DEBUG=$APP_DEBUG (should be 'false' in production)${NC}"
    fi
else
    echo -e "   ${RED}✗ .env file not found${NC}"
    echo "   Run: cp .env.example .env"
fi
echo ""

# 4. Check Permissions
echo "4. Checking File Permissions..."
if [ -w "storage" ]; then
    echo -e "   ${GREEN}✓ storage/ is writable${NC}"
else
    echo -e "   ${RED}✗ storage/ is not writable${NC}"
    echo "   Run: chmod -R 775 storage"
fi

if [ -w "bootstrap/cache" ]; then
    echo -e "   ${GREEN}✓ bootstrap/cache/ is writable${NC}"
else
    echo -e "   ${RED}✗ bootstrap/cache/ is not writable${NC}"
    echo "   Run: chmod -R 775 bootstrap/cache"
fi
echo ""

# 5. Check Storage Link
echo "5. Checking Storage Link..."
if [ -L "public/storage" ]; then
    echo -e "   ${GREEN}✓ Storage link exists${NC}"
else
    echo -e "   ${RED}✗ Storage link missing${NC}"
    echo "   Run: php artisan storage:link"
fi
echo ""

# 6. Check Composer Dependencies
echo "6. Checking Composer Dependencies..."
if [ -d "vendor" ]; then
    echo -e "   ${GREEN}✓ vendor/ directory exists${NC}"
    VENDOR_COUNT=$(find vendor -maxdepth 2 -type d | wc -l)
    echo "   Packages: ~$VENDOR_COUNT directories"
else
    echo -e "   ${RED}✗ vendor/ directory not found${NC}"
    echo "   Run: composer install --optimize-autoloader --no-dev"
fi
echo ""

# 7. Check Node Modules & Build
echo "7. Checking Frontend Assets..."
if [ -d "node_modules" ]; then
    echo -e "   ${GREEN}✓ node_modules/ exists${NC}"
else
    echo -e "   ${YELLOW}⚠ node_modules/ not found${NC}"
    echo "   Run: npm install"
fi

if [ -f "public/build/manifest.json" ]; then
    echo -e "   ${GREEN}✓ Assets built (manifest.json exists)${NC}"
else
    echo -e "   ${RED}✗ Assets not built${NC}"
    echo "   Run: npm run build"
fi
echo ""

# 8. Check Database Connection
echo "8. Testing Database Connection..."
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Connected'; } catch (Exception \$e) { echo 'Failed: ' . \$e->getMessage(); }" 2>&1 | grep -q "Connected"
if [ $? -eq 0 ]; then
    echo -e "   ${GREEN}✓ Database connection successful${NC}"
else
    echo -e "   ${RED}✗ Database connection failed${NC}"
    echo "   Check DB credentials in .env"
fi
echo ""

# 9. Check Gemini API
echo "9. Testing Gemini API Connection..."
GEMINI_TEST=$(php artisan tinker --execute="
try {
    \$key = config('services.gemini.api_key');
    if (empty(\$key)) {
        echo 'NO_KEY';
    } else {
        echo 'KEY_SET:' . strlen(\$key);
    }
} catch (Exception \$e) {
    echo 'ERROR';
}
" 2>&1)

if echo "$GEMINI_TEST" | grep -q "KEY_SET"; then
    KEY_LENGTH=$(echo "$GEMINI_TEST" | grep -oP 'KEY_SET:\K\d+')
    echo -e "   ${GREEN}✓ Gemini API key configured ($KEY_LENGTH chars)${NC}"
    echo "   Testing API call..."
    
    # Test actual API call (this might take a few seconds)
    API_TEST=$(timeout 10 php artisan tinker --execute="
    try {
        \$service = app(\App\Services\GeminiService::class);
        \$result = \$service->generateCopywriting([
            'category' => 'social_media',
            'subcategory' => 'caption',
            'brief' => 'Test produk makanan enak',
            'tone' => 'casual',
            'platform' => 'instagram'
        ]);
        echo 'SUCCESS';
    } catch (Exception \$e) {
        echo 'FAILED:' . \$e->getMessage();
    }
    " 2>&1)
    
    if echo "$API_TEST" | grep -q "SUCCESS"; then
        echo -e "   ${GREEN}✓ Gemini API call successful${NC}"
    else
        echo -e "   ${RED}✗ Gemini API call failed${NC}"
        ERROR_MSG=$(echo "$API_TEST" | grep -oP 'FAILED:\K.*')
        echo "   Error: $ERROR_MSG"
        echo "   Check API key validity at: https://aistudio.google.com/app/apikey"
    fi
elif echo "$GEMINI_TEST" | grep -q "NO_KEY"; then
    echo -e "   ${RED}✗ Gemini API key not configured${NC}"
    echo "   Add GEMINI_API_KEY to .env file"
else
    echo -e "   ${RED}✗ Error checking Gemini API${NC}"
fi
echo ""

# 10. Check Logs for Recent Errors
echo "10. Checking Recent Errors..."
if [ -f "storage/logs/laravel.log" ]; then
    ERROR_COUNT=$(tail -100 storage/logs/laravel.log | grep -c "ERROR")
    if [ $ERROR_COUNT -gt 0 ]; then
        echo -e "   ${YELLOW}⚠ Found $ERROR_COUNT errors in last 100 log lines${NC}"
        echo "   Recent errors:"
        tail -100 storage/logs/laravel.log | grep "ERROR" | tail -3 | sed 's/^/   /'
        echo ""
        echo "   View full log: tail -f storage/logs/laravel.log"
    else
        echo -e "   ${GREEN}✓ No recent errors in log${NC}"
    fi
else
    echo -e "   ${YELLOW}⚠ Log file not found${NC}"
fi
echo ""

# 11. Check Cache
echo "11. Checking Cache Status..."
if [ -f "bootstrap/cache/config.php" ]; then
    echo -e "   ${GREEN}✓ Config cached${NC}"
else
    echo -e "   ${YELLOW}⚠ Config not cached${NC}"
    echo "   Run: php artisan config:cache"
fi

if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo -e "   ${GREEN}✓ Routes cached${NC}"
else
    echo -e "   ${YELLOW}⚠ Routes not cached${NC}"
    echo "   Run: php artisan route:cache"
fi
echo ""

# 12. Check Services
echo "12. Checking Services Status..."

# Check PHP-FPM
if systemctl is-active --quiet php8.2-fpm 2>/dev/null || systemctl is-active --quiet php-fpm 2>/dev/null; then
    echo -e "   ${GREEN}✓ PHP-FPM is running${NC}"
else
    echo -e "   ${YELLOW}⚠ Cannot check PHP-FPM status (may need root)${NC}"
fi

# Check Nginx
if systemctl is-active --quiet nginx 2>/dev/null; then
    echo -e "   ${GREEN}✓ Nginx is running${NC}"
else
    echo -e "   ${YELLOW}⚠ Cannot check Nginx status (may need root)${NC}"
fi

# Check MySQL
if systemctl is-active --quiet mysql 2>/dev/null || systemctl is-active --quiet mariadb 2>/dev/null; then
    echo -e "   ${GREEN}✓ MySQL/MariaDB is running${NC}"
else
    echo -e "   ${YELLOW}⚠ Cannot check MySQL status (may need root)${NC}"
fi
echo ""

# Summary
echo "=========================================="
echo "Health Check Complete!"
echo "=========================================="
echo ""
echo "Quick Fixes:"
echo ""
echo "If Gemini API failed:"
echo "  1. Get new API key: https://aistudio.google.com/app/apikey"
echo "  2. Edit .env: nano .env"
echo "  3. Add: GEMINI_API_KEY=your_key_here"
echo "  4. Clear cache: php artisan config:clear && php artisan config:cache"
echo ""
echo "If assets not built:"
echo "  npm install && npm run build"
echo ""
echo "If permissions wrong:"
echo "  chown -R www:www /www/wwwroot/pintar-menulis"
echo "  chmod -R 775 storage bootstrap/cache"
echo ""
echo "View logs:"
echo "  tail -f storage/logs/laravel.log"
echo ""
