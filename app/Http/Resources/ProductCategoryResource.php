<?php

namespace CodeShopping\Http\Resources;

use CodeShopping\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use CodeShopping\Http\Resources\CategoryResource;

class ProductCategoryResource extends JsonResource
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
            'product' => new ProductResource($this->resource),
            'categories' => CategoryResource::collection($this->resource->categories)
        ];
    }
}
