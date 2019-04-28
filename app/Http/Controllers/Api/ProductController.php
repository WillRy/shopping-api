<?php

namespace CodeShopping\Http\Controllers\Api;


use Illuminate\Http\Request;
use CodeShopping\Models\Product;
use Illuminate\Routing\Controller;
use CodeShopping\Common\OnlyTrashed;
use Illuminate\Database\Eloquent\Builder;
use CodeShopping\Http\Requests\ProductRequest;
use CodeShopping\Http\Resources\ProductResource;

class ProductController extends Controller
{

    use OnlyTrashed;

    public function index(Request $request)
    {
        $query = Product::query();
        $query = $this->onlyTrashedIfRequest($request, $query);
        $products = $query->paginate(10);
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

    public function restore(Product $product)
    {
        $product->restore();
        return response()->json([],204);
    }

}
