<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Models\ChatGroup;
use CodeShopping\Firebase\ChatMessageFb;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\ChatMessageFbRequest;

class ChatMessageFbController extends Controller
{

    public function store(ChatMessageFbRequest $request, ChatGroup $chat_group)
    {
        $chatMessageFb = new ChatMessageFb();
        $chatMessageFb->create([
            'user'=> \Auth::guard('api')->user(),
            'chat_group' => $chat_group
        ] + $request->all());
        return response()->json([],204);
    }

}
