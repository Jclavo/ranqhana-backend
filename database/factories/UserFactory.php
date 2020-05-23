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
        'store_id' => Store::all()->random()->id,
        'identification' => $faker->unique()->randomNumber(),
        'name' => $faker->name,
        'lastname' => $faker->name,
        'email' => $faker->unique()->email,
        'phone' => $faker->e164PhoneNumber,
        'login' => $faker->unique()->randomNumber(),
        'address' => $faker->address,
        'password' => bcrypt('secret'),
        'api_token' => $faker->regexify('[A-Za-z0-9]{80}'),
        
    ]; 
});

$factory->defineAs(User::class, 'completed', function (Faker $faker)  {
    $user = factory(User::class)->raw();
    $user['login'] = $user['identification'] . $user['store_id'];
    return $user;
    // return array_merge($user,[
    //     'completed' => true
    // ]);
});

$factory->defineAs(User::class, 'brazilian', function (Faker $faker)  {
    $user = factory(User::class)->raw([
        'store_id' => function () {
            return factory(Store::class)->create(['country_id' => 1])->id;
        },
        'identification' => $faker->regexify('[0-9]{11}'),
        'phone' => $faker->regexify('[0-9]{11}'),
    ]);
    $user['login'] = $user['identification'] . $user['store_id'];
    return $user;
});

// Samples about state
// $factory->state(User::class, 'password', [
//     'password' => 'secret'
// ]);

// factory(App\User::class,'brazilian')->states('password')->create();

// $factory->state(User::class, 'brasilian', function (Faker $faker) {
//     return [
//         // 'store_id' => Store::all()->random()->id,
//         'store_id' => function () {
//             return factory(Store::class)->create(['country_id' => 1])->id;
//         },
//         'identification' => $faker->regexify('[0-9]{11}'),
//     ];
// });\



