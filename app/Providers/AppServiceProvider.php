<?php

namespace CodeShopping\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductInput;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        ProductInput::created(function($input){
            $product = $input->product;
            $product->stock += $input->amount;
            $product->save();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
