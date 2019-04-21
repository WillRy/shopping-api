<?php

namespace CodeShopping\Http\Controllers\Api;

use CodeShopping\Models\ProductInput;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\ProductInputResource;
use CodeShopping\Http\Requests\ProductInputRequest;

class ProductInputController extends Controller
{

    public function index()
    {
        $inputs = ProductInput::with('product')->paginate();  //eager loading
        return ProductInputResource::collection($inputs);
    }

    public function store(ProductInputRequest $request)
    {
        $input = ProductInput::create($request->all());
        $product = $input->product;
        $product->stock += $input->amount;
        $product->save();
        return new ProductInputResource($input);
    }

    public function show(ProductInput $input)
    {
        return new ProductInputResource($input);
    }
}
