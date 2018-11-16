<?php

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class AoolTest extends TestCase
{
    public function testParsingCompanies()
    {
        ParserTask::dispatch_now([
            'http://aool.ru/all/avtosalon-atlant-avto-kontakty-otzyvy/',
            'http://aool.ru/all/avtosalon-pelican-motors-kontakty-otzyvy/',
        ], 'companies');


        $this->assertDatabaseHas('parsed_companies', [
            'donor_page' => 'http://aool.ru/all/avtosalon-atlant-avto-kontakty-otzyvy/',
        ]);
        $this->assertDatabaseHas('parsed_companies', [
            'donor_page' => 'http://aool.ru/all/avtosalon-pelican-motors-kontakty-otzyvy/',
        ]);
        $this->assertTrue(true);
    }
    public function testParseAll(){

        ParserTask::dispatch_now([
            'https://edgo.ru/wp-admin/admin-ajax.php',
        ], 'archivePages');

        $this->assertTrue(true);

    }
}
