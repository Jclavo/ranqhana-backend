<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Country;
use Faker\Generator as Faker;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'code' => $faker->unique()->randomNumber($nbDigits = 2, $strict = true),
        //'code' => $faker->unique()->randomNumber(),
        'name' => $faker->name,
        'timezone' => 'UTC',
        'currency' => $faker->currencyCode,
    ];
});
