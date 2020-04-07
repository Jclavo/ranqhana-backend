<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Item;
use App\Store;
use App\Unit;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->name,
        'price' => $faker->randomNumber(3),
        'stock' => 0,
        'unit' => function () {
            return factory(Unit::class)->create()->code;
        },
        // 'stocked' => intval($faker->boolean(100)),
        'stocked' => intval($faker->boolean),
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        }

    ];
});
