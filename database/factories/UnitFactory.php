<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Unit;
use App\Store;
use Faker\Generator as Faker;

$factory->define(Unit::class, function (Faker $faker) {
    return [
        'code' => $faker->unique()->lexify('??'),
        'description' => $faker->name,
        'allow_decimal' => intval($faker->boolean),
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        }
    ];
});
