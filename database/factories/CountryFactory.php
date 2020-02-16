<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Country;
use Faker\Generator as Faker;

$factory->define(Country::class, function (Faker $faker) {
    return [
        'country_code' => $faker->unique()->randomNumber($nbDigits = 2, $strict = true),
        'name' => $faker->name,
    ];
});
