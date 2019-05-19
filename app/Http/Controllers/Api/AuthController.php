<?php

namespace CodeShopping\Http\Controllers\Api;

use Kreait\Firebase;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use CodeShopping\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\UserResource;
use CodeShopping\Firebase\Auth as FirebaseAuth;
use CodeShopping\Rules\FirebaseTokenVerification;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = $this->credentials($request);
        $token = JWTAuth::attempt($credentials);

        return $this->responseToken($token);
    }

    public function loginFirebase(Request $request)
    {
        $this->validate($request, [
            'token' => new FirebaseTokenVerification()
        ]);
        $firebaseAuth = app(FirebaseAuth::class);
        $user = $firebaseAuth->user($request->token);
        $profile = UserProfile::where('phone_number','=',$user->phoneNumber)->first();
        $token = null;
        if($profile) {
            $token = \Auth::guard('api')->login($profile->user);
        }
        return $this->responseToken($token);
    }

    public function responseToken($token)
    {
        return $token ? ['token'=>$token] : response()->json(['error'=>Lang::get('auth.failed')],400);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([],204);
    }

    public function me()
    {
        $user = Auth::guard('api')->user();
        return new UserResource($user);
    }

    public function refresh()
    {
        $token = Auth::guard('api')->refresh();
        return response()->json(['token'=>$token],200);
    }
}
