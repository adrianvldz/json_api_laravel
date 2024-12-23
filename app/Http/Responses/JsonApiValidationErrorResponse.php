<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class JsonApiValidationErrorResponse extends JsonResponse
{
    public function __construct(ValidationException $exception)
    {
        $title = $exception->getMessage();
        $errors = [];
       foreach($exception->errors() as $field => $messages){
            // Modificamos cómo se construye el pointer 
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
        ],

        $headers =  [
            'content-type' => 'application/vnd.api+json'
        ]);
        parent::__construct($errors, 422, $headers);
    }
}