# Daily Article System - Complete Rebuild

## Summary of Changes

The daily article system has been completely rebuilt with the following corrected requirements:

### 1. ARTICLE GENERATION (1 per day, rotating)

**Rotation Pattern (3-day cycle):**
- **Day 1**: Industry article (random from: fashion, food, beauty, tech, lifestyle, health)
- **Day 2**: Tips article (digital marketing tips)
- **Day 3**: Quote article (inspirational quote)
- Pattern repeats every 3 days

**Implementation:**
- Updated `ArticleGeneratorService` to generate 1 article per day
- Uses day-of-year calculation to determine position in 3-day cycle
- Each article type has dedicated generation method
- Uses Laravel Schedule (not Jobs) - more proper approach
- Runs daily at 00:00 (midnight) via `routes/console.php`

**Files Modified:**
- `app/Services/ArticleGeneratorService.php` - Complete rewrite
- `routes/console.php` - Updated to use Schedule instead of Jobs

---

### 2. PUBLIC ACCESS

**Changes:**
- Article routes remain public (no auth middleware)
- All users can view articles without login
- Public API endpoints available at `/api/articles/*`
- Sitemap accessible at `/sitemap.xml`

**Files Modified:**
- `routes/web.php` - Routes already public, added sitemap route

---

### 3. SEO OPTIMIZATION

**Meta Tags Added:**
- Title, description, keywords
- Open Graph tags (og:title, og:description, og:type, og:url, og:image, etc.)
- Twitter Card tags (twitter:card, twitter:title, twitter:description, twitter:image)
- Canonical URLs
- Breadcrumb schema

**Structured Data (JSON-LD):**
- Article schema with headline, description, image, dates, author, publisher
- Breadcrumb schema for navigation
- Organization schema for site-wide SEO

**Additional SEO Features:**
- Sitemap.xml generation at `/sitemap.xml`
- robots.txt file with proper directives
- SEO-friendly slug generation
- Meta description generation from content
- Keyword extraction from title and content

**Files Created:**
- `app/Helpers/SeoHelper.php` - SEO helper functions
- `app/Http/Controllers/SitemapController.php` - Sitemap generation
- `public/robots.txt` - Search engine directives

**Files Modified:**
- `app/Models/Article.php` - Added SEO fields and accessors
- `resources/views/articles/show.blade.php` - Added SEO meta tags and structured data
- `resources/views/articles/index.blade.php` - Added SEO meta tags
- `resources/views/layouts/app.blade.php` - Added @yield('head') for SEO tags
- `routes/web.php` - Added sitemap route

**Database Migration:**
- `database/migrations/2026_03_11_000000_add_seo_fields_to_articles.php`
  - Added `description` field (text)
  - Added `keywords` field (text)
  - Added `canonical_url` field (string)

---

### 4. GOOGLE ADSENSE INTEGRATION

**Ad Placement Locations:**
1. `article_list_top` - Top of article list page
2. `article_list_bottom` - Bottom of article list page
3. `article_detail_top` - Top of article detail page
4. `article_detail_middle` - Middle of article detail page
5. `article_detail_bottom` - Bottom of article detail page

**Admin Panel Features:**
- View all ad placements
- Add/edit Google AdSense code for each location
- Toggle placement active/inactive status
- No code changes needed to add/remove ads

**Files Created:**
- `app/Models/AdPlacement.php` - Ad placement model
- `app/Http/Controllers/Admin/AdPlacementController.php` - Admin controller
- `resources/views/admin/ad-placements/index.blade.php` - Admin view
- `database/migrations/2026_03_11_000001_create_ad_placements_table.php` - Database table

**Files Modified:**
- `resources/views/articles/show.blade.php` - Added ad placement display
- `resources/views/articles/index.blade.php` - Added ad placement display
- `resources/views/layouts/admin.blade.php` - Added ad placements menu item
- `routes/web.php` - Added admin routes for ad management

---

### 5. MODIFIED EXISTING FILES

**ArticleGeneratorService** (`app/Services/ArticleGeneratorService.php`)
- Generates 1 article per day instead of 7
- Implements 3-day rotation pattern
- Separate methods for each article type
- Uses day-of-year calculation for cycle position
- Improved logging

**routes/console.php**
- Changed from Job-based to Schedule-based approach
- Uses `Schedule::call()` with closure
- Runs at 00:00 daily
- Includes logging for success/failure

**Article Model** (`app/Models/Article.php`)
- Added SEO fields: `description`, `keywords`, `canonical_url`
- Added accessors for SEO data
- Maintains existing scopes and methods

**Article Views**
- `resources/views/articles/show.blade.php` - Added SEO meta tags, structured data, ad placements
- `resources/views/articles/index.blade.php` - Added SEO meta tags, ad placements
- `resources/views/layouts/app.blade.php` - Added @yield('head') support

**Routes** (`routes/web.php`)
- Added sitemap route: `GET /sitemap.xml`
- Added admin ad placement routes:
  - `GET /admin/ad-placements` - List placements
  - `PUT /admin/ad-placements/{placement}` - Update placement
  - `PATCH /admin/ad-placements/{placement}/toggle` - Toggle active status

---

### 6. CREATED NEW FILES

**Models:**
- `app/Models/AdPlacement.php` - Ad placement model with helper methods

**Controllers:**
- `app/Http/Controllers/Admin/AdPlacementController.php` - Admin ad management
- `app/Http/Controllers/SitemapController.php` - Sitemap generation

**Helpers:**
- `app/Helpers/SeoHelper.php` - SEO utility functions

**Views:**
- `resources/views/admin/ad-placements/index.blade.php` - Admin ad management interface

**Migrations:**
- `database/migrations/2026_03_11_000000_add_seo_fields_to_articles.php` - SEO fields
- `database/migrations/2026_03_11_000001_create_ad_placements_table.php` - Ad placements table

**Public Files:**
- `public/robots.txt` - Search engine directives

---

### 7. KEPT EXISTING

- Article model (enhanced with SEO fields)
- Article controller and API controller
- Article views (enhanced with SEO and ads)
- Landing page integration
- Database structure (enhanced with SEO fields)
- All existing routes and functionality

---

## How to Use

### Generate Articles Manually (for testing)
```bash
php artisan schedule:run
```

### Access Admin Panel
1. Go to `/admin/ad-placements`
2. For each location, add your Google AdSense code
3. Toggle the placement to "Active"
4. Save changes

### View Articles
- Public list: `/articles`
- Individual article: `/articles/{slug}`
- API: `/api/articles`

### SEO Features
- Sitemap: `/sitemap.xml`
- Robots: `/robots.txt`
- Each article has full SEO meta tags and structured data

---

## Database Changes

### New Tables
- `ad_placements` - Stores ad codes and placement configuration

### Modified Tables
- `articles` - Added `description`, `keywords`, `canonical_url` fields

---

## Schedule Configuration

The article generation runs automatically via Laravel Schedule:
- **Time**: 00:00 (midnight) daily
- **Frequency**: Once per day
- **Rotation**: 3-day cycle (Industry → Tips → Quote)
- **Logging**: All generation attempts are logged

To ensure the schedule runs, add this to your crontab:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Testing the System

1. **Test Article Generation:**
   ```bash
   php artisan schedule:run
   ```

2. **Check Generated Articles:**
   - Visit `/articles` to see the list
   - Click on an article to view details with SEO tags

3. **Test Ad Placements:**
   - Go to `/admin/ad-placements`
   - Add test ad codes
   - Toggle active status
   - View articles to see ads displayed

4. **Verify SEO:**
   - Check page source for meta tags
   - Verify structured data in JSON-LD blocks
   - Check `/sitemap.xml` for article URLs
   - Verify `/robots.txt` directives

---

## Key Features

✅ 1 article per day with 3-day rotation pattern
✅ Industry, Tips, and Quote article types
✅ Uses Laravel Schedule (proper approach)
✅ Runs at midnight (00:00) daily
✅ Public access (no auth required)
✅ Complete SEO optimization
✅ Google AdSense integration
✅ Admin panel for ad management
✅ Sitemap and robots.txt
✅ JSON-LD structured data
✅ Open Graph and Twitter Card tags
✅ Canonical URLs
✅ Breadcrumb schema

---

## Migration Status

✅ All migrations applied successfully
✅ Database tables created
✅ SEO fields added to articles table
✅ Ad placements table created

---

## Next Steps

1. Run migrations: `php artisan migrate`
2. Set up cron job for schedule
3. Add Google AdSense codes in admin panel
4. Test article generation
5. Monitor logs for any issues
