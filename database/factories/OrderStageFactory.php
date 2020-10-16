<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderStage;
use Faker\Generator as Faker;

$factory->define(OrderStage::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
