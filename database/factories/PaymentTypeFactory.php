<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentType;
use Faker\Generator as Faker;

$factory->define(PaymentType::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('?')),
        'name' => $faker->name,
    ];
});
