<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Models\Product;
use Illuminate\Routing\Controller;
use CodeShopping\Http\Requests\ProductRequest;
use CodeShopping\Http\Resources\ProductResource;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::paginate(10);
        return ProductResource::collection($products);
    }


    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());
        $product->refresh();
        return new ProductResource($product);
    }


    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->fill($request->all());
        $product->save();
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response([],204);
    }
}
