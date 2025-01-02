<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->renderable(function(NotFoundHttpException $e){
           throw new JsonApi\NotFoundHttpException;
        });

        $this->renderable(function(BadRequestHttpException $e){
            throw new JsonApi\BadRequestHttpException($e->getMessage());
         });
    }
    protected function invalidJson($request, ValidationException $exception): JsonApiValidationErrorResponse
    {
        return new JsonApiValidationErrorResponse($exception);
    }
}