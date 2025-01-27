<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_update_articles(): void
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article))
            ->assertUnauthorized();

    }

    /** @test */
    public function can_update_owned_articles(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ['article:update']);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => $article->slug,
            'content' => 'Updated content',
        ])->assertOk();

        $response->assertJsonApiResource($article, [

            'title' => 'Updated article',
            'slug' => $article->slug,
            'content' => 'Updated content',
        ]);
    }

    /** @test */
    public function can_update_owned_articles_with_relationships(): void
    {
        $article = Article::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($article->author, ['article:update']);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => $article->slug,
            'content' => 'Updated content',
            '_relationships' => [
                'category' => $category,
            ],
        ])->assertOk();

        $response->assertJsonApiResource($article, [

            'title' => 'Updated article',
            'slug' => $article->slug,
            'content' => 'Updated content',
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => 'Updated article',
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function cannot_update_articles_owned_by_other_users(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => $article->slug,
            'content' => 'Updated content',
        ])->assertForbidden();

    }

    /** @test */
    public function title_is_required(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'updated-article',
            'content' => 'Article content',

        ])->dump()->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters(): void
    {

        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Nue',
            'slug' => 'updated-article',
            'content' => 'Article content',

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => 'updated-article',
            'content' => 'Article content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->postJson(route('api.v1.articles.update', $article), [
            'title' => 'Nuevo Artículo',
            'slug' => '$%^&',
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug')->dump();
    }

    /** @test */
    public function slug_must_not_contain_underscores(): void
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->postJson(route('api.v1.articles.update', $article), [
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
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->postJson(route('api.v1.articles.update', $article), [
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
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $this->postJson(route('api.v1.articles.update', $article), [
            'title' => 'Nuevo Artículo',
            'slug' => 'end-with-dashes-',
            'content' => 'Contenido del articulo',
        ])->assertSee(trans('validation.no_ending_dashes', [
            'attribute' => 'data.attributes.slug',
        ]))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique(): void
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        Sanctum::actingAs($article1->author);

        $this->patchJson(route('api.v1.articles.update', $article1), [
            'title' => 'Nuevo Artículo',
            'slug' => $article2->slug,
            'content' => 'Contenido del articulo',
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author);

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => 'updated-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
