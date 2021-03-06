<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Review::class, function (Faker $faker) {
    return [
        'name'  => $faker->firstName,
        'title' => $faker->words(2,true),
        'text'  => nl2p($faker->paragraphs(3,true)),
        'good'  => $faker->boolean,
        'rated_at'  => Carbon::now(),
    ];
});
$factory->state(App\Models\Review::class,'not rated', function (Faker $faker) {
    return [
        'good'  => null,
        'rated_at'  => null,
    ];
});

