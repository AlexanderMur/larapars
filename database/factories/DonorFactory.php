<?php

use Faker\Generator as Faker;


$factory->defineAs(App\Models\Donor::class, 'donor1', function (Faker $faker) {
    return [
        'link' => 'https://avtosalon-otzyv.ru/',
        'title' => 'Avtosalon otzyv',
    ];
});

$factory->defineAs(App\Models\Donor::class, 'donor2', function (Faker $faker) {
    return [
        'link' => 'https://otziv-avto.ru/msk',
        'title' => 'Otzyv avto',
    ];
});