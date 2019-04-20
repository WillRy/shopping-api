<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\Product;
use CodeShopping\Models\Category;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Requests\ProductCategoryRequest;

class ProductCategoryController extends Controller
{

    public function index(Product $product)
    {
        return $product->categories;
    }

    public function store(ProductCategoryRequest $request, Product $product)
    {
        $changed = $product->categories()->sync($request->categories);
        $categoriesAttachedId = $changed['attached'];
        $categories = Category::whereIn('id',$categoriesAttachedId)->get();
        return $categories->count() ? response()->json($categories,201) : [];
    }

    public function destroy(Product $product, Category $category)
    {
        $product->categories()->detach($category);
        return response()->json([],204);
    }
}
