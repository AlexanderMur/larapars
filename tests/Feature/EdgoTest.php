<?php

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class EdgoTest extends TestCase
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
            'http://aool.ru/jm-ajax/get_listings/',
        ], 'archivePages');

        $this->assertTrue(true);

    }
}
