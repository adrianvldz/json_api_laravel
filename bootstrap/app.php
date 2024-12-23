<?php

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Debug\ExceptionHandler;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ValidateJsonApiHeaders::class);
        $middleware->append(ValidateJsonApiDocument::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        return \App\Exceptions\Handler::class;

    })->create();
