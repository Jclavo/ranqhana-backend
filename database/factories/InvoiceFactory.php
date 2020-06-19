<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Invoice;
use App\Models\InvoiceTypes;
use App\Models\User;
use App\Models\Store;

use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {

    //$subtotal = $faker->randomNumber(4, $strict = true);
    //$discount = $faker->randomNumber(1, $strict = true);

    return [
        'serie' => strtoupper($faker->lexify('?')) . '-' . $faker->randomNumber(4, $strict = true),
        'subtotal' => $faker->randomNumber(4, $strict = true),
        'taxes' => $faker->randomNumber(1),
        'discount' => $faker->randomNumber(1),
        'total' => 0,
        //'type_id' => $faker->randomElement(['1', '2']),
        'type_id' => InvoiceTypes::all()->random()->id,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        },
        'stage' => 'P',

    ];
});

$factory->defineAs(Invoice::class,'full', function (Faker $faker) {

    $subtotal = $faker->randomNumber(4, $strict = true);
    $discount = $faker->randomNumber(1, $strict = true);
    $taxes = 0;
    $total = ( $subtotal + $taxes ) - $discount;

    return [
        'serie' => strtoupper($faker->lexify('?')) . '-' . $faker->randomNumber(4, $strict = true),
        'subtotal' => $subtotal,
        'taxes' => $taxes,
        'discount' => $discount,
        'total' => $total,
        'type_id' => InvoiceTypes::all()->random()->id,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        },
        'stage' => 'P',
    ];
});

$factory->defineAs(Invoice::class,'full-type-sell', function (Faker $faker) {

    $subtotal = $faker->randomNumber(4, $strict = true);
    $discount = $faker->randomNumber(1, $strict = true);
    $taxes = 0;
    $total = ( $subtotal + $taxes ) - $discount;

    return [
        'serie' => strtoupper($faker->lexify('?')) . '-' . $faker->randomNumber(4, $strict = true),
        'subtotal' => $subtotal,
        'taxes' => $taxes,
        'discount' => $discount,
        'total' => $total,
        'type_id' => 1,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        },
        'stage' => 'P',
    ];
});

$factory->defineAs(Invoice::class,'full-type-purchase', function (Faker $faker) {

    $subtotal = $faker->randomNumber(4, $strict = true);
    $discount = $faker->randomNumber(1, $strict = true);
    $taxes = 0;
    $total = ( $subtotal + $taxes ) - $discount;

    return [
        'serie' => strtoupper($faker->lexify('?')) . '-' . $faker->randomNumber(4, $strict = true),
        'subtotal' => $subtotal,
        'taxes' => $taxes,
        'discount' => $discount,
        'total' => $total,
        'type_id' => 2,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        },
        'stage' => 'P',
    ];
});
