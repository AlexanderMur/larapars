<?php

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class DonorsTest extends TestCase
{
    public function testParsingCompanies()
    {
        ParserTask::dispatch_now([
            'https://avtospletnik.com/carshowroom/audi-center-taganka/',
            'https://otziv-avto.ru/avtosalon-cars-city-otzyvy/',
            'https://avtosalon-otzyv.ru/avtosalon-yaravtotorg/',
            'https://top-otziv.ru/avtosalon-arteks-avto-otzyvy/',
        ], 'companies');


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
        ));

    }
    public function testObmanAvtosalona()
    {
        ParserTask::dispatch_now([
            'http://xn----7sbbaabk7edlfchc6bo.xn--p1ai/%d0%b0%d0%b2%d1%82%d0%be%d1%81%d0%b0%d0%bb%d0%be%d0%bd-%d0%b3%d1%80%d0%be%d1%81%d1%82%d0%b5%d1%80-%d0%be%d1%82%d0%b7%d1%8b%d0%b2%d1%8b/',
        ], 'companies');

        $this->assertDatabaseHas('parsed_companies', array (
            'site' => 'https://dc-groster.ru',
            'phone' => '8 (800) 505-65-39',
            'address' => 'г. Красноярск, п. Солонцы, проспект Котельникова, 13А',
        ));

//        ParserTask::dispatch_now([
//            'http://xn----7sbbaabk7edlfchc6bo.xn--p1ai/',
//        ], 'archivePages');


        $this->assertTrue(true);

    }
}
