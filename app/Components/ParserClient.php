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
use Throwable;
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

    public function __construct()
    {
        $this->client = new Client();
    }

    public function addGet($link, $options = [])
    {
        info('addGet ' . $link);
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
                    $link = array_shift($this->links);
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
                        if(!$link){
                            $aaa = 12;
                        }
                        return $this->client
                            ->getAsync($link['link'], $link['options'])
                            ->then(function ($value) use ($link) {
                                unset($this->pending[$link['link']]);
                                $link['promise']->resolve($value);
                                queue()->run();
                            }, function (\Exception $exception) use ($link) {
                                unset($this->pending[$link['link']]);
                                $link['promise']->reject($exception);
                                queue()->run();
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
                'rejected'    => function ($exception) {
                    if($exception instanceof Throwable){
                        info($exception->getMessage());
                    } else {
                        info($exception);

                        throw new Exception($exception);
                    }
                    throw $exception;
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
        return 20;
    }
}