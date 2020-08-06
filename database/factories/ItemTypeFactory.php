<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemType;
use Faker\Generator as Faker;

$factory->define(ItemType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
