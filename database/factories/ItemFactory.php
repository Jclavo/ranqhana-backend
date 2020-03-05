<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Item;
use App\Store;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->name,
        //'quantity' => $faker->randomNumber(4),
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        }

    ];
});