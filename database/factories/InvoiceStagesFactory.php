<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InvoiceStages;
use Faker\Generator as Faker;

$factory->define(InvoiceStages::class, function (Faker $faker) {
    return [
        'code' => $faker->name,
        'description' => $faker->text(),
    ];
});
