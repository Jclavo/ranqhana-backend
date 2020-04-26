<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\InvoiceStage;
use Faker\Generator as Faker;

$factory->define(InvoiceStage::class, function (Faker $faker) {
    return [
        'code' => $faker->name,
        'description' => $faker->text(),
    ];
});