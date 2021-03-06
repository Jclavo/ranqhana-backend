<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\PaymentStage;
use Faker\Generator as Faker;

$factory->define(PaymentStage::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('?')),
        'name' => $faker->name,
    ];
});
