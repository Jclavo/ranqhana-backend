<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\InvoiceTypes;
use Faker\Generator as Faker;

$factory->define(InvoiceTypes::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('?')),
        'description' => $faker->name,
    ];
});
