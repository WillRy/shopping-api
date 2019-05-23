<?php

namespace CodeShopping\Http\Requests;

use CodeShopping\Rules\PhoneNumberUnique;
use Illuminate\Foundation\Http\FormRequest;
use CodeShopping\Rules\FirebaseTokenVerification;

class ChatGroupUpdateRequest extends ChatGroupCreateRequest
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
        $rules = parent::rules();
        $this->removeRulesRequiredFromPhoto($rules);
        return $rules;
    }

    private function removeRulesRequiredFromPhoto(array &$rules)
    {
        $rules['photo'] = str_replace('required|', '', $rules['photo']);
    }
}
