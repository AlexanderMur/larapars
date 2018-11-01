<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 01.11.2018
 * Time: 16:07
 */

namespace Tests\Feature;


use App\Models\ParserTask;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    public function testGetParsedCompaniesFromTask()
    {
        $task = ParserTask::find(1);

        $donors = $task->getDonors();

        dump($donors);
        $this->assertTrue(true);
    }

    public function testCustomSelect()
    {

        $task = ParserTask::find(1);

        $task->donors2;

    }
}