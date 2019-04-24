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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        return new ProductPhotoCollection($product->photos, $product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductPhotoRequest $request, Product $product)
    {
        $photos = ProductPhoto::createWithPhotosFiles($product->id, $request->photos);
        return response()->json(new ProductPhotoCollection($photos, $product), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \CodeShopping\Models\ProductPhoto  $productPhoto
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        return new ProductPhotoResource($photo);
    }


    public function update(ProductPhotoRequest $request, Product $product, ProductPhoto $photo)
    {
        $this->assertProductPhoto($product, $photo);
        $photos = $request->photos;
        // visando usar o metodo ja existe para deletar arquivos, enviei somente um arquivo, filtrando com array_shift
        $photo->updatePhoto(array_shift($photos));
        return new ProductPhotoResource($photo);
    }

    public function destroy(Product $product, ProductPhoto $photo)
    {
        $photo->deletePhoto();
        return response()->json([], 204);
    }

    public function assertProductPhoto(Product $product, ProductPhoto $photo)
    {
        if ($product->id != $photo->product_id) {
            abort(404);
        }
    }
}
