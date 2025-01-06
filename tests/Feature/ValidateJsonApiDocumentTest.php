<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use MakesJsonApiRequests, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('api/test-route', function () {
            return 'OK';
        })->middleware(\App\Http\Middleware\ValidateJsonApiDocument::class);
    }

    /** @test */
    public function only_accepts_valid_json_api_document(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => ['name' => 'test'],
            ],
        ])->assertSuccessful('data');

        $this->patchJson('api/test-route', [
            'data' => [
                'id' => '1',
                'type' => 'string',
                'attributes' => ['name' => 'test'],
            ],
        ])->assertSuccessful('data');

    }

    /** @test */
    public function data_is_required(): void
    {
        $this->postJson('api/test-route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test-route', [])
            ->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array(): void
    {
        $this->postJson('api/test-route', [
            'data' => 'string',
        ])->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test-route', [
            'data' => 'string',
        ])->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'attributes' => [],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                'attributes' => [],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'string'
                ]
            ]
        ])->assertSuccessful();
    }

    /** @test */
    public function data_type_must_be_a_string(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test'],
            ],
        ])->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attribute_is_required(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => ['name' => 'test'],

            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => ['name' => 'test'],

            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attribute_must_be_an_array(): void
    {
        $this->postJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_id_is_required(): void
    {
        $this->patchJson('api/test-route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function data_id_must_be_a_string(): void
    {
        $this->patchJson('api/test-route', [
            'data' => [
                'id' => 1,
                'type' => 'string',
                'attributes' => 'string',
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }
}
