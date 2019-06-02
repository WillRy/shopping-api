<?php

namespace CodeShopping\Http\Requests;

use CodeShopping\Models\User;
use Illuminate\Foundation\Http\FormRequest;


class ChatMessageFbRequest extends FormRequest
{

    public function authorize()
    {
        return $this->groupHasUser() || $this->hasSeller();
    }

    private function groupHasUser()
    {
        // chatgroup istance
        $chatGroup = $this->route('chat_group');
        $user = \Auth::guard('api')->user();
        return $chatGroup->users()->where('user_id', $user->id)->exists();
    }

    private function hasSeller()
    {
        $user = \Auth::guard('api')->user();
        return $user->role == User::ROLE_SELLER;
    }


    public function rules()
    {
        return [
            'type'=>'required|in:text,image,audio',
            'content'=>'required'
        ];
    }
}
