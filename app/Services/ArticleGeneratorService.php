<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;
use Exception;

class ArticleGeneratorService
{
    protected $geminiService;
    protected $industries = ['fashion', 'food', 'beauty', 'tech', 'lifestyle', 'health'];
    
    // Rotation pattern: Day 1 = Industry, Day 2 = Tips, Day 3 = Quote (repeats every 3 days)
    protected $rotationPattern = [
        1 => 'industry',
        2 => 'tips',
        3 => 'quote',
    ];

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generate 1 article per day with rotation pattern
     * Day 1: Industry article (random industry)
     * Day 2: Tips article (digital marketing tips)
     * Day 3: Quote article (inspirational quote)
     * Pattern repeats every 3 days
     */
    public function generateDailyArticles()
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'articles' => [],
            'errors' => [],
        ];

        try {
            $dayInCycle = $this->getDayInCycle();
            $articleType = $this->rotationPattern[$dayInCycle];
            
            $article = $this->generateArticleByType($articleType);

            if ($article) {
                $results['success']++;
                $results['articles'][] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'type' => $articleType,
                    'industry' => $article->industry,
                ];
                
                \Log::info("✅ Daily article generated", [
                    'type' => $articleType,
                    'day_in_cycle' => $dayInCycle,
                    'article_id' => $article->id,
                ]);
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to generate {$articleType} article";
            }
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Error: {$e->getMessage()}";
            \Log::error("❌ Daily article generation failed", ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Generate article based on type
     */
    protected function generateArticleByType($type)
    {
        if ($type === 'industry') {
            $industry = $this->getRandomIndustry();
            return $this->generateIndustryArticle($industry);
        } elseif ($type === 'tips') {
            return $this->generateTipsArticle();
        } elseif ($type === 'quote') {
            return $this->generateQuoteArticle();
        }

        return null;
    }

    /**
     * Generate industry article
     */
    protected function generateIndustryArticle($industry)
    {
        $content = $this->generateContent(
            "Generate a comprehensive and engaging industry article about {$industry}. Include insights, trends, and practical advice. (300-500 words)"
        );

        if (!$content) {
            return null;
        }

        $title = "Latest Trends in " . ucfirst($industry) . " Industry";
        $slug = $this->generateUniqueSlug($title);
        $description = Str::limit(strip_tags($content), 160);
        $keywords = "{$industry}, trends, industry insights, {$industry} tips";

        return Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'keywords' => $keywords,
            'category' => 'industry',
            'industry' => $industry,
            'day_number' => $this->getDayInCycle(),
        ]);
    }

    /**
     * Generate tips article (digital marketing tips)
     */
    protected function generateTipsArticle()
    {
        $content = $this->generateContent(
            "Generate 5-7 practical and actionable digital marketing tips that are relevant and useful for content creators and marketers. Format as a numbered list with explanations. (300-500 words)"
        );

        if (!$content) {
            return null;
        }

        $title = "Essential Digital Marketing Tips for 2026";
        $slug = $this->generateUniqueSlug($title);
        $description = "Discover practical digital marketing tips to boost your content strategy and engagement.";
        $keywords = "digital marketing, marketing tips, content strategy, social media marketing";

        return Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'keywords' => $keywords,
            'category' => 'tips',
            'industry' => 'marketing',
            'day_number' => $this->getDayInCycle(),
        ]);
    }

    /**
     * Generate quote article
     */
    protected function generateQuoteArticle()
    {
        $content = $this->generateContent(
            "Generate an inspirational and motivational quote for content creators and entrepreneurs. Include the quote, author (if applicable), and a brief explanation of why this quote is relevant. (150-250 words)"
        );

        if (!$content) {
            return null;
        }

        $title = "Daily Inspiration: Motivational Quote for Creators";
        $slug = $this->generateUniqueSlug($title);
        $description = "Get inspired with our daily motivational quote for content creators and entrepreneurs.";
        $keywords = "inspiration, motivation, quote, entrepreneurship, content creation";

        return Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'keywords' => $keywords,
            'category' => 'quote',
            'industry' => 'general',
            'day_number' => $this->getDayInCycle(),
        ]);
    }

    /**
     * Generate content using Gemini API
     */
    protected function generateContent($prompt)
    {
        try {
            $response = $this->geminiService->generateText($prompt, 800, 0.7);
            return $response;
        } catch (Exception $e) {
            \Log::error("Gemini API error: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug($title)
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Get current day in 3-day cycle (1, 2, or 3)
     */
    protected function getDayInCycle()
    {
        $dayOfYear = now()->dayOfYear;
        $dayInCycle = (($dayOfYear - 1) % 3) + 1;
        return $dayInCycle;
    }

    /**
     * Get random industry
     */
    protected function getRandomIndustry()
    {
        return $this->industries[array_rand($this->industries)];
    }

    /**
     * Get all industries
     */
    public function getIndustries()
    {
        return $this->industries;
    }
}
