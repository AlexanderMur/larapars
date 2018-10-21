<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 21.10.2018
 * Time: 19:46
 */

namespace App\Schedules;


use App\Services\ParserService;

class ParseDonors
{
    public function __invoke(ParserService $parserService)
    {
        info('calling crom job...');
        $parserService->parseDonors();
    }
}