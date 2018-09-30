<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Company::class, function (Faker $faker) {
    return [
        'phone'            => $faker->optional()->phoneNumber,
        'site'             => $faker->domainName,
        'title'            => $faker->randomElement(['Автосалон', 'Салон', 'Дилерский центр'])
            . ' ' . $faker->words(random_int(1,2), true)
            . ' ' . join(' ',$faker->randomElements(['Cars', 'Avto', 'Motors','Fast',''],2)),
        'address'          => $faker->address,
    ];
});

$factory->defineAs(App\Models\Company::class, 'company1', function (Faker $faker) {
    $title = $faker->optional()->randomElement(['Автосалон ', 'Салон ','Авто']) . 'Престиж ';
    return [
        'phone'            => $faker->optional()->phoneNumber,
        'site'             => 'https://' . str_slug($title) . '.ru',
        'title'            => $title,
        'address'          => $faker->optional()->randomElement(['г. Москва, ','Москва, ']) . 'ул. Сергея Эйзенштейна, д. 1',
    ];
});
