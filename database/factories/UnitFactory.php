<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Unit;

use Faker\Generator as Faker;

$factory->define(Unit::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('??')),
        'description' => $faker->name,
        'fractioned' => intval($faker->boolean)
    ];
});
