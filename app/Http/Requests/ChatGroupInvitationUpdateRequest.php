<?php

namespace CodeShopping\Http\Requests;

use CodeShopping\Rules\PhoneNumberUnique;
use Illuminate\Foundation\Http\FormRequest;
use CodeShopping\Rules\FirebaseTokenVerification;

class ChatGroupInvitationUpdateRequest extends FormRequest
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


    public function rules()
    {
        $invitation = $this->route('link_invitation');
        return [
            'total' => 'required|integer|min:'.$invitation->remaining,
            'expires_at' => 'nullable|date|after_or_equal:today'
        ];
    }
}
