<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\User;
use CodeShopping\Firebase\Auth as Auth;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\CustomerRequest;

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

    private function getPhoneNumber($token)
    {
        $firebaseAuth = app(Auth::class);
        return $firebaseAuth->phoneNumber($token);
    }

}
