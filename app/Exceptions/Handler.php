<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->renderable(function(NotFoundHttpException $e, $request){
            $id = $request->input('data.id');
            $type = $request->input('data.type');


            return response()->json([
                'errors' => [
                    'title' => 'Not Found',
                    'detail' => "No records found with the id '{$id}' in the '{$type}' resource.",
                    'status' => '404'
                ]
            ], 404);
        });
    }
    protected function invalidJson($request, ValidationException $exception): JsonApiValidationErrorResponse
    {
        return new JsonApiValidationErrorResponse($exception);
    }
}