<?php

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class DonorsTest extends TestCase
{
    public function testParsingCompanies()
    {
        ParserTask::dispatch_now([
            'https://edgo.ru/com/avtosalon-biznes-kar-kashirskij-kontakty-otzyvy/',
        ], 'companies');


        $this->assertDatabaseHas('parsed_companies', [
            'donor_page' => 'https://edgo.ru/com/avtosalon-biznes-kar-kashirskij-kontakty-otzyvy/',
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
