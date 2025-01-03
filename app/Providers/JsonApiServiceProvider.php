<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiTestResponse;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Database\Eloquent\Builder;
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

       Request::macro('isJsonApi', function (){
            if($this->header('accept') === 'application/vnd.api+json'){
                return true;
            }
            return $this->header('content-type') === 'application/vnd.api+json';
       });
    }
}
