<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Models\ChatGroup;
use CodeShopping\Models\ChatGroupInvitation;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\ChatGroupInvitationResource;
use CodeShopping\Http\Resources\ChatGroupInvitationCollection;
use CodeShopping\Http\Requests\ChatGroupInvitationCreateRequest;
use CodeShopping\Http\Requests\ChatGroupInvitationUpdateRequest;

class ChatGroupInvitationController extends Controller
{

    public function index(ChatGroup $chat_group)
    {
        $linkInvitations = $chat_group->linkInvitations()->paginate();
        return new ChatGroupInvitationCollection($linkInvitations, $chat_group);
    }

    public function store(ChatGroupInvitationCreateRequest $request, ChatGroup $chat_group)
    {
        $chatGroupInvitation = ChatGroupInvitation::create($request->all() + ['group_id' => $chat_group->id]);
        return new ChatGroupInvitationResource($chatGroupInvitation);
    }


    public function show(ChatGroup $chat_group, ChatGroupInvitation $link_invitation)
    {
        $this->assertInvitation($chat_group, $link_invitation);

        return new ChatGroupInvitationResource($link_invitation);
    }

    public function update(ChatGroupInvitationUpdateRequest $request, ChatGroup $chat_group, ChatGroupInvitation $link_invitation)
    {
        $this->assertInvitation($chat_group, $link_invitation);
        $link_invitation->fill($request->except('group_id'))->save();
        return new ChatGroupInvitationResource($link_invitation);
    }


    public function destroy(ChatGroup $chat_group, ChatGroupInvitation $link_invitation)
    {
        $this->assertInvitation($chat_group, $link_invitation);
        $link_invitation->delete();
        return response()->json([],204);
    }

    public function assertInvitation(ChatGroup $chatGroup, ChatGroupInvitation $link_invitation)
    {
        if ($link_invitation->group_id != $chatGroup->id) {
            abort(404);
        }
    }
}
