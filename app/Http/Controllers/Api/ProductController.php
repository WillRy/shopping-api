<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\Product;
use Illuminate\Routing\Controller;
use Illuminate\Database\Eloquent\Builder;
use CodeShopping\Http\Requests\ProductRequest;
use CodeShopping\Http\Resources\ProductResource;

class ProductController extends Controller
{

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

    private function onlyTrashedIfRequest(Request $request, Builder $query)
    {
        if($request->get('trashed') == 1){
            $query = $query->onlyTrashed();
        }
        return $query;
    }
}
