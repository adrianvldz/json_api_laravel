<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:sanctum', only: ['store', 'update', 'destroy']),
        ];
    }

    public function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category', 'author', 'comments'])
            ->sparseFieldSet()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function index(): AnonymousResourceCollection
    {

        $articles = Article::query()
            ->allowedIncludes(['category', 'author', 'comments'])
            ->allowedFilters(['title', 'content', 'year', 'month', 'categories', 'authors'])
            ->allowedSorts(['title', 'content', 'created-at'])
            ->sparseFieldSet()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function store(SaveArticleRequest $request)
    {

        $this->authorize('create', new Article);

        $articleData = $request->getAttributes();

        $articleData['user_id'] = $request->getRelationshipId('author');
        $categorySlug = $request->getRelationshipId('category');

        $category = Category::where('slug', $categorySlug)->first();
        $articleData['category_id'] = $category->id;

        $article = Article::create($articleData);

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);

        $articleData = $request->getAttributes();

        if ($request->hasRelationship('author')) {

            $articleData['user_id'] = $request->getRelationshipId('author');
        }
        if ($request->hasRelationship('category')) {

            $categorySlug = $request->getRelationshipId('category');
            $category = Category::where('slug', $categorySlug)->first();
            $articleData['category_id'] = $category->id;
        }

        $article->update($articleData);

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request): Response
    {

        $this->authorize('delete', $article);

        $article->delete();

        return response()->noContent();
    }
}
