<?php

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class DonorsTest extends TestCase
{
    public function testParsingCompanies()
    {
        ParserTask::dispatch([
            'https://avtospletnik.com/carshowroom/audi-center-taganka/',
            'https://otziv-avto.ru/avtosalon-cars-city-otzyvy/',
            'https://avtosalon-otzyv.ru/avtosalon-yaravtotorg/',
            'https://top-otziv.ru/avtosalon-arteks-avto-otzyvy/',
        ], 'companies')->now();


        $assertParsedCompanies = [
            [
                'donor_page' => 'https://otziv-avto.ru/avtosalon-cars-city-otzyvy/',
            ],
            [
                'donor_page' => 'https://avtosalon-otzyv.ru/avtosalon-yaravtotorg/',
                'address'    => 'г. Ярославль, Промышленное шоссе, д. 53',
            ],
            [
                'site'       => 'http://arteks-auto.com',
                'address'    => 'г. Москва, ул. Обручева, дом 21, стр. 4',
                'title'      => 'Автосалон Артекс авто отзывы',
                'donor_page' => 'https://top-otziv.ru/avtosalon-arteks-avto-otzyvy/',
            ],
        ];
        foreach ($assertParsedCompanies as $assertParsedCompany) {
            $this->assertDatabaseHas('parsed_companies', $assertParsedCompany);
        }
        $this->assertTrue(true);
    }

    public function testAvtoSpletnik()
    {
        ParserTask::dispatch_now([
            'https://avtospletnik.com/carshowroom/audi-center-taganka/',
        ], 'companies');

        $this->assertDatabaseHas('parsed_companies', array (
            'site' => 'http://www.audi-taganka.ru/content/iph/market_ru/RUS10476/ru.html',
            'phone' => '8 (495) 152 66 01',
            'address' => 'Михайловский проезд 3',
            'title' => '“Ауди Центр Таганка”',
            'city' => 'Москва',
            'donor_page' => 'https://avtospletnik.com/carshowroom/audi-center-taganka/',
            'donor_id' => 19,
        ));

    }
    public function testObmanAvtosalona()
    {
        ParserTask::dispatch_now([
            'http://xn----7sbbaabk7edlfchc6bo.xn--p1ai/',
        ], 'archivePages');


        $this->assertTrue(true);

    }
}
