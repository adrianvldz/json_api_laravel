<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_create_articles(): void
    {
        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'
        ])->assertCreated();

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
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'

        ])->dump()->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nue',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'Nuevo Artículo',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique(): void
    {
        $article = Article::factory()->create();
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => $article->slug,
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes(): void
    {

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => '$%^&',
            'content' => 'Contenido del articulo'
        ])->assertJsonApiValidationErrors('slug')->dump();
    }

    /** @test */
    public function slug_must_not_contain_underscores(): void
    {

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'witch_underscores',
            'content' => 'Contenido del articulo'
        ])->assertSee(trans('validation.no_underscores', [
            'attribute' => 'data.attributes.slug'
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes(): void
    {

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => '-starts-with-dashes',
            'content' => 'Contenido del articulo'
        ])->assertSee(trans('validation.no_starting_dashes', [
            'attribute' => 'data.attributes.slug'
        ]))->assertJsonApiValidationErrors('slug');
    }

       /** @test */
       public function slug_must_not_end_with_dashes(): void
       {
   
           $this->postJson(route('api.v1.articles.store'), [
               'title' => 'Nuevo Artículo',
               'slug' => 'end-with-dashes-',
               'content' => 'Contenido del articulo'
           ])->assertSee(trans('validation.no_ending_dashes', [
            'attribute' => 'data.attributes.slug'
        ]))->assertJsonApiValidationErrors('slug');
       }

    /** @test */
    public function content_is_required(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'nuevo-articulo'
        ])->assertJsonApiValidationErrors('content');
    }
}
