<?php

use Faker\Generator as Faker;

/** @var Illuminate\Database\Eloquent\Factory $factory */

$factory->defineAs(App\Models\Donor::class, 'donor1', function (Faker $faker) {
    return [
        'link'  => 'https://avtosalon-otzyv.ru/',
        'title' => 'Avtosalon otzyv',
    ];
});

$factory->defineAs(App\Models\Donor::class, 'donor2', function (Faker $faker) {
    return [
        'link'  => 'https://otziv-avto.ru/msk',
        'title' => 'Otzyv avto',
    ];
});
$factory->define(App\Models\Donor::class, function (Faker $faker) {
    $title = $faker->sentence(1) . ' ' . join(' ', $faker->randomElements(['Отзыв', 'Avto', 'Rate', 'Car'], 2));
    $title = str_replace_first('.','',$title);
    return [
        'link'  => 'http://' . str_slug($title) . '.ru',
        'title' => $title,
    ];
});