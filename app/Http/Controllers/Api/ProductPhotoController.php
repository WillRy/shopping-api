<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductPhoto;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\ProductPhotoRequest;
use CodeShopping\Http\Resources\ProductPhotoResource;
use CodeShopping\Http\Resources\ProductPhotoCollection;

class ProductPhotoController extends Controller
{

    public function index(Product $product)
    {
        return new ProductPhotoCollection($product->photos, $product);
    }


    public function store(ProductPhotoRequest $request, Product $product)
    {
        $photos = ProductPhoto::createWithPhotosFiles($product->id, $request->photos);
        return response()->json(new ProductPhotoCollection($photos, $product), 201);
    }


    public function show(Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        return new ProductPhotoResource($photo);
    }


    public function update(ProductPhotoRequest $request, Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        $photo->updateWithPhotosFiles($request->photo);
        return new ProductPhotoResource($photo);
    }

    public function destroy(Product $product, ProductPhoto $photo)
    {
        $photo->deleteWithPhoto();
        return response()->json([], 204);
    }

    public function assertProductPhoto(Product $product, ProductPhoto $photo)
    {
        if ($product->id != $photo->product_id) {
            abort(404);
        }
    }
}
