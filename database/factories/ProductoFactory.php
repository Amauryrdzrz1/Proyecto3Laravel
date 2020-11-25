<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Productos;
use Faker\Generator as Faker;

$factory->define(Productos::class, function (Faker $faker) {
    return [
        'nombre' => $faker->word,
        'precio' => $faker->randomDigitNotNull,
        'users_id' => $faker->numberBetween($min = 1, $max = 10)
    ];
});
