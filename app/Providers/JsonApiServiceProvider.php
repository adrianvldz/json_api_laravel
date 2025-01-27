<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\JsonApi\Mixins\JsonApiQueryBuilder;
use App\JsonApi\Mixins\JsonApiTestResponse;
use App\JsonApi\Mixins\JsonApiRequest;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \App\Exceptions\Handler::class,
            \App\JsonApi\Exceptions\Handler::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::mixin(new JsonApiQueryBuilder);
        TestResponse::mixin(new JsonApiTestResponse);

        Request::mixin(new JsonApiRequest);

    }
}
