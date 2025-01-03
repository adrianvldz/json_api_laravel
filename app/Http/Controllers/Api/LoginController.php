<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Responses\TokenResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LoginController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'guest:sanctum'),
        ];
    }
    
   
    public function __invoke(Request $request)
    {

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required']
        ]);
        $user = User::whereEmail($request->email)->first();


        if(! $user || ! Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ]);
        }

        return new TokenResponse($user);

    }
}
