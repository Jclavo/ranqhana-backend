<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\Country;
use App\Store;
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
        'identification' => $faker->unique()->randomNumber($nbDigits = 9, $strict = true) . $faker->unique()->randomNumber($nbDigits = 2, $strict = true),
        'password' => bcrypt('secret'),
        'api_token' => $faker->regexify('[A-Za-z0-9]{80}'),
        'country_code' => function () {
             return factory(Country::class)->create()->country_code;
        },
        'store_id' => function () {
            return factory(Store::class)->create()->id;
        }
    ];
});
