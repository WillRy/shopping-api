<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\User;
use CodeShopping\Firebase\Auth as Auth;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\CustomerRequest;
use CodeShopping\Http\Resources\UserResource;
use CodeShopping\Http\Requests\UserProfileRequest;

class UserProfileController extends Controller
{

    public function update(UserProfileRequest $request)
    {
        $data = $request->all();
        if ($request->has('token')){
            $token = $request->token;
            $data['phone_number'] = $this->getPhoneNumber($token);
        }
        if($request->has('remove_photo')){
            $data['photo'] = null;
        }
        $user = \Auth::guard('api')->user();
        $user->updateWithProfile($data);
        $resource = new  UserResource($user);
        return [
            'user' => $resource->toArray($request),
            'token' => \Auth::guard('api')->login($user)
        ];
    }

    private function getPhoneNumber($token)
    {
        $firebaseAuth = app(Auth::class);
        return $firebaseAuth->phoneNumber($token);
    }

}
