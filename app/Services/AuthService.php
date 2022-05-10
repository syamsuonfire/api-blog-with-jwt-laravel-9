<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\JWTGuard;

class AuthService
{
    public function guard(): JWTGuard
    {
        return Auth::guard();
    }
    // create token
    public function createNewToken($token) : array
    {
        $data =[[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => (int)$this->guard()->factory()->getTTL(),
            'data' => $this->guard()->user()
        ]];
        return $data[0];
    }
}
