<?php

namespace CodeShopping\Http\Resources\Open;

use CodeShopping\Models\Product;
use CodeShopping\Http\Resources\ProductPhotoResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductPhotoCollection extends ResourceCollection
{

    private $product;

    public function __construct($resource,Product $product)
    {
        $this->product = $product;
        parent::__construct($resource);
    }
    public function toArray($request)
    {
        return [
            'product'=>new ProductResource($this->product),
            'photos'=>$this->collection->map(function($photo){
                return new ProductPhotoResource($photo,true);
            })
        ];
    }
}
