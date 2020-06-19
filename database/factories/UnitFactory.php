<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Unit;
use App\Models\Store;

use Faker\Generator as Faker;

$factory->define(Unit::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('??')),
        'description' => $faker->name,
        'fractioned' => intval($faker->boolean),
        'store_id' => Store::all()->random()->id,
    ];
});
