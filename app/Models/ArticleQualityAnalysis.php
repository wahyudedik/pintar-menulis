<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArticleQualityAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'seo_score',
        'readability_score',
        'engagement_score',
        'keyword_optimization',
        'strengths',
        'improvements',
        'suggested_title',
        'meta_description',
        'internal_links',
        'external_links',
        'overall_quality',
        'full_analysis',
    ];

    protected $casts = [
        'strengths' => 'json',
        'improvements' => 'json',
        'internal_links' => 'json',
        'external_links' => 'json',
        'full_analysis' => 'json',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
