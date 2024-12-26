<?php

namespace App\Providers;

use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiTestResponse;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;


class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::mixin(new JsonApiQueryBuilder());
       TestResponse::mixin(new JsonApiTestResponse());
    }
}
