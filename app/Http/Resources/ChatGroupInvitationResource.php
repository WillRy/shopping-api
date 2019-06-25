<?php

namespace CodeShopping\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use CodeShopping\Models\ChatGroup;

class ChatGroupInvitationResource extends JsonResource
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
            'total'=> (int) $this->total,
            'remaining'=>(int) $this->remaining,
            'expires_at'=>$this->expires_at,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
        if(!$this->isCollection){
            $data['group'] = new ChatGroupResource($this->group);
        }
        return $data;
    }
}
