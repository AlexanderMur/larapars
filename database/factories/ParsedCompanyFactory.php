<?php

use App\Models\ParsedCompany;
use Faker\Generator as Faker;


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(ParsedCompany::class, 'company1', function (Faker $faker) {
    $title = $faker->optional()->randomElement(['Автосалон ', 'Салон ', 'Дилерский центр ']) . 'Алеавто ';
    return [
        'phone'   => $faker->optional()->phoneNumber,
        'site'    => 'https://' . str_slug($title) . '.ru',
        'title'   => $title,
        'address' => $faker->optional()->randomElement(['г. Москва, ', 'Москва, ', 'Город Москва, ']) . 'м. Ясенево, 38 км МКАД с. 6б стр.1',
        'city'    => $faker->city,
    ];
});

$factory->defineAs(ParsedCompany::class, 'company2', function (Faker $faker) {
    $title = $faker->optional()->randomElement(['Автосалон ', 'Салон ', 'Авто']) . 'Престиж ';
    return [
        'phone'   => $faker->optional()->phoneNumber,
        'site'    => 'https://' . str_slug($title) . '.ru',
        'title'   => $title,
        'address' => $faker->optional()->randomElement(['г. Москва, ', 'Москва, ']) . 'ул. Сергея Эйзенштейна, д. 1',
        'city'    => $faker->city,
    ];
});
