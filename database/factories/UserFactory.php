<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Country;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
         'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        // 'identification' => $faker->numberBetween($min = 1000, $max = 9000),
        'identification' => $faker->unique()->randomNumber($nbDigits = 9, $strict = true) . $faker->unique()->randomNumber($nbDigits = 2, $strict = true),
        //'identification' => '12312312312',
        'password' => bcrypt('secret'),
         'country_code' => function () {
             return factory(Country::class)->create()->country_code;
         }
    ];
});
