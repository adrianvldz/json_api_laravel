<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleController extends Controller
{
    public function show($article): JsonResource{
        $article = Article::where('slug', $article)
                ->allowedIncludes(['category'])
                ->sparseFieldSet()
                ->firstOrFail();
        return ArticleResource::make($article);
    }

    public function index(): AnonymousResourceCollection{

        $articles = Article::query()
        ->allowedIncludes(['category'])
        ->allowedFilters(['title', 'content', 'year', 'month', 'categories'])
        ->allowedSorts(['title', 'content'])
        ->sparseFieldSet()
        ->jsonPaginate();
       

        return ArticleResource::collection($articles);
    }

    public function store(SaveArticleRequest $request){

        
        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request){

     

        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {   
        $article->delete();
        return response()->noContent();

    } 

}
