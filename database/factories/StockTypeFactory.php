<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\StockType;
use Faker\Generator as Faker;

$factory->define(StockType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
