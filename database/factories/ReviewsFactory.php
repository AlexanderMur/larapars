<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Review::class, function (Faker $faker) {
    return [
        'name'  => $faker->firstName,
        'title' => $faker->words(2,true),
        'text'  => $faker->text(300),
        'good'  => $faker->boolean,
    ];
});
