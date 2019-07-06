<?php

namespace CodeShopping\Http\Controllers\Api\Open;

use Illuminate\Http\Request;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\ProductResource;
use CodeShopping\Models\Product;
use CodeShopping\Http\Filters\Open\ProductFilter as OpenProductFilter;

class ProductController extends Controller
{

    public function index()
    {
        $filter = app(OpenProductFilter::class);
        $filterQuery = Product::filtered($filter);
        $products = $filterQuery->where('active', true)->where('stock','>',0)->paginate();
        return ProductResource::collection($products);
    }

}
