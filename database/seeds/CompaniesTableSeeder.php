<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('companies')->insert([
            'phone' => '8 495 661-05-98',
            'site' => 'cars-city77.ru',
            'title' => '«Cars-City» автосалон — отзывы покупателей',
            'address' => 'г. Москва, Алтуфьевское ш. д. 31, стр. 1',
            'donor_id' => '1',
        ]);
        DB::table('companies')->insert([
            'phone' => ' +7 (800) 511-84-30',
            'site' => 'https://avancars.ru/',
            'title' => 'Автоцентр Аванкарс — Отзывы',
            'address' => 'г. Москва, Алтуфьевское ш. д. 31, стр. 1',
            'donor_id' => '1',
        ]);
        DB::table('companies')->insert([
            'phone' => '8 (800) 333-07-47; 8 (495) 785-36-33',
            'site' => ' http://varshavka91.ru/',
            'title' => '«АвтоЛидер» Варшавка отзывы',
            'address' => 'г. Москва, Алтуфьевское ш. д. 31, стр. 1',
            'donor_id' => '1',
        ]);
        DB::table('companies')->insert([
            'phone' => '8 495 258-46-68; 8 499 281-62-78',
            'site' => 'owauto.ru',
            'title' => '«Ост Вест Авто» отзывы о салоне',
            'address' => 'г. Москва, Алтуфьевское ш. д. 31, стр. 1',
            'donor_id' => '1',
        ]);

        DB::table('companies')->insert([
            'phone' => null,
            'site' => 'http://alea-auto.ru',
            'title' => 'Автосалон АлеаАвто',
            'address' => 'г. Москва, м. Ясенево, 38 км МКАД с. 6б стр.1',
            'donor_id' => '2',
        ]);
        DB::table('companies')->insert([
            'phone' => null,
            'site' => 'http://www.prestige-kzn.ru/',
            'title' => 'Автосалон Престиж',
            'address' => 'г. Казань, ул. Рихарда Зорге 66',
            'donor_id' => '2',
        ]);
        DB::table('companies')->insert([
            'phone' => null,
            'site' => 'https://dc-ladastart.ru/',
            'title' => 'Автосалон ДЦ LADA СТАРТ',
            'address' => 'г. Кемерово, ул. Терешковой д.62, к.1',
            'donor_id' => '2',
        ]);
        DB::table('companies')->insert([
            'phone' => null,
            'site' => 'https://tdk-auto.ru/',
            'title' => 'Автосалон ТД Куйбышев',
            'address' => 'г. Самара, Московское шоссе 19 км, д. 2 литер "Д"',
            'donor_id' => '2',
        ]);
    }
}
