<?php

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class MosotzivTest extends TestCase
{
    public function testParsingCompanies()
    {
        ParserTask::dispatch_now([
            'http://mosotziv.ru/company/avtosalon-lada24-kontakty-otzyvy/',
        ], 'companies');


        $this->assertDatabaseHas('parsed_companies', [
            'donor_page' => 'http://mosotziv.ru/company/avtosalon-lada24-kontakty-otzyvy/',
        ]);
        $this->assertTrue(true);
    }
}
