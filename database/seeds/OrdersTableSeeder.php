<?php

use CodeShopping\Models\Order;
use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        foreach (range(1,20) as $v) {
            Order::createWithProduct([
                'user_id' => 1,
                'product_id' => $products->random()->id,
                'amount' => rand(1,2)
            ]);
        }
    }
}
