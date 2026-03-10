# Daily Articles System - Quick Reference

## Article Generation Schedule

**Rotation Pattern (Every 3 Days):**
```
Day 1 → Industry Article (random industry)
Day 2 → Digital Marketing Tips
Day 3 → Inspirational Quote
(repeats)
```

**Automatic Execution:**
- Time: 00:00 (midnight) daily
- Method: Laravel Schedule
- Location: `routes/console.php`

**Manual Testing:**
```bash
php artisan schedule:run
```

---

## Public Routes

| Route | Purpose |
|-------|---------|
| `GET /articles` | List all articles |
| `GET /articles/{slug}` | View single article |
| `GET /sitemap.xml` | SEO sitemap |
| `GET /robots.txt` | Search engine directives |
| `GET /api/articles` | API - List articles |
| `GET /api/articles/{slug}` | API - Get article |
| `GET /api/articles/category/{category}` | API - By category |
| `GET /api/articles/industry/{industry}` | API - By industry |
| `GET /api/articles/today` | API - Today's articles |

---

## Admin Routes

| Route | Purpose |
|-------|---------|
| `GET /admin/ad-placements` | View all ad placements |
| `PUT /admin/ad-placements/{id}` | Update ad code |
| `PATCH /admin/ad-placements/{id}/toggle` | Toggle active status |

---

## Ad Placement Locations

1. **article_list_top** - Top of article list page
2. **article_list_bottom** - Bottom of article list page
3. **article_detail_top** - Top of article detail page
4. **article_detail_middle** - Middle of article detail page
5. **article_detail_bottom** - Bottom of article detail page

---

## SEO Features

### Meta Tags
- ✅ Title, description, keywords
- ✅ Canonical URLs
- ✅ Open Graph tags
- ✅ Twitter Card tags

### Structured Data
- ✅ Article schema (JSON-LD)
- ✅ Breadcrumb schema
- ✅ Organization schema

### Files
- ✅ `/sitemap.xml` - Article sitemap
- ✅ `/robots.txt` - Search engine directives

---

## Database Tables

### articles (modified)
```sql
- id
- title
- slug
- content
- description (NEW)
- keywords (NEW)
- canonical_url (NEW)
- category (industry, tips, quote)
- industry
- day_number
- created_at
- updated_at
```

### ad_placements (new)
```sql
- id
- location (unique)
- ad_code
- is_active
- created_at
- updated_at
```

---

## Key Files

### Services
- `app/Services/ArticleGeneratorService.php` - Article generation logic

### Models
- `app/Models/Article.php` - Article model with SEO
- `app/Models/AdPlacement.php` - Ad placement model

### Controllers
- `app/Http/Controllers/ArticleController.php` - Public article views
- `app/Http/Controllers/ArticleApiController.php` - API endpoints
- `app/Http/Controllers/Admin/AdPlacementController.php` - Ad management
- `app/Http/Controllers/SitemapController.php` - Sitemap generation

### Helpers
- `app/Helpers/SeoHelper.php` - SEO utility functions

### Views
- `resources/views/articles/index.blade.php` - Article list
- `resources/views/articles/show.blade.php` - Article detail
- `resources/views/admin/ad-placements/index.blade.php` - Ad management

### Configuration
- `routes/console.php` - Schedule configuration
- `routes/web.php` - Web routes
- `public/robots.txt` - Search engine directives

---

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Set Up Cron Job
Add to your server's crontab:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Configure Ad Placements
1. Go to `/admin/ad-placements`
2. For each location, paste your Google AdSense code
3. Toggle "Active" to enable
4. Save changes

### 4. Test Article Generation
```bash
php artisan schedule:run
```

---

## Article Types

### Industry Article
- **Frequency**: Every 3 days (Day 1)
- **Industries**: fashion, food, beauty, tech, lifestyle, health
- **Content**: Comprehensive industry insights and trends
- **Length**: 300-500 words

### Tips Article
- **Frequency**: Every 3 days (Day 2)
- **Topic**: Digital marketing tips
- **Content**: 5-7 practical, actionable tips
- **Format**: Numbered list with explanations
- **Length**: 300-500 words

### Quote Article
- **Frequency**: Every 3 days (Day 3)
- **Topic**: Inspirational quotes for creators
- **Content**: Quote + author + explanation
- **Length**: 150-250 words

---

## Monitoring

### Check Generated Articles
```bash
php artisan tinker
>>> App\Models\Article::latest()->first();
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Verify Schedule
```bash
php artisan schedule:list
```

---

## Troubleshooting

### Articles Not Generating
1. Check cron job is running: `ps aux | grep schedule:run`
2. Check logs: `storage/logs/laravel.log`
3. Test manually: `php artisan schedule:run`

### Ads Not Displaying
1. Check ad placement is active in admin panel
2. Verify ad code is valid
3. Check browser console for errors

### SEO Issues
1. Verify meta tags in page source
2. Check structured data: `https://schema.org/validator`
3. Test sitemap: `/sitemap.xml`

---

## API Examples

### Get All Articles
```bash
curl https://yoursite.com/api/articles
```

### Get Article by Slug
```bash
curl https://yoursite.com/api/articles/latest-trends-in-fashion-industry
```

### Get Today's Articles
```bash
curl https://yoursite.com/api/articles/today
```

### Get by Category
```bash
curl https://yoursite.com/api/articles/category/tips
```

### Get by Industry
```bash
curl https://yoursite.com/api/articles/industry/tech
```

---

## Performance Notes

- Articles are generated once per day at midnight
- No real-time generation needed
- Sitemap is generated on-demand (cached recommended)
- Ad codes are stored in database (no external calls)
- SEO tags are generated server-side

---

## Security Notes

- Article routes are public (no auth required)
- Admin routes require admin role
- Ad codes are stored securely in database
- No sensitive data in articles
- Robots.txt prevents admin area indexing

---

## Future Enhancements

- [ ] Article scheduling (publish at specific times)
- [ ] Multiple article types per day
- [ ] Article categories/tags
- [ ] Comment system
- [ ] Social sharing
- [ ] Email notifications
- [ ] Analytics tracking
- [ ] A/B testing for ads
