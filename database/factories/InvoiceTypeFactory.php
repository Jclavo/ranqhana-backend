<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InvoiceType;
use Faker\Generator as Faker;

$factory->define(InvoiceType::class, function (Faker $faker) {
    return [
        'code' => strtoupper($faker->unique()->lexify('?')),
        'name' => $faker->name,
    ];
});
