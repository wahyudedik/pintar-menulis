# 📰 Daily Articles System - Quick Start Guide

## ✅ System Status: READY TO USE

Sistem auto-generate artikel harian sudah **fully implemented dan tested**!

---

## 🚀 Quick Start (5 Minutes)

### 1. Run Migration (if not done)
```bash
php artisan migrate
```

### 2. Generate Articles Manually (Testing)
```bash
php artisan articles:generate-daily
```

### 3. View Articles
- **Web:** http://yourapp.com/articles
- **Detail:** http://yourapp.com/articles/{slug}
- **API:** http://yourapp.com/api/articles/today

### 4. Check Landing Page
- Visit: http://yourapp.com
- See "Artikel" menu in navigation
- See "Artikel Hari Ini" section with today's articles

---

## 📋 What's Included

### ✅ Auto-Generation
- 7 articles per day (2 captions, 2 quotes, 2 tips, 1 mixed)
- Scheduled at 00:00 (midnight) daily
- Runs automatically every day

### ✅ Web Interface
- Browse all articles: `/articles`
- Read article detail: `/articles/{slug}`
- Related articles on detail page
- Copy to clipboard button

### ✅ API Endpoints
- Get all articles: `GET /api/articles`
- Get today's articles: `GET /api/articles/today`
- Filter by category: `GET /api/articles/category/{cat}`
- Filter by industry: `GET /api/articles/industry/{ind}`
- Get specific article: `GET /api/articles/{slug}`

### ✅ Landing Page
- "Artikel" menu in navigation
- "Artikel Hari Ini" section showing today's articles
- Links to full articles listing

---

## 🎯 Features

| Feature | Status | Details |
|---------|--------|---------|
| Auto-generation | ✅ | 7 articles daily at 00:00 |
| Multi-category | ✅ | Captions, quotes, tips, mixed |
| Multi-industry | ✅ | Fashion, food, beauty, tech, lifestyle, health |
| Duplicate prevention | ✅ | Unique constraint + content tracking |
| Web interface | ✅ | Browse and read articles |
| API endpoints | ✅ | JSON API for integration |
| Landing page | ✅ | Navigation + today's articles |
| Slug-based URLs | ✅ | SEO-friendly URLs |
| Related articles | ✅ | Show similar content |
| Copy to clipboard | ✅ | Easy content sharing |
| Error handling | ✅ | Graceful error logging |
| Scheduling | ✅ | Daily at midnight |

---

## 📊 Database

### Table: articles
```
id              BIGINT (primary key)
title           VARCHAR(255)
slug            VARCHAR(255)
content         LONGTEXT
category        ENUM('caption', 'quote', 'tips')
industry        VARCHAR(255)
day_number      INT (1-5)
created_at      TIMESTAMP
updated_at      TIMESTAMP

UNIQUE: (slug, day_number)
INDEXES: category, industry, day_number, created_at
```

---

## 🛣️ Routes

### Web Routes
```
GET  /articles                    → List articles
GET  /articles/{slug}             → Show article
```

### API Routes
```
GET  /api/articles                → All articles
GET  /api/articles/today          → Today's articles
GET  /api/articles/category/{cat} → By category
GET  /api/articles/industry/{ind} → By industry
GET  /api/articles/{slug}         → Specific article
```

---

## 🔧 Configuration

### Change Generation Time
Edit `routes/console.php`:
```php
->dailyAt('02:00')  // Change to 2 AM
```

### Add More Industries
Edit `ArticleGeneratorService.php`:
```php
protected $industries = ['fashion', 'food', 'beauty', 'technology', 'lifestyle', 'health', 'sports'];
```

### Change Articles Per Day
Edit `ArticleGeneratorService.php`:
```php
$articlesToGenerate = [
    ['category' => 'caption', 'count' => 3],  // 3 instead of 2
    // ...
];
```

---

## 📁 Files Created

```
database/migrations/2026_03_10_180856_create_articles_table.php
app/Models/Article.php
app/Services/ArticleGeneratorService.php
app/Console/Commands/GenerateDailyArticles.php
app/Http/Controllers/ArticleController.php
app/Http/Controllers/ArticleApiController.php
resources/views/articles/index.blade.php
resources/views/articles/show.blade.php
```

---

## 📝 Files Modified

```
routes/web.php                          (added article routes)
routes/console.php                      (added scheduler)
resources/views/welcome.blade.php       (added navigation + today's articles)
```

---

## 🧪 Testing

### Check Articles in Database
```bash
php artisan tinker
>>> App\Models\Article::count()
>>> App\Models\Article::today()->count()
>>> App\Models\Article::byCategory('caption')->count()
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Test API
```bash
curl http://yourapp.com/api/articles/today
```

---

## 🎨 Landing Page

### Navigation
```
Home | Artikel | Login | Register
```

### Today's Articles Section
- Shows 1 article per category
- Displays before footer
- Links to full articles listing
- Empty state when no articles

---

## 🚨 Troubleshooting

### Articles Not Generating
1. Check scheduler: `php artisan schedule:work`
2. Check API key: `.env` file
3. Check logs: `storage/logs/laravel.log`
4. Run manually: `php artisan articles:generate-daily`

### Routes Not Working
1. Clear cache: `php artisan route:cache`
2. Check routes: `php artisan route:list | grep articles`

### Database Issues
1. Run migration: `php artisan migrate`
2. Check table: `php artisan tinker`

---

## 📚 Documentation

- **DAILY_ARTICLES_SYSTEM_COMPLETE.md** - Full documentation
- **DAILY_ARTICLES_IMPLEMENTATION_SUMMARY.md** - Implementation details
- **ARTICLES_QUICK_START.md** - This file

---

## ✅ Checklist

- ✅ Migration created and executed
- ✅ Model created with scopes
- ✅ Service created with generation logic
- ✅ Command created and scheduled
- ✅ Controllers created (web + API)
- ✅ Views created (index + show)
- ✅ Routes registered (web + API)
- ✅ Scheduler configured (00:00 daily)
- ✅ Landing page updated
- ✅ Error handling implemented
- ✅ Logging implemented
- ✅ Duplicate prevention implemented
- ✅ System tested and working

---

## 🎉 You're All Set!

The daily articles system is **ready to use**!

### Next Steps
1. Visit landing page: http://yourapp.com
2. Click "Artikel" menu
3. Browse today's articles
4. Read full articles with slug URLs
5. Check API endpoints

### Automatic Generation
- Articles generate automatically at **00:00 (midnight) daily**
- 7 new articles every day
- No manual intervention needed
- Check logs for generation status

---

## 📞 Need Help?

1. Check logs: `storage/logs/laravel.log`
2. Run command manually: `php artisan articles:generate-daily`
3. Check database: `php artisan tinker`
4. Review documentation files

---

**System Status: ✅ FULLY OPERATIONAL**

Happy content creation! 🚀
