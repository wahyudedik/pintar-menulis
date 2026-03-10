#!/bin/bash

# 🚀 Pintar Menulis - Production Deployment Script
# Usage: bash deploy.sh

set -e  # Exit on error

echo "🚀 Starting Pintar Menulis Deployment..."
echo "========================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DB_USER="your_db_user"
DB_NAME="pintar_menulis"
BACKUP_DIR="backups"

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Step 1: Backup
echo -e "${YELLOW}📦 Step 1: Creating backup...${NC}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/backup_${TIMESTAMP}.sql"

# Backup database
echo "   Backing up database to ${BACKUP_FILE}..."
mysqldump -u $DB_USER -p $DB_NAME > $BACKUP_FILE
echo -e "${GREEN}   ✅ Database backup created${NC}"

# Backup .env
cp .env .env.backup_${TIMESTAMP}
echo -e "${GREEN}   ✅ .env backup created${NC}"
echo ""

# Step 2: Maintenance Mode
echo -e "${YELLOW}🔧 Step 2: Enabling maintenance mode...${NC}"
php artisan down --refresh=15 --secret="deploy-$(date +%s)"
echo -e "${GREEN}   ✅ Maintenance mode enabled${NC}"
echo ""

# Step 3: Pull Code
echo -e "${YELLOW}📥 Step 3: Pulling latest code...${NC}"
git pull origin main
echo -e "${GREEN}   ✅ Code updated${NC}"
echo ""

# Step 4: Dependencies
echo -e "${YELLOW}📦 Step 4: Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}   ✅ Dependencies installed${NC}"
echo ""

# Step 5: Migrations
echo -e "${YELLOW}🗄️  Step 5: Running migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}   ✅ Migrations completed${NC}"
echo ""

# Step 6: Seed Hashtags
echo -e "${YELLOW}🏷️  Step 6: Seeding hashtags...${NC}"
php artisan db:seed --class=TrendingHashtagSeeder --force
HASHTAG_COUNT=$(php artisan tinker --execute="echo App\Models\TrendingHashtag::count();")
echo "   Hashtags in database: ${HASHTAG_COUNT}"
echo -e "${GREEN}   ✅ Hashtags seeded${NC}"
echo ""

# Step 7: Clear Cache
echo -e "${YELLOW}🧹 Step 7: Clearing cache...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}   ✅ Cache cleared${NC}"
echo ""

# Step 8: Rebuild Cache
echo -e "${YELLOW}⚡ Step 8: Rebuilding cache...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}   ✅ Cache rebuilt${NC}"
echo ""

# Step 9: Optimize
echo -e "${YELLOW}⚡ Step 9: Optimizing...${NC}"
composer dump-autoload --optimize
php artisan optimize
echo -e "${GREEN}   ✅ Optimization completed${NC}"
echo ""

# Step 10: Disable Maintenance
echo -e "${YELLOW}✅ Step 10: Disabling maintenance mode...${NC}"
php artisan up
echo -e "${GREEN}   ✅ Site is now live!${NC}"
echo ""

# Summary
echo "========================================"
echo -e "${GREEN}🎉 Deployment Completed Successfully!${NC}"
echo "========================================"
echo ""
echo "📊 Summary:"
echo "   - Backup: ${BACKUP_FILE}"
echo "   - Hashtags: ${HASHTAG_COUNT}"
echo "   - Time: $(date)"
echo ""
echo "🔍 Next Steps:"
echo "   1. Monitor logs: tail -f storage/logs/laravel.log"
echo "   2. Test features: https://your-domain.com/ai-generator"
echo "   3. Check hashtags: php artisan hashtags:update --platform=instagram"
echo ""
echo -e "${YELLOW}⚠️  Remember to monitor for the next 24 hours!${NC}"
