<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected function invalidJson($request, ValidationException $exception)
    {
        $title = $exception->getMessage();
        $errors = [];
        foreach($exception->errors() as $field => $messages){
            // Modificamos cómo se construye el pointer para seguir el formato JSON:API
            $pointer = '/data/attributes/' . str_replace(['data.attributes.', '.'], ['', '/'], $field);

            $errors[] = [
                'title' => $title,
                'detail' => $messages[0],
                'source' => [
                    'pointer' => $pointer
                ]
            ];
        }
        return response()->json([
            'errors' => $errors
        ], 422);
    }
}