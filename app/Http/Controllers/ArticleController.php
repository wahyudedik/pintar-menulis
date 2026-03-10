<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Pagination\Paginator;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles
     */
    public function index()
    {
        $articles = Article::latest('created_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    /**
     * Display a specific article
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        
        // Get related articles (same category, different slug)
        $relatedArticles = Article::where('category', $article->category)
            ->where('slug', '!=', $slug)
            ->latest('created_at')
            ->limit(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
