<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Locale;
use Faker\Generator as Faker;

$factory->define(locale::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('??')),
    ];
});
