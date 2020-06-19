<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Price;
use App\Models\Item;

use Faker\Generator as Faker;

$factory->define(Price::class, function (Faker $faker) {
    return [
        'price' => $faker->randomNumber(2),
        'item_id' => function () {
            return factory(Item::class)->create()->id;
        }
    ];
});
