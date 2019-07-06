<?php

namespace CodeShopping\Http\Controllers\Api\Open;

use Illuminate\Http\Request;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Models\Category;
use CodeShopping\Http\Resources\CategoryResource;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::where('active', true)->orderBy('name')->get();
        return CategoryResource::collection($categories);
    }

}
