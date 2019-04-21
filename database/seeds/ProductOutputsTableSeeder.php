<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductOutput;

class ProductOutputsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        factory(ProductOutput::class, 150)
            ->make()
            ->each(function ($output) use ($products) {
                $product = $products->random();
                $output->product_id = $product->id;
                $output->save();
            });
    }
}
