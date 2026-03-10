<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'description',
        'keywords',
        'category',
        'industry',
        'day_number',
        'canonical_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get articles by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get articles by industry
     */
    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }

    /**
     * Get today's articles
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Get articles by day number
     */
    public function scopeByDayNumber($query, $dayNumber)
    {
        return $query->where('day_number', $dayNumber);
    }

    /**
     * Get article by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug)->first();
    }

    /**
     * Check if article exists by slug and day_number
     */
    public static function existsBySlugAndDay($slug, $dayNumber)
    {
        return self::where('slug', $slug)
            ->where('day_number', $dayNumber)
            ->exists();
    }

    /**
     * Get recently generated articles
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get canonical URL for article
     */
    public function getCanonicalUrlAttribute()
    {
        return $this->canonical_url ?? route('articles.show', $this->slug);
    }

    /**
     * Get SEO meta description
     */
    public function getSeoDescriptionAttribute()
    {
        return $this->description ?? Str::limit(strip_tags($this->content), 160);
    }

    /**
     * Get SEO keywords
     */
    public function getSeoKeywordsAttribute()
    {
        return $this->keywords ?? "{$this->category}, {$this->industry}";
    }
}
