<?php

namespace Tests\Feature;

use App\Components\ParserClient;
use App\Models\ParserTask;
use App\Services\ParserService;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Response;
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

    public function testGuzzleClient()
    {
        $client   = new Client([
            'proxy'=>[
                'http' => '127.0.0.1:8080'
            ],
        ]);
        $promises = [];
        $promises[] = $client->getAsync('http://example.com/delay6',[
            'delay' => 6000
        ]);
        $promises[] = $client->getAsync('http://example.com/1');
        $promises[] = $client->getAsync('http://example.com/2');
        $promises[] = $client->getAsync('http://example.com/delay7',[
            'delay' => 7000
        ]);
        $promises[] = $client->getAsync('http://example.com/3');
        $promises[] = $client->getAsync('http://example.com/4');
        $promises[] = $client->getAsync('http://example.com/delay10',[
            'delay' => 10003
        ]);
        $promises[] = $client->getAsync('http://example.com/6');
        $promises[] = $client->getAsync('http://example.com/7');

        unwrap($promises);
        $this->assertTrue(true);
    }

    public function testGuzzleClient404()
    {
        $client     = new Client();
        $promises   = [];
        $promises[] = $client->getAsync('http://example.com/404');

        unwrap($promises);
    }

    public function testClientRecurs()
    {


        $parserClient = new ParserClient(['proxy'=>['http'=>'127.0.0.1:8080']]);
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

        $task = ParserTask::dispatch_now(['http://bp-auto.ru/news/'],'archivePages');

        $task = $task->getFresh();
        echo $task->new_companies_count;

        $this->assertTrue(true);
    }

    public function testParsingCompany()
    {

        $task = ParserTask::dispatch_now(['https://otziv-avto.ru/avtosalon-cars-city-otzyvy/'],'companies');
        $task = $task->getFresh();
        echo $task->new_companies_count;
        $this->assertTrue(true);
    }
    public function testProxy(){


        $client = new ParserClient();
        $proxy = collect(setting()->getProxies())->random();

        $text = '';
        $res = $client->addGet('https://2ip.ru/',[
            'proxy' => [
                'http' => $proxy,
                'https' => $proxy,
            ],
        ])->then(function(Response $response) use(&$text){
            $text = $response->getBody()->getContents();
            $text = trim($text);
        });
        $client->run();
        echo $text . ' || ' . $proxy;

        $this->assertContains($text,$proxy);
    }
    public function testCookies(){


        $client = new ParserClient();
        $proxy = collect(setting()->getProxies())->random();
        $jar = CookieJar::fromArray([
            '_ga'                 => 'GA1.2.1751261396.1541711047',
            '_gid'                => 'GA1.2.1833519052.1541711047',
            '_gat'                => '1',
            '_ym_uid'             => '154171104741839599',
            '_ym_d'               => '1541711047',
            '_ym_visorc_26593554' => 'w',
            '_ym_isad'            => '1',
            'swp_token'           => '1541712891:79f739f530a636d9ea6b0a80652aa762:efeb2d8e28cf4036e3172aab764c1fbe',
        ], parse_url('http://httpbin.org/cookies')['host']);
        $res = $client->addGet('http://httpbin.org/cookies',[
            'proxy' => [
                'http' => $proxy,
                'https' => $proxy,
            ],
            'cookies' => $jar,
        ])->then(function(Response $response) use(&$text){
            $html = $response->getBody()->getContents();
            dump($html);
            xdebug_break();
        });
        $client->run();

        $this->asserttrue(true);
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

    public function testInfoSpeed()
    {

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
