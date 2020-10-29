<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Translation;
use Faker\Generator as Faker;

$factory->define(Translation::class, function (Faker $faker) {
    return [
        'key' => '',
        'value' => '',
        'locale' => '',
        'translationable_id' => '',
        'translationable_type' => '',
    ];
});
