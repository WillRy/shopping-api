<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use CodeShopping\Models\ProductInput;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Filters\ProductInputFilter;
use CodeShopping\Http\Requests\ProductInputRequest;
use CodeShopping\Http\Resources\ProductInputResource;

class ProductInputController extends Controller
{

    public function index()
    {
        // DB::enableQueryLog();
        $filter = app(ProductInputFilter::class);
        $filterQuery = ProductInput::with('product')->filtered($filter);
        $inputs = $filterQuery->paginate();  //eager loading
         // dd(DB::getQueryLog());
        return ProductInputResource::collection($inputs);
    }

    public function store(ProductInputRequest $request)
    {
        $input = ProductInput::create($request->all());

        return new ProductInputResource($input);
    }

    public function show(ProductInput $input)
    {
        return new ProductInputResource($input);
    }
}
