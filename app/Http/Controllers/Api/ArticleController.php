<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    public function show($article): JsonResource{
        $article = Article::where('slug', $article)
                ->allowedIncludes(['category', 'author'])
                ->sparseFieldSet()
                ->firstOrFail();
        return ArticleResource::make($article);
    }

    public function index(): AnonymousResourceCollection{

        $articles = Article::query()
        ->allowedIncludes(['category', 'author'])
        ->allowedFilters(['title', 'content', 'year', 'month', 'categories'])
        ->allowedSorts(['title', 'content'])
        ->sparseFieldSet()
        ->jsonPaginate();
       

        return ArticleResource::collection($articles);
    }

    public function store(SaveArticleRequest $request){

        $this->authorize('create', new Article);

        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);
        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request): Response
    {   
        
        $this->authorize('delete', $article);

        $article->delete();
        return response()->noContent();

    } 

}
