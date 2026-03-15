<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml for articles
     */
    public function index()
    {
        $articles = Article::select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(1000)
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Add articles
        foreach ($articles as $article) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . route('articles.show', $article->slug) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $article->updated_at->toAtomString() . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        // Add articles index
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . route('articles.index') . '</loc>' . "\n";
        $xml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>1.0</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
