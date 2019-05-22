<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\User;
use CodeShopping\Models\UserProfile;
use CodeShopping\Firebase\Auth as Auth;
use CodeShopping\Mail\PhoneNumberChangeMail;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\CustomerRequest;
use CodeShopping\Http\Requests\PhoneNumberToUpdateRequest;

class CustomerController extends Controller
{

    public function store(CustomerRequest $request)
    {
        $data = $request->all();
        $token = $request->token;
        $data['phone_number'] = $this->getPhoneNumber($token);
        $data['photo'] = $data['photo'] ?? null;
        $user = User::createCustomer($data);
        return [
            'token' => \Auth::guard('api')->login($user)
        ];
    }



    public function requestPhoneNumberUpdate(PhoneNumberToUpdateRequest $request)
    {
        $user = User::whereEmail($request->email)->first();
        $phoneNumber = $this->getPhoneNumber($request->token);
        $token = UserProfile::createTokenToChangePhoneNumber($user->profile, $phoneNumber);
        \Mail::to($user)->send(new PhoneNumberChangeMail($user, $token));
        return response()->json([], 204);
    }

    public function updatePhoneNumber($token)
    {
        UserProfile::updatePhoneNumber($token);
        return response()->json([], 204);
    }

    private function getPhoneNumber($token)
    {
        $firebaseAuth = app(Auth::class);
        return $firebaseAuth->phoneNumber($token);
    }
}
