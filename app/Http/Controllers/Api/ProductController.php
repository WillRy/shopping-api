<?php

namespace CodeShopping\Http\Controllers\Api;


use Illuminate\Http\Request;
use CodeShopping\Models\Product;
use Illuminate\Routing\Controller;
use CodeShopping\Common\OnlyTrashed;
use Illuminate\Database\Eloquent\Builder;
use CodeShopping\Http\Requests\ProductRequest;
use CodeShopping\Http\Resources\ProductResource;
use CodeShopping\Http\Filters\ProductFilter;

class ProductController extends Controller
{

    use OnlyTrashed;

    public function index(Request $request)
    {
        $filter = app(ProductFilter::class);
        $query = Product::query();
        $query = $this->onlyTrashedIfRequest($request, $query);
        $filterQuery = $query->filtered($filter);
        $products = $filter->hasFilterParameter() ? $filterQuery->get() : $filterQuery->paginate(10);
        return ProductResource::collection($products);
    }


    public function store(ProductRequest $request)
    {
        $product = Product::createWithPhoto($request->all());
        $product->refresh();
        return new ProductResource($product);
    }


    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->updateWithPhoto($request->all());
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
