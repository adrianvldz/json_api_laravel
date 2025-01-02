<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    
   
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


        //generate token
        $plainTextToken = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'plain-text-token' => $plainTextToken
        ]);

    }
}
