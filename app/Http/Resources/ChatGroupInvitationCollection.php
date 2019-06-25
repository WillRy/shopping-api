<?php

namespace CodeShopping\Http\Resources;

use CodeShopping\Models\ChatGroup;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChatGroupInvitationCollection extends ResourceCollection
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
            'link_invitations'=>$this->collection->map(function($invitation){
                return new ChatGroupInvitationResource($invitation,true);
            })
        ];
    }
}
