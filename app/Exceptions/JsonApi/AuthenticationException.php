<?php

namespace App\Exceptions\JsonApi;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticationException extends Exception
{
    
    public function render(Request $request)
    {
        return response()->json([
            'errors' => [
            [
                'title' => 'Unauthenticated',
                'detail' => 'This action requires authentication',
                'status' => '401'
            ]
        ]
        ], 401);
    }
}
