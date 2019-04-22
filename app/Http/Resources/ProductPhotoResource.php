<?php

namespace CodeShopping\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductPhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'photo'=> $this->photo_url, //accessors and mutator
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'product'=>new ProductResource($this->product)
        ];
    }
}
