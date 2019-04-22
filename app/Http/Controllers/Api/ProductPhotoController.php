<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductPhoto;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\ProductPhotoResource;

class ProductPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        return ProductPhotoResource::collection($product->photos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \CodeShopping\Models\ProductPhoto  $productPhoto
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product,ProductPhoto $photo)
    {
        if($product->id != $photo->product_id){
            abort(404);
        }
        return $photo;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \CodeShopping\Models\ProductPhoto  $productPhoto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductPhoto $productPhoto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \CodeShopping\Models\ProductPhoto  $productPhoto
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductPhoto $productPhoto)
    {
        //
    }
}
