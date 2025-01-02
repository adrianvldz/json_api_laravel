<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
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

         $this->renderable(function(AuthenticationException $e){
            throw new JsonApi\AuthenticationException;
         });
    }
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        if(!$request->routeIs('api.v1.login')){

            return new JsonApiValidationErrorResponse($exception);
        }

        return parent::invalidJson($request, $exception);
    }
}