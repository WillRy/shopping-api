<?php

namespace CodeShopping\Http\Resources\Open;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'active' => (bool)$this->active,
            'photo_url' => $this->photo_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
