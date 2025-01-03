<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\Api\ArticleAuthorController;
use App\Http\Controllers\Api\ArticleCategoryController;

Route::middleware([ValidateJsonApiHeaders::class, ValidateJsonApiDocument::class])->name('api.v1.')->group(function () {

    Route::apiResource('articles', ArticleController::class);

    Route::apiResource('comments', CommentController::class);

    Route::apiResource('categories', CategoryController::class)->only('index', 'show');

    Route::apiResource('authors', AuthorController::class)->only('index', 'show');

    Route::get('articles/{article}/relationships/category',
        [ArticleCategoryController::class, 'index'])
        ->name('articles.relationships.category');

    Route::patch('articles/{article}/relationships/category',
        [ArticleCategoryController::class, 'update'])
        ->name('articles.relationships.category');

    Route::get('articles/{article}/category',
        [ArticleCategoryController::class, 'show'])
        ->name('articles.category');

    Route::get('articles/{article}/relationships/author',
        [ArticleAuthorController::class, 'index'])
        ->name('articles.relationships.author');

    Route::patch('articles/{article}/relationships/author',
        [ArticleAuthorController::class, 'update'])
        ->name('articles.relationships.author');

    Route::get('articles/{article}/author',
        [ArticleAuthorController::class, 'show'])
        ->name('articles.author');

    Route::withoutMiddleware([
        ValidateJsonApiDocument::class,
        ValidateJsonApiHeaders::class,
    ])->group(function () {
        Route::post('login', LoginController::class)
            ->name('login');

        Route::post('logout', LogoutController::class)
            ->name('logout');

        Route::post('register', RegisterController::class)
            ->name('register');
    });
});
