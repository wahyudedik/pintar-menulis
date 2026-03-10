<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleApiController extends Controller
{
    /**
     * Get all articles with pagination
     */
    public function index(): JsonResponse
    {
        $perPage = request('per_page', 15);
        $category = request('category');
        $industry = request('industry');

        $query = Article::query();

        if ($category) {
            $query->byCategory($category);
        }

        if ($industry) {
            $query->byIndustry($industry);
        }

        $articles = $query->latest('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'pagination' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ],
        ]);
    }

    /**
     * Get article by slug
     */
    public function show($slug): JsonResponse
    {
        $article = Article::where('slug', $slug)->first();

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }

    /**
     * Get today's articles
     */
    public function today(): JsonResponse
    {
        $articles = Article::today()
            ->latest('created_at')
            ->get()
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'data' => $articles,
            'count' => Article::today()->count(),
        ]);
    }

    /**
     * Get articles by category
     */
    public function byCategory($category): JsonResponse
    {
        $articles = Article::byCategory($category)
            ->latest('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'pagination' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ],
        ]);
    }

    /**
     * Get articles by industry
     */
    public function byIndustry($industry): JsonResponse
    {
        $articles = Article::byIndustry($industry)
            ->latest('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $articles->items(),
            'pagination' => [
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
            ],
        ]);
    }
}
