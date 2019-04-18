<?php

use Faker\Generator as Faker;

$factory->define(CodeShopping\Models\Product::class, function (Faker $faker) {
    return [
        'name'=>$faker->colorName,
        'description'=>$faker->paragraph,
        'price'=>rand(100,3000),
        'stock'=>rand(0,100)
    ];
});
