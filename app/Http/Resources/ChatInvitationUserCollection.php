<?php

namespace CodeShopping\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use CodeShopping\Models\ChatGroup;

class ChatInvitationUserCollection extends ResourceCollection
{

    private $group;

    public function __construct($resource,ChatGroup $group)
    {
        $this->group = $group;
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'group'=>new ChatGroupResource($this->group),
            'invitations'=>$this->collection->map(function($invitation){
                return new ChatInvitationUserResource($invitation,true);
            })
        ];
    }
}
