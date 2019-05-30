<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductInput;

class ProductInputsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        factory(ProductInput::class, 300)
            ->make()
            ->each(function ($input) use ($products) {
                $product = $products->random();
                $input->product_id = $product->id;
                $input->save();
            });
    }
}
