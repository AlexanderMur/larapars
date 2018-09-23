<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Company::class, function (Faker $faker) {
    return [
        'phone'            => $faker->optional()->phoneNumber,
        'site'             => $faker->domainName,
        'single_page_link' => 'https://avtosalon-otzyv.ru/avtosalon-aleaavto/',
        'title'            => $faker->randomElement(['Автосалон', 'Салон', 'Дилерский центр'])
            . ' ' . $faker->words(random_int(1,2), true)
            . ' ' . join(' ',$faker->randomElements(['Cars', 'Avto', 'Motors','Fast',''],2)),
        'address'          => $faker->address,
    ];
});
