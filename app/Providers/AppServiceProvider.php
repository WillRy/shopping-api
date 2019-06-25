<?php

namespace CodeShopping\Providers;

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use CodeShopping\Models\Product;
use Kreait\Firebase\ServiceAccount;
use CodeShopping\Models\ProductInput;
use CodeShopping\Models\ProductOutput;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use CodeShopping\Models\ChatGroupInvitation;

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
        ProductOutput::created(function($input){
            $product = $input->product;
            $product->stock -= $input->amount;
            if($product->stock < 0){
                throw new \Exception("Estoque de {$product->name} não pode ser negativo");
            }
            $product->save();
        });

        ChatGroupInvitation::creating(function($invitation){
            $invitation->slug = str_random(7);
            $invitation->remaining = $invitation->total;
        });
        ChatGroupInvitation::updating(function($invitation){
            $invitation->remaining = $invitation->total;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Firebase::class, function () {
            $serviceAccount = Firebase\ServiceAccount::fromJsonFile(base_path('firebase-admin.json'));
            return (new Firebase\Factory())->withServiceAccount($serviceAccount)->create();
        });
    }
}
