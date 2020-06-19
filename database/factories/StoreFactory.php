<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Store;
use App\Models\Country;

use Faker\Generator as Faker;

$factory->define(Store::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'country_id' => Country::all()->random()->id,
    ];
});
