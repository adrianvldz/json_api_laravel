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

class ArticleController extends Controller
{
    public function show(Article $article): ArticleResource{
        
        return ArticleResource::make($article);
    }

    public function index(): ArticleCollection{

        $articles = Article::allowedSorts(['title', 'content']);
       

        return ArticleCollection::make($articles->jsonPaginate());
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
