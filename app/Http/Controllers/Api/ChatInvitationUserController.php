<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\ChatInvitationUser;
use CodeShopping\Models\ChatGroupInvitation;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Exceptions\ChatInvitationUserException;

class ChatInvitationUserController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(ChatGroupInvitation $invitation_slug)
    {
        try {
            $invitationUser = ChatInvitationUser::createIfAllowed($invitation_slug, \Auth::guard('api')->user);

        } catch (ChatInvitationUserException $e) {
            switch($e->getCode()){
                case ChatInvitationUserException::ERROR_NOT_INVITATION:
                    return abort(403, $e->getMessage());
                case ChatInvitationUserException::ERROR_HAS_SELLER:
                    return abort(422, $e->getMessage());
            }
        }

    }


    public function show(ChatInvitationUser $chatInvitationUser)
    {
        //
    }


    public function edit(ChatInvitationUser $chatInvitationUser)
    {
        //
    }


    public function update(Request $request, ChatInvitationUser $chatInvitationUser)
    {
        //
    }


    public function destroy(ChatInvitationUser $chatInvitationUser)
    {
        //
    }
}
