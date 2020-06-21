<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RanqhanaUser;
use Faker\Generator as Faker;

$factory->define(RanqhanaUser::class, function (Faker $faker) {
    return [
        'external_user_id' => $faker->randomNumber(1),
        'company_project_id' => $faker->randomNumber(1),
    ];
});
