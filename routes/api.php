<?php

use App\Http\Controllers\ArticleApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes are automatically prefixed with "api" and assigned
| the "api" middleware group by bootstrap/app.php.
|
*/

Route::prefix('v1')->group(function () {
    // Articles
    Route::get('/articles', [ArticleApiController::class, 'index']);
    Route::get('/articles/today', [ArticleApiController::class, 'today']);
    Route::get('/articles/category/{category}', [ArticleApiController::class, 'byCategory']);
    Route::get('/articles/industry/{industry}', [ArticleApiController::class, 'byIndustry']);
    Route::get('/articles/{slug}', [ArticleApiController::class, 'show']);
});
