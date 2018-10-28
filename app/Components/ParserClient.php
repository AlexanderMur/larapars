<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 26.10.2018
 * Time: 23:45
 */

namespace App\Components;


use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Promise\queue;

class ParserClient
{
    /**
     * @var Client $client
     */
    public $client;
    public $links = [];
    protected $pending = [];
    private $runned;
    protected $last_promise;
    /**
     * @var ParserClass $parser
     */
    private $parser;

    public function __construct($config = [])
    {
        $this->client = new Client($config);
        $this->parser = new ParserClass();
    }

    public function addGet($link, $options = [])
    {
        info('addget');
        $promise       = new Promise();
        $this->links[] = [
            'link'    => $link,
            'promise' => $promise,
            'options' => $options,
        ];
        return $promise;
    }

    public function run()
    {
        if (count($this->links)) {
            $this->runned = true;
            $requests = function () {
                while (true) {
                    queue()->run();
                    shuffle($this->links);
                    $link = array_shift($this->links);
                    info('inks count:' . count($this->links));
                    if (!$link && count($this->pending) > 0) {
                        yield function () {
                            info('pool fix '.array_keys($this->pending)[0]);
                            return reset($this->pending);
                        };
                        continue;
                    }

                    if(!$link){
                        break;
                    }

                    $this->pending[$link['link']] = $link['promise'];
                    yield function () use ($link) {
                        return $this->client
                            ->getAsync($link['link'], $link['options'])
                            ->then(function (Response $response) use ($link) {
                                info($response->getStatusCode());
                                unset($this->pending[$link['link']]);
                                $link['promise']->resolve($response);
                            }, function (\Exception $exception) use ($link) {
                                unset($this->pending[$link['link']]);
                                info($exception->getMessage());
                                $link['promise']->reject($exception);
                                info('then level :'.count(debug_backtrace()));
                            });
                    };
                }
                info('stop_while!!!!!!');
            };

            (new Pool($this->client, $requests(), [
                'concurrency' => function () {

                    $concurrency = min(count($this->links), $this->concurrency());
                    return max(1, $concurrency);
                },
            ]))
                ->promise()
                ->then(null, function ($throwable) {
                    info($throwable->getMessage());
                })
                ->wait();
        }
    }

    public function __destruct()
    {
        if (!$this->runned) {
            $this->run();
        }


    }

    public function concurrency()
    {
        return 100;
    }
}