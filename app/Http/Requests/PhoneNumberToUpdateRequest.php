<?php

namespace CodeShopping\Http\Requests;

use CodeShopping\Rules\PhoneNumberUnique;
use Illuminate\Foundation\Http\FormRequest;
use CodeShopping\Rules\FirebaseTokenVerification;

class PhoneNumberToUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'token' => [
                'required',
                new FirebaseTokenVerification(),
                new PhoneNumberUnique()
            ]
        ];
    }
}
