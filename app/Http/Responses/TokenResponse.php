<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;

class TokenResponse implements Responsable
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toResponse($request)
    {
        // generate token
        $plainTextToken = $this->user->createToken(
            $request->device_name,
            $this->user->permissions->pluck('name')->toArray() // user permissions
        )->plainTextToken;

        return response()->json([
            'plain-text-token' => $plainTextToken,
        ]);
    }
}
