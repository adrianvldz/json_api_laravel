<?php

namespace App\JsonApi\Exceptions;
use Exception;
use Illuminate\Http\Request;

class AuthenticationException extends Exception
{
    public function render(Request $request)
    {
        return response()->json([
            'errors' => [
                [
                    'title' => 'Unauthenticated',
                    'detail' => 'This action requires authentication',
                    'status' => '401',
                ],
            ],
        ], 401);
    }
}
