<?php

namespace App\Providers;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TestResponse::macro('assertJsonApiValidationErrors',
        function($attribute){
           /** @var TestResponse $this */
           $this->assertJsonStructure([
               'errors' => [
                   [
                       'title',
                       'detail',
                       'source' => ['pointer']
                   ]
               ]
           ])->assertJsonFragment([
               'source' => ['pointer' => '/data/attributes/{$attribute}']
           ])->assertHeader(
               'content-type', 'application/vnd.api+json'
           )->assertStatus(422);
        });
    }
}
