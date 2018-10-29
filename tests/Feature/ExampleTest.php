<?php

namespace Tests\Feature;

use App\Components\ParserClient;
use App\Models\Donor;
use App\Services\ParserService;
use GuzzleHttp\Client;
use Tests\TestCase;
use function GuzzleHttp\Promise\unwrap;

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
    public function testClient404()
    {


        $parserClient = new ParserClient();

        $parserClient
            ->addGet('http://example.com/404');

        $parserClient->run();

        $this->assertTrue(true);
        echo 'after';

    }
    public function testGuzzleClient(){
        $client = new Client();
        $promises = [];
        for ($i = 0; $i < 3; $i++) {
            $promises[] = $client->getAsync('http://example.com')
                ->then(function() use ($client) {
                    return $client->getAsync('http://example.com');
                })
                ->then(function(){
                    return 'ok';
                }
                )->then(function($val) use ($i, $client) {
                    echo $val;
                    echo $i;
                    $client->getAsync('http://foo.bar')
                        ->then(function(){
                            $this->assertTrue(true);
                        });
                    return $client->getAsync('http://example.com');
                })
                ->then(function(){
                    $this->assertTrue(true);
                });
        }

        unwrap($promises);
    }
    public function testGuzzleClient404(){
        $client = new Client();
        $promises = [];
        $promises[] = $client->getAsync('http://example.com/404');

        unwrap($promises);
    }
    public function testClientRecurs()
    {


        $parserClient = new ParserClient();
        $i            = 0;
        $recurs       = function ($link) use (&$i, &$recurs, $parserClient) {
            return $parserClient->addGet($link)->then(function () use (&$i, $link, $recurs) {
                if ($i++ < 10) {
                    return $recurs($link)
                        ->then(function ($val) use ($i) {
                            return $val . $i . 'stack';
                        });
                } else {
                    return 'end';
                }
            });
        };

        $expected = 'end10stack9stack8stack7stack6stack5stack4stack3stack2stack1stack';
        $actual   = '';
        $recurs('http://example.com')->then(function ($val) use (&$actual) {
            $actual = $val;
        });

        $parserClient->run();
        $this->assertEquals($expected, $actual);
        echo 'after';

    }


    public function testParsing()
    {

        $parserService = app(ParserService::class);

        $donor = Donor::find(15);

        $parserService->parse([$donor->link],'archivePages');

        $this->assertTrue(true);
    }

    public function testGetStatistics()
    {


        $parserService = app(ParserService::class);

        $parserService->getStatistics();
        $this->assertTrue(true);
    }

    public function testJson()
    {
        $links = array_fill(0, 500, 'http://google.com');
        $start = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            json_encode($links);
        }
        $this->assertTrue(true);
        dump(microtime(true) - $start);
    }

    public function testArr()
    {


        $test = '2';
        $arr  = [];
        for ($i = 0; $i < 600000; $i++) {
            $arr[] = ['name' => 'http://banana.com/' . $i, 'id' => $i];
        }

        if ($test === '1') {
            $start = microtime(true);
            $arr   = array_filter($arr, function ($item, $i) {
                return $item['id'] !== 40;
            }, ARRAY_FILTER_USE_BOTH);
            echo microtime(true) - $start;
        }
        if ($test === '2') {
            $start = microtime(true);
            $arr   = array_map(function ($item, $i) {
                if ($item['id'] !== 40) {
                    return $item;
                }
            }, $arr);
            echo microtime(true) - $start;
        }

        if ($test === '3') {
            $start = microtime(true);
            foreach ($arr as $key => $item) {
                if ($item['id'] === 40) {
                    unset($arr[$key]);
                }
            }
            echo microtime(true) - $start;
        }


        $this->assertTrue(true);
    }

    public function testInfoSpeed(){

        $start = microtime(true);
        info('AAAAAAAAAAAA');
        info('AAAAAAAAAAAA');
        info('AAAAAAAAAAAA');
        info('AAAAAAAAAAAA');
        info('AAAAAAAAAAAA');
        echo microtime(true) - $start;
        $this->assertTrue(true);
    }
}
