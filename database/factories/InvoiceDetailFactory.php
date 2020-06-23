<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InvoiceDetail;
use App\Models\Invoice;
use App\Models\Item;

use Faker\Generator as Faker;

$factory->define(InvoiceDetail::class, function (Faker $faker) {

    // defineAs
    $quantity = $faker->randomNumber(2, $strict = true);
    $price    = $faker->randomNumber(2, $strict = true);
    $total    = $quantity * $price;

    return [
        'item_id' => Item::all()->random()->id,
        'quantity' => $quantity,
        'price' => $price,
        'total' => 0,
        'invoice_id' => Invoice::all()->random()->id,
    ];
});

$factory->defineAs(InvoiceDetail::class,'full', function (Faker $faker) {

    $item = factory(Item::class)->create();
    $quantity = $faker->randomNumber(2, $strict = true);
    $price    = $item->price;
    $total    = $quantity * $price;

    return [
        'item_id' => $item->id,
        'quantity' => $quantity,
        'price' => $price,
        'total' => $total,
        'invoice_id' => Invoice::all()->random()->id,
    ];
});
