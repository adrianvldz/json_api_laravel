<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeCategoryTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_include_related_category_of_an_article(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'category'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name
                    ]
                ]
            ]
        ]);
    }

     /** @test */
     public function can_include_related_categories_of_multiple_articles(): void
     {
         $article = Article::factory()->create()->load('category');
         $article2 = Article::factory()->create()->load('category');
 
         $url = route('api.v1.articles.index', [
             'include' => 'category'
         ]);
         

         $this->getJson($url)->assertJson([
             'included' => [
                 [
                     'type' => 'categories',
                     'id' => $article->category->getRouteKey(),
                     'attributes' => [
                         'name' => $article->category->name
                     ]
                ],
                [
                    'type' => 'categories',
                    'id' => $article2->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article2->category->name
                    ]
                ]
             ]
         ]);
     }

        /** @test */
    public function cannot_include_unknown_relationships(): void
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'unknown'
        ]);

        $this->getJson($url)->assertStatus(400);

        $url = route('api.v1.articles.index', [
            'include' => 'unknown'
        ]);

        $this->getJson($url)->assertStatus(400);
    }

}
