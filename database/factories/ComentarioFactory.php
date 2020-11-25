<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comentarios;
use App\Model;
use Faker\Generator as Faker;

$factory->define(Comentarios::class, function (Faker $faker) {
    return [
        'titulo' => $faker->word,
        'comentario' => $faker->sentence,
        'users_id' => $faker->numberBetween($min = 1, $max = 10),
        'producto_id' => $faker->numberBetween($min = 1, $max = 20)
    ];
});
