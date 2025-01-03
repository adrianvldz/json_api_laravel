<?php

namespace Tests\Unit\JsonApi;

use Mockery;
use App\JsonApi\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    /** @test */
    public function can_create_json_api_documents(): void
    {
        $category = Mockery::mock('Category', function ($mock) {
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('category-id');
        });

        $document = Document::type('articles')
            ->id('article-id')
            ->attributes([
                'title' => 'Article title',
            ])->relationshipsData([
                'category' => $category,
            ])->toArray();

        $excepted = [
            'data' => [
                'type' => 'articles',
                'id' => 'article-id',
                'attributes' => [
                    'title' => 'Article title',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => 'category-id',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($excepted, $document);

    }
}
