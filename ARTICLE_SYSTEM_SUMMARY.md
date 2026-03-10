# Daily Article Auto-Generation System - Implementation Summary

## Overview
A comprehensive daily article auto-generation system that generates 7 unique articles daily (2 captions, 2 quotes, 2 tips, 1 mixed) using Gemini API, with web and API routes for displaying articles.

---

## Files Created

### 1. Database Migration
**File:** `database/migrations/2026_03_10_180856_create_articles_table.php`
- Creates `articles` table with fields:
  - `id` (primary key)
  - `title` (string)
  - `slug` (string)
  - `content` (longText)
  - `category` (enum: caption, quote, tips)
  - `industry` (string, default: general)
  - `day_number` (integer, default: 1)
  - `created_at`, `updated_at` (timestamps)
- Unique constraint on `(slug, day_number)` to prevent duplicates
- Indexes on: category, industry, day_number, created_at

### 2. Models
**File:** `app/Models/Article.php`
- Eloquent model with relationships and query scopes
- Scopes:
  - `byCategory($category)` - Filter by category
  - `byIndustry($industry)` - Filter by industry
  - `today()` - Get today's articles
  - `byDayNumber($dayNumber)` - Filter by day number
  - `bySlug($slug)` - Get article by slug
  - `recent($days)` - Get recently generated articles
- Helper methods:
  - `existsBySlugAndDay($slug, $dayNumber)` - Check for duplicates

### 3. Services
**File:** `app/Services/ArticleGeneratorService.php`
- Main service for generating articles
- Key methods:
  - `generateDailyArticles()` - Generate 7 articles daily
  - `generateArticle($category, $industry, $dayNumber)` - Generate single article
  - `generateContent($category, $industry, $recentContent)` - Use Gemini API
  - `buildContentPrompt($category, $industry, $recentContent)` - Build AI prompt
  - `generateTitle($category, $industry)` - Generate article title
  - `generateUniqueSlug($title, $dayNumber)` - Generate unique slug
  - `getCurrentDayNumber()` - Get current day (1-5)
  - `getRandomIndustry()` - Select random industry
  - `getRandomCategory()` - Select random category
- Supports industries: fashion, food, beauty, technology, lifestyle, health
- Tracks previously generated content to avoid duplicates
- Returns detailed results with success/failure counts

### 4. Console Commands
**File:** `app/Console/Commands/GenerateDailyArticles.php`
- Command: `articles:generate-daily`
- Runs ArticleGeneratorService
- Logs generation results
- Handles errors gracefully
- Scheduled to run daily at 00:00 (midnight)

### 5. Controllers

#### ArticleController (Web)
**File:** `app/Http/Controllers/ArticleController.php`
- Routes:
  - `index()` - Display paginated article listing (12 per page)
  - `show($slug)` - Display article detail with related articles

#### ArticleApiController (API)
**File:** `app/Http/Controllers/ArticleApiController.php`
- Routes:
  - `index()` - Get all articles with pagination (supports filtering)
  - `show($slug)` - Get article by slug
  - `today()` - Get today's articles grouped by category
  - `byCategory($category)` - Get articles by category
  - `byIndustry($industry)` - Get articles by industry
- Returns JSON responses with pagination metadata

### 6. Views

#### Article Listing
**File:** `resources/views/articles/index.blade.php`
- Grid layout (1 col mobile, 2 cols tablet, 3 cols desktop)
- Article cards with:
  - Category and industry badges
  - Title and content preview
  - Publication date and day number
  - "Read More" link
- Pagination support
- Empty state message

#### Article Detail
**File:** `resources/views/articles/show.blade.php`
- Full article content
- Category and industry badges
- Meta information (date, day, word count)
- Copy to clipboard button
- Related articles section (3 related articles)
- Back to articles link

#### Welcome Page Update
**File:** `resources/views/welcome.blade.php` (modified)
- Added "Artikel" navigation link in header
- Added "Today's Articles" section before footer
- Shows 1 article per category (caption, quote, tips)
- Links to full articles listing
- Empty state when no articles available

---

## Routes

### Web Routes
```
GET  /articles                    → ArticleController@index    (articles.index)
GET  /articles/{slug}             → ArticleController@show     (articles.show)
```

### API Routes
```
GET  /api/articles                → ArticleApiController@index           (api.articles.index)
GET  /api/articles/today          → ArticleApiController@today           (api.articles.today)
GET  /api/articles/category/{cat} → ArticleApiController@byCategory     (api.articles.by-category)
GET  /api/articles/industry/{ind} → ArticleApiController@byIndustry     (api.articles.by-industry)
GET  /api/articles/{slug}         → ArticleApiController@show            (api.articles.show)
```

---

## Scheduler

**File:** `routes/console.php` (modified)
- Command scheduled to run daily at 00:00 (midnight)
- Schedule name: `articles-generate-daily`
- Runs without overlapping
- Runs on one server only

---

## How It Works

### Daily Generation Flow
1. **Scheduler** triggers at 00:00 (midnight)
2. **GenerateDailyArticles command** executes
3. **ArticleGeneratorService** generates 7 articles:
   - 2 captions
   - 2 quotes
   - 2 tips
   - 1 mixed (random category)
4. For each article:
   - Randomly select industry
   - Get recent articles to avoid duplicates
   - Generate content using Gemini API
   - Generate title and unique slug
   - Check for duplicates
   - Save to database
5. Log results with success/failure counts

### Content Generation
- Uses Gemini API via existing `GeminiService`
- Builds context-aware prompts based on category and industry
- Includes recent content to ensure uniqueness
- Generates 50-200 word content depending on category

### Duplicate Prevention
- Unique constraint on `(slug, day_number)`
- Tracks recently generated content (last 30 days)
- Regenerates if duplicate detected
- Prevents similar content in same industry/category

---

## Features

✅ **Automated Daily Generation** - 7 articles generated at midnight
✅ **Multiple Categories** - Captions, quotes, tips, mixed
✅ **Industry Support** - 6 industries (fashion, food, beauty, tech, lifestyle, health)
✅ **Duplicate Prevention** - Unique constraints and content tracking
✅ **Web Interface** - Browse and read articles
✅ **API Endpoints** - JSON API for integration
✅ **Pagination** - 12 articles per page on web, 15 on API
✅ **Filtering** - By category and industry
✅ **Related Articles** - Show similar content
✅ **Copy to Clipboard** - Easy content sharing
✅ **Landing Page Integration** - Today's articles on homepage
✅ **Error Handling** - Graceful error logging and reporting

---

## Usage

### Manual Generation (Testing)
```bash
php artisan articles:generate-daily
```

### View Articles
- Web: `http://yourapp.com/articles`
- Detail: `http://yourapp.com/articles/{slug}`

### API Usage
```bash
# Get all articles
GET /api/articles

# Get today's articles
GET /api/articles/today

# Get by category
GET /api/articles/category/caption

# Get by industry
GET /api/articles/industry/fashion

# Get specific article
GET /api/articles/{slug}
```

---

## Database Schema

```sql
CREATE TABLE articles (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  content LONGTEXT NOT NULL,
  category ENUM('caption', 'quote', 'tips') NOT NULL,
  industry VARCHAR(255) DEFAULT 'general',
  day_number INT DEFAULT 1,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE KEY unique_slug_day (slug, day_number),
  INDEX idx_category (category),
  INDEX idx_industry (industry),
  INDEX idx_day_number (day_number),
  INDEX idx_created_at (created_at)
);
```

---

## Configuration

### Industries
- fashion
- food
- beauty
- technology
- lifestyle
- health

### Categories
- caption (Instagram captions)
- quote (Inspirational quotes)
- tips (Practical tips)
- mixed (Random category)

### Day Numbers
- 1-5 (Monday-Friday)
- Sunday maps to Friday (5)

---

## Dependencies

- Laravel 11+
- Gemini API (via existing GeminiService)
- Tailwind CSS (for views)

---

## Files Modified

1. `routes/web.php` - Added article routes
2. `routes/console.php` - Added scheduler
3. `resources/views/welcome.blade.php` - Added navigation and today's articles section

---

## Files Created

1. `database/migrations/2026_03_10_180856_create_articles_table.php`
2. `app/Models/Article.php`
3. `app/Services/ArticleGeneratorService.php`
4. `app/Console/Commands/GenerateDailyArticles.php`
5. `app/Http/Controllers/ArticleController.php`
6. `app/Http/Controllers/ArticleApiController.php`
7. `resources/views/articles/index.blade.php`
8. `resources/views/articles/show.blade.php`

---

## Next Steps (Optional)

1. **Email Notifications** - Send daily digest to subscribers
2. **Social Media Integration** - Auto-post to social platforms
3. **Advanced Analytics** - Track article performance
4. **User Preferences** - Let users customize article types
5. **Multi-language Support** - Generate in different languages
6. **Image Generation** - Add AI-generated images to articles
7. **SEO Optimization** - Add meta tags and structured data
8. **Caching** - Cache popular articles for performance

---

## Testing

To test the system:

```bash
# Run migration
php artisan migrate

# Generate articles manually
php artisan articles:generate-daily

# Check articles in database
php artisan tinker
>>> App\Models\Article::count()

# View articles
# Visit: http://yourapp.com/articles
# API: http://yourapp.com/api/articles/today
```

---

## Support

For issues or questions about the article generation system, check:
- Application logs: `storage/logs/laravel.log`
- Database: `articles` table
- Gemini API status and rate limits
