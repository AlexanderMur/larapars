<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 26.10.2018
 * Time: 23:45
 */

namespace App\Components;


use Complex\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Promise;
use function GuzzleHttp\Promise\queue;

class ParserClient
{
    /**
     * @var Client $client
     */
    public $client;
    public $links = [];
    private $num;
    private $runned;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function addGet($link, $options)
    {
        info('addGet');
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
                while (info(count($this->links)) || count($this->links) > 0) {
                    $link = array_shift($this->links);
                    yield function () use ($link) {
                        return $this->client
                            ->getAsync($link['link'], $link['options'])
                            ->then(function ($value) use ($link) {
                                $link['promise']->resolve($value);
                                queue()->run();
                            }, function (\Exception $exception) use ($link) {
                                info($exception->getMessage());
                                $link['promise']->reject($exception);
                                queue()->run();
                            });
                    };
                }
                info('stop_while!!!!!!');
            };

            (new Pool($this->client, $requests(), [
                'concurrency' => function () {
                    return max(1, min(count($this->links), $this->concurrency()));
                },
                'rejected'    => function (Exception $exception) {
                    info($exception->getMessage());
                    throw  $exception;
                },
            ]))
                ->promise()
                ->then(null, function (\Throwable $throwable) {

                    info($throwable->getMessage());
                })
                ->wait();
        }
    }

    public function __destruct()
    {
        if(!$this->runned){
            $this->run();
        }
    }

    public function concurrency()
    {
        return 20;
    }
}