<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;


Route::name('api.v1.')->group(function(){

    Route::apiResource('articles', ArticleController::class);
    
    Route::apiResource('categories', CategoryController::class)->only('index', 'show');

});

