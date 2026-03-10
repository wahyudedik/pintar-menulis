<?php

namespace App\Helpers;

use App\Models\Article;

class SeoHelper
{
    /**
     * Generate JSON-LD structured data for article
     */
    public static function generateArticleSchema(Article $article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->seo_description,
            'image' => [
                '@type' => 'ImageObject',
                'url' => asset('images/article-default.jpg'),
                'width' => 1200,
                'height' => 630,
            ],
            'datePublished' => $article->created_at->toIso8601String(),
            'dateModified' => $article->updated_at->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png'),
                    'width' => 250,
                    'height' => 60,
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $article->canonical_url,
            ],
        ];
    }

    /**
     * Generate breadcrumb schema
     */
    public static function generateBreadcrumbSchema(Article $article)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => config('app.url'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Articles',
                    'item' => route('articles.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $article->title,
                    'item' => $article->canonical_url,
                ],
            ],
        ];
    }

    /**
     * Generate organization schema
     */
    public static function generateOrganizationSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name'),
            'url' => config('app.url'),
            'logo' => asset('images/logo.png'),
            'description' => 'Daily articles, tips, and inspiration for content creators',
            'sameAs' => [
                'https://www.facebook.com/' . config('app.name'),
                'https://www.twitter.com/' . config('app.name'),
                'https://www.instagram.com/' . config('app.name'),
            ],
        ];
    }

    /**
     * Generate SEO-friendly slug
     */
    public static function generateSlug($title)
    {
        return \Illuminate\Support\Str::slug($title);
    }

    /**
     * Generate meta description from content
     */
    public static function generateMetaDescription($content, $length = 160)
    {
        $text = strip_tags($content);
        return \Illuminate\Support\Str::limit($text, $length, '...');
    }

    /**
     * Generate keywords from title and content
     */
    public static function generateKeywords($title, $content, $count = 5)
    {
        $text = $title . ' ' . strip_tags($content);
        $words = str_word_count($text, 1);
        
        // Remove common words
        $commonWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'is', 'are', 'was', 'were', 'be', 'been', 'being'];
        $words = array_filter($words, function($word) use ($commonWords) {
            return !in_array(strtolower($word), $commonWords) && strlen($word) > 3;
        });

        $keywords = array_slice(array_unique(array_map('strtolower', $words)), 0, $count);
        return implode(', ', $keywords);
    }

    /**
     * Get canonical URL
     */
    public static function getCanonicalUrl($url = null)
    {
        return $url ?? request()->url();
    }

    /**
     * Generate Open Graph meta tags
     */
    public static function generateOpenGraphTags(Article $article)
    {
        return [
            'og:title' => $article->title,
            'og:description' => $article->seo_description,
            'og:type' => 'article',
            'og:url' => $article->canonical_url,
            'og:image' => asset('images/article-default.jpg'),
            'og:site_name' => config('app.name'),
            'article:published_time' => $article->created_at->toIso8601String(),
            'article:modified_time' => $article->updated_at->toIso8601String(),
            'article:author' => config('app.name'),
        ];
    }

    /**
     * Generate Twitter Card meta tags
     */
    public static function generateTwitterCardTags(Article $article)
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $article->title,
            'twitter:description' => $article->seo_description,
            'twitter:image' => asset('images/article-default.jpg'),
            'twitter:site' => '@' . config('app.name'),
        ];
    }
}
