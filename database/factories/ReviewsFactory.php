<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Review::class, function (Faker $faker) {
    return [
        'name'  => $faker->firstName,
        'title' => $faker->words(2,true),
        'text'  => $faker->paragraphs(3,true),
        'good'  => $faker->boolean,
    ];
});
