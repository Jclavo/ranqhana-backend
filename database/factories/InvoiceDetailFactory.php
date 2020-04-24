<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\InvoiceDetail;
use App\Invoice;
use App\Item;
use Faker\Generator as Faker;

$factory->define(InvoiceDetail::class, function (Faker $faker) {

    // defineAs
    $quantity = $faker->randomNumber(2, $strict = true);
    $price    = $faker->randomNumber(2, $strict = true);
    $total    = $quantity * $price;

    return [
        'item_id' => function () {
            return factory(Item::class)->create()->id;
        },
        'quantity' => $quantity,
        'price' => $price,
        'total' => 0,
        'invoice_id' => function () {
            return factory(Invoice::class)->create()->id;
        },
    ];
});
