<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LogoutController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{

    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'auth:sanctum'),
        ];
    }

    public function __invoke(Request $request)
    {
       $request->user()->currentAccessToken()->delete();

       return response()->noContent();
    }
}
