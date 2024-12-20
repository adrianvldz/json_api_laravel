<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_create_articles(): void
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del articulo'

                ]
            ]
        ]);
        
        $response->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del articulo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

      /** @test */
public function title_is_required(): void
{
    $response = $this->postJson(route('api.v1.articles.create'), [
        'data' => [
            'type' => 'articles',
            'attributes' => [
                
                'slug' => 'nuevo-articulo',
                'content' => 'Contenido del articulo'
            ]
        ]
    ])->dump();



   
    $response->assertJsonStructure([
        'errors' => [
            [
                'title',
                'detail',
                'source' => ['pointer']
            ]
        ]
    ])->assertJsonFragment([
        'source' => ['pointer' => '/data/attributes/title']
    ])->asseetHeader(
        'content-type', 'application/vnd.api+json'
    )->assertStatus(422);
}

          /** @test */
          public function title_must_be_at_least_4_characters(): void
          {
              $response = $this->postJson(route('api.v1.articles.create'), [
                  'data' => [
                      'type' => 'articles',
                      'attributes' => [
                        'title' => 'Nue',
                          'slug' => 'nuevo-articulo',
                          'content' => 'Contenido del articulo'
      
                      ]
                  ]
              ])->dump();
              
              $response->assertJsonValidationErrors('data.attributes.title');
        }

       /** @test */
       public function slug_is_required(): void
       {
           $response = $this->postJson(route('api.v1.articles.create'), [
               'data' => [
                   'type' => 'articles',
                   'attributes' => [
                       'title' => 'Nuevo Artículo',
                       'content' => 'Contenido del articulo'
   
                   ]
               ]
           ]);
           
           $response->assertJsonValidationErrors('data.attributes.slug');
       }


         /** @test */
         public function content_is_required(): void
         {
             $response = $this->postJson(route('api.v1.articles.create'), [
                 'data' => [
                     'type' => 'articles',
                     'attributes' => [
                         'title' => 'Nuevo Artículo',
                         'slug' => 'nuevo-articulo'
     
                     ]
                 ]
             ]);
             
             $response->assertJsonValidationErrors('data.attributes.content');
         }
}
