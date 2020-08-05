<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Service;
use Faker\Generator as Faker;

$factory->define(Service::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text(),
        'price' => $faker->randomNumber(2, $strict = true),
        'user_id' => RanqhanaUser::all()->random()->id,
    ]; 
});
