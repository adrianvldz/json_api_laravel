<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            $request->isJsonApi() && throw new JsonApi\NotFoundHttpException($e->getMessage());
        });

        $this->renderable(function (BadRequestHttpException $e, Request $request) {
            $request->isJsonApi() && throw new JsonApi\BadRequestHttpException($e->getMessage());
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            $request->isJsonApi() && throw new JsonApi\AuthenticationException;
        });
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return $request->isJsonApi()
                ? new JsonApiValidationErrorResponse($exception)
                : parent::invalidJson($request, $exception);
       
    }
}
