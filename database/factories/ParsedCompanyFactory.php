<?php

use App\Models\Donor;
use App\Models\ParsedCompany;
use Faker\Generator as Faker;
use Illuminate\Support\Collection;


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->defineAs(ParsedCompany::class, 'company1', function (Faker $faker) use (&$count) {
    $title = $faker->optional()->randomElement(['Автосалон ', 'Салон ', 'Дилерский центр ']) . 'Алеавто ';
    return [
        'phone'      => $faker->optional()->phoneNumber,
        'site'       => 'http://' . str_slug($title) . '.ru',
        'donor_page' => null,
        'title'      => $title,
        'address'    => $faker->optional()->randomElement(['г. Москва, ','Москва, ', 'Город Москва, ']) . 'м. Ясенево, 38 км МКАД с. 6б стр.1',
    ];
});

$factory->defineAs(ParsedCompany::class, 'company2', function (Faker $faker) use (&$count) {
    $title = $faker->optional()->randomElement(['Автосалон ', 'Салон ','Авто']) . 'Престиж ' . $count++;
    return [
        'phone'      => $faker->optional()->phoneNumber,
        'site'       => 'http://' . str_slug($title) . '.ru',
        'donor_page' => null,
        'title'      => $title,
        'address'    => $faker->optional()->randomElement(['г. Москва, ','Москва, ']) . 'ул. Сергея Эйзенштейна, д. 1',
    ];
});
