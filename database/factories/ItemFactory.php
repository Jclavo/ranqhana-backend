<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Item;
use App\Models\Store;
use App\Models\Unit;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        // 'description' => $faker->text(),
        'price' => $faker->randomNumber(2, $strict = true),
        'stock' => $faker->randomNumber(3, $strict = true),
        'unit_id' => Unit::all()->random()->id,
        // 'stocked' => intval($faker->boolean(100)),
        'stocked' => intval($faker->boolean),
        // 'store_id' => Store::all()->random()->id,
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        }

    ];
});
