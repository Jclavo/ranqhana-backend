<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Invoice;
use App\User;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {

    $subtotal = $faker->randomNumber(4, $strict = true);
    $discount = $faker->randomNumber(1, $strict = true);

    return [
        // 'serie' => strtoupper($faker->lexify('?????')),
        'serie' => strtoupper($faker->lexify('?')) . '-' . $faker->randomNumber(4, $strict = true),
        'subtotal' => $subtotal,
        // 'taxes' => $faker->randomNumber(1),
        'discount' => $discount,
        'total' => $subtotal - $discount,
        'type' => $faker->randomElement(['S', 'P']),
        // 'user_id' => function () {
        //     return factory(User::class)->create()->id;
        // },

    ];
});
