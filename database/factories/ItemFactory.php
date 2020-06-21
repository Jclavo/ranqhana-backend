<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Item;
use App\Models\Unit;
use App\Models\RanqhanaUser;
use Illuminate\Support\Facades\Auth;

use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text(),
        'stock' => $faker->randomNumber(3, $strict = true),
        'price' => $faker->randomNumber(2, $strict = true),
        'stocked' => intval($faker->boolean),

        'unit_id' => Unit::all()->random()->id,
        'user_id' => RanqhanaUser::all()->random()->id,

    ];
});
// 'stocked' => intval($faker->boolean(100)),

$factory->defineAs(Item::class, 'stocked', function (Faker $faker)  {
    $item = factory(Item::class)->raw([
        'stocked' => true,
    ]);
    return $item;
});

$factory->defineAs(Item::class, 'Nostocked', function (Faker $faker)  {
    $item = factory(Item::class)->raw([
        'stock' => 0,
        'stocked' => false,
    ]);
    return $item;
});
