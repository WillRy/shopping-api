<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Models\Product;
use Illuminate\Routing\Controller;
use CodeShopping\Http\Requests\ProductRequest;

class ProductController extends Controller
{

    public function index()
    {
        return Product::all();
    }


    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());
        $product->refresh();
        return $product;
    }


    public function show(Product $product)
    {
        return $product;
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->fill($request->all());
        $product->save();
        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response([],204);
    }
}
