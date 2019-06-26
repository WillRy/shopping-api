<?php

namespace CodeShopping\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ChatInvitationUserResource extends JsonResource
{

    private $isCollection;

    public function __construct($resource,$isCollection = false)
    {
        parent::__construct($resource);
        $this->isCollection = $isCollection;
    }

    public function toArray($request)
    {
        $data =  [
            'id'=>$this->id,
            'user'=> new UserResource($this->user),
            'status'=>(int) $this->status,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
        if(!$this->isCollection){
            $data['invitation'] = new ChatGroupInvitationResource($this->invitation);
        }
        return $data;
    }
}
