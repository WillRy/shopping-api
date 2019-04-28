<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Lang;
use CodeShopping\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = $this->credentials($request);
        $token = JWTAuth::attempt($credentials);

        return $token ? ['token'=>$token] : response()->json(['error'=>Lang::get('auth.failed')],400);
    }
}
