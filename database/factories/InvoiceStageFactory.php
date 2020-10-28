<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InvoiceStage;
use Faker\Generator as Faker;

$factory->define(InvoiceStage::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('?')),
        'name' => $faker->name,
    ];
});
