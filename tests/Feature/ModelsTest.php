<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 01.11.2018
 * Time: 16:07
 */

namespace Tests\Feature;


use App\Models\Company;
use App\Models\ParserTask;
use Tests\TestCase;

class ModelsTest extends TestCase
{

    public function testCustomSelect()
    {

        $task = ParserTask::find(1);

        $task->donors;

    }
    public function testHasManyDeep(){
        $company = Company::first();

        $company->tasks()->first()->logs->toArray();
        $this->assertTrue(true);

    }
}