<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 26.10.2018
 * Time: 23:45
 */

namespace App\Components;


use GuzzleHttp\Client;
use GuzzleHttp\Promise\EachPromise;
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
    /**
     * @var callable $concurrencyfn
     */
    protected $concurrencyfn;
    /**
     * @var callable $eachRequestfn
     *
     * protected $last_promise;
     * /**
     * @var ParserClass $parser
     */
    private $parser;

    public function __construct($config = [])
    {
        $this->client = new Client($config);
        $this->parser = new ParserClass();
    }

    public function sendToQueue($method = 'GET', $link, $options = [])
    {

        $promise       = new Promise();
        $this->links[] = [
            'method'      => $method,
            'link'        => $link,
            'promise'     => $promise,
            'before_send' => $options['before_send'] ?? null,
            'options'     => $options,
        ];
        return $promise;
    }

    public function addGet($link, $options = [])
    {
        return $this->sendToQueue('GET', $link, $options);
    }

    public function addPost($link, $options = [])
    {
        return $this->sendToQueue('POST', $link, $options);
    }

    public function getPendingCount()
    {
        return count($this->pending);
    }

    public function onConcurrency(callable $fn)
    {
        $this->concurrencyfn = $fn;
        return $this;
    }

    public function run()
    {
        if (count($this->links)) {
            $this->runned = true;
            $requests     = function () {
                while (true) {

                    queue()->run();
//                    shuffle($this->links);
                    $link = array_shift($this->links);

                    if (!$link && count($this->pending) > 0) {
                        info('pool fix ' . array_keys($this->pending)[0]);
                        yield reset($this->pending);
                        continue;
                    }

                    if (!$link)
                        break;

                    if (is_callable($link['before_send']) && $link['before_send']() === false)
                        break;

                    $this->pending[$link['link']] = $link['promise'];

                    yield $this->client
                        ->requestAsync($link['method'],$link['link'], $link['options'])
                        ->then(function (Response $response) use ($link) {
                            unset($this->pending[$link['link']]);
                            $link['promise']->resolve($response);
                        }, function (\Exception $exception) use ($link) {
                            unset($this->pending[$link['link']]);
                            info('pool error: ' . $exception->getMessage());
                            $link['promise']->reject($exception);
                        })
                        ->then(null, function (\Throwable $exception) {
                            info('pool fatal error: ' . $exception->getMessage());
                        });
                }
                info('stop_while!!!!!!');
            };

            (new EachPromise($requests(), [
                'concurrency' => function () {
                    $concurrency = min(count($this->links), $this->concurrency());
                    return max(1, $concurrency);
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
        if (!$this->runned) {
            $this->run();
        }


    }

    protected function concurrency()
    {

        return is_callable($this->concurrencyfn) ? call_user_func($this->concurrencyfn) : 25;
    }
}