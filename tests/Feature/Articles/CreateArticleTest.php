<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user, ['article:create']);

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo',
            '_relationships' => [
                'category' => $category,
                'author' => $user,
            ],
        ])->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del articulo',
                ],
                'relationships' => [
                    'category' => [
                        'links' => [
                            'self' => route('api.v1.articles.relationships.category', $article),
                            'related' => route('api.v1.articles.category', $article),
                        ],
                    ],
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article),
                ],
            ],
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Nuevo artículo',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function guests_cannot_create_articles(): void
    {
        $this->postJson(route('api.v1.articles.store'))
            ->assertUnauthorized();

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test */
    public function title_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo',

        ])->dump()->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nue',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo',

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'Nuevo Artículo',
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $article = Article::factory()->create();
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => $article->slug,
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => '$%^&',
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug')->dump();
    }

    /** @test */
    public function slug_must_not_contain_underscores(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'witch_underscores',
            'content' => 'Contenido del articulo',
        ])->assertSee(trans('validation.no_underscores', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => '-starts-with-dashes',
            'content' => 'Contenido del articulo',
        ])->assertSee(trans('validation.no_starting_dashes', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'end-with-dashes-',
            'content' => 'Contenido del articulo',
        ])->assertSee(trans('validation.no_ending_dashes', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'nuevo-articulo',
        ])->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function category_relationship_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',

        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }

    /** @test */
    public function category_must_exist_in_database(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo Artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo',
            '_relationships' => [
                'category' => Category::factory()->make(),
            ],

        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }
}
