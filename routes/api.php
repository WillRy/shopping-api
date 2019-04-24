<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace'=>'Api','as'=>'api.'],function(){
    Route::resource('categories','CategoryController',['except'=>['create','edit']]);
    Route::resource('products','ProductController',['except'=>['create','edit']]);

    Route::resource('products.categories','ProductCategoryController',['only'=>['index','store','destroy']]);

    Route::resource('inputs','ProductInputController',['only'=>['index','store','show']]);
    Route::resource('outputs','ProductOutputController',['only'=>['index','store','show']]);

    Route::resource('products.photos','ProductPhotoController',['except'=>['create','edit','update']]);

    // criado rota personalizada fora do resource, pois o PHP não suporta envio de arquivos com método PUT, use o POST.
    Route::post('products/{product}/photos/{photo}','ProductPhotoController@update');
});



