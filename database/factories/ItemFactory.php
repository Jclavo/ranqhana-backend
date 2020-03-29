<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Item;
use App\Store;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->name,
        'price' => $faker->randomNumber(3),
        'stock' => 0,
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        }

    ];
});
