# Quick Reference Guide - Pintar Menulis

## 🚀 Quick Start

### Local Development:
```bash
# Start server
php artisan serve

# Watch assets
npm run dev

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Production Deployment:
```bash
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

# Set permissions
chmod -R 775 storage bootstrap/cache
```

---

## 🔑 Important URLs

### Production:
- Website: https://noteds.com
- Admin Panel: https://noteds.com/admin
- API Endpoint: https://noteds.com/api

### Development:
- Local: http://localhost:8000
- AI Generator: http://localhost:8000/ai-generator
- Analytics: http://localhost:8000/analytics

### External Services:
- Gemini API Key: https://aistudio.google.com/app/apikey
- WhatsApp Operator: https://wa.me/6281654932383

---

## 📧 Contact Information

**All Emails**: info@noteds.com
- Support inquiries
- Privacy questions
- Refund requests
- Partnership inquiries
- General questions

**WhatsApp**: +62 816-5493-2383
- Operator registration only

---

## 🔧 Common Commands

### Artisan Commands:
```bash
# Check Gemini models
php artisan gemini:list-models

# Test Gemini API
php artisan gemini:test

# Clear all caches
php artisan optimize:clear

# Generate app key
php artisan key:generate
```

### Database:
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh database (WARNING: deletes all data)
php artisan migrate:fresh --seed
```

### Queue (if using):
```bash
# Run queue worker
php artisan queue:work

# Restart queue workers
php artisan queue:restart
```

---

## 🐛 Troubleshooting

### AI Generator Error 500:
1. Check API key source (must be from AI Studio)
2. Verify `.env` has `GEMINI_API_KEY`
3. Run: `php artisan config:clear`
4. Check logs: `tail -f storage/logs/laravel.log`

### Analytics Not Showing Data:
- This is normal! Users need to manually input metrics
- Check info box on analytics page for instructions
- Not a bug - manual input by design

### Permission Errors:
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Error:
1. Check `.env` database credentials
2. Verify database exists
3. Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

---

## 📊 Key Database Tables

### Users & Auth:
- `users` - All users (client, operator, admin, guru)
- `password_reset_tokens` - Password resets
- `sessions` - User sessions

### AI & Content:
- `caption_histories` - All generated captions (for anti-repetition)
- `caption_analytics` - Performance metrics (manual input)
- `brand_voices` - User brand voice preferences

### Orders & Payments:
- `orders` - All orders between clients and operators
- `order_revisions` - Revision requests
- `payments` - Payment transactions
- `withdrawal_requests` - Operator withdrawal requests

### Platform:
- `packages` - Service packages
- `payment_settings` - Payment gateway settings
- `notifications` - User notifications
- `feedback` - User feedback

### ML Training:
- `ml_training_data` - Curated data for ML training
- `ml_model_versions` - ML model version tracking

---

## 🎨 UI Components

### Layouts:
- `layouts.app` - Main app layout
- `layouts.client` - Client dashboard layout
- `layouts.operator` - Operator dashboard layout
- `layouts.admin` - Admin dashboard layout
- `layouts.guest` - Guest/auth pages layout

### Common Components:
- `components.button` - Reusable buttons
- `components.input` - Form inputs
- `components.modal` - Modal dialogs
- `components.alert` - Alert messages

---

## 🔐 User Roles

### Client (role: client):
- Generate AI captions
- Order from operators
- Track analytics
- Manage projects
- Save brand voice

### Operator (role: operator):
- Accept orders
- Complete orders
- Request withdrawals
- Manage profile

### Admin (role: admin):
- Manage all users
- Manage packages
- Approve withdrawals
- View reports
- Handle feedback

### Guru (role: guru):
- Review ML training data
- Curate caption examples
- Manage model versions

---

## 📈 Analytics Metrics

### Engagement Rate Formula:
```
(Likes + Comments + Shares + Saves) / Reach × 100%
```

### Benchmarks:
- < 1%: Poor
- 1-3%: Average
- 3-5%: Good
- 5-10%: Very Good
- > 10%: Excellent

### Successful Caption Criteria:
- Engagement rate > 5%
- OR marked as successful by user
- OR user rating >= 4 stars

---

## 🤖 AI Configuration

### Models Available:
- `gemini-2.0-flash-exp` (current, stable)
- `gemini-2.5-flash` (alternative)

### Temperature Settings:
- Default: 0.7 (balanced)
- Frequent users (10+): 0.8 (more creative)
- Power users (20+): 0.9 (very creative)

### Token Limits:
- 5 variations: 4096 tokens
- 20 variations: 8192 tokens

---

## 📱 Platform Features

### AI Generator:
- 5 variations (default) or 20 (premium)
- Multiple categories (social media, quick templates, industry presets)
- Platform-specific (Instagram, TikTok, Facebook, LinkedIn, Twitter)
- Tone options (casual, formal, funny, persuasive, emotional, educational)
- Auto hashtag generation
- Local language support
- Anti-repetition system

### Analytics:
- Manual input (by design)
- Track: likes, comments, shares, saves, reach, impressions, clicks
- Auto-calculate engagement rate
- Charts: platform performance, category performance, engagement over time
- Export: PDF, CSV
- Top performing captions

### Order System:
- Client creates order request
- Operator accepts & completes
- Revision system
- Escrow payment (10% platform fee)
- Rating & review

---

## 🎯 Quick Fixes

### Clear Everything:
```bash
php artisan optimize:clear
composer dump-autoload
npm run build
```

### Reset Config:
```bash
php artisan config:clear
php artisan config:cache
```

### Fix Permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Check Health:
```bash
./check-production.sh
```

---

**Need Help?** 
- Check logs: `storage/logs/laravel.log`
- Email: info@noteds.com
- Documentation: See `CONTEXT_TRANSFER_SUMMARY.md`
