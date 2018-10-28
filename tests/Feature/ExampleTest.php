<?php

namespace Tests\Feature;

use App\Components\ParserClient;
use App\Models\Donor;
use App\Services\ParserService;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testHelloWorld()
    {
        $str = 'Hello World!';

        $this->assertEquals('Hello World!', $str);
    }

    public function testClient()
    {

        $this->expectOutputString('beforebeforebeforeafter');

        $parserClient = new ParserClient();

        $parserClient
            ->addGet('http://example.com')
            ->then(function () use ($parserClient) {
                echo 'before';
            })
            ->then(function () use ($parserClient) {
                echo 'before';
                return $parserClient->addGet('http://example.com');
            })
            ->then(function () use ($parserClient) {
                echo 'before';
            });

        $parserClient->run();
        echo 'after';

    }


    public function testParsing(){

        $parserService = app(ParserService::class);
        $donor = Donor::find(15);
        $count = 0;
        $parserService->parseArchivePageByUrl($donor->link,$donor,function() use (&$count) {
            $this->assertTrue(true);
        });
        $parserService->run();
    }

    public function testGetStatistics(){


        $parserService = app(ParserService::class);

        $parserService->getStatistics();
        $this->assertTrue(true);
    }
    public function testJson(){
        $links = array_fill(0,500,'http://google.com');
        $start = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            json_encode($links);
        }
        $this->assertTrue(true);
        dump(microtime(true) - $start);
    }

}
