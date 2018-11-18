<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.11.2018
 * Time: 2:37
 */

namespace App\Parsers;


use App\Components\Crawler;
use App\Components\ParserClient;
use App\Models\Donor;
use App\Models\ParserTask;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;

abstract class Parser
{
    protected $client;
    public $concurrency;
    /**
     * @var ParserTask
     */
    protected $parserTask;
    public $proxies = [];
    public $visitedPages = [];
    public $tries;
    public $canceled = false;

    public function __construct(ParserClient $client, ParserTask $parserTask, $proxies, $tries)
    {
        $this->client     = $client;
        $this->parserTask = $parserTask;

        $this->proxies = $proxies;
        $this->tries   = $tries;
    }

    public function fetch($method = 'GET', $url, $options = [],$attempts = 0)
    {

        $http = $this->parserTask->createGet($url, $options['methodName'], $options['donor_id'],$options['form_params'] ?? null);

        $random_proxy = $this->proxies[rand(0, count($this->proxies) - 1)];

        return $this->client->sendToQueue($method,$url,array_merge([
            'headers'     => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
            ],
            'verify'      => false,
            'proxy'       => [
                'http'  => $random_proxy,
                'https' => $random_proxy,
            ],
            'before_send' => function () use ($http) {
                if ($this->should_stop())
                    return false;

                $http->update(['sent_at' => Carbon::now()]);
                return true;
            },
//            'connect_timeout' => 20,
            'attempts' => $this->tries,
        ],$options))
            ->then(function (Response $response) use ($options, $http) {
                $http->updateStatus($response->getStatusCode(), $response->getReasonPhrase());
                $contents = $response->getBody()->getContents();

                return $contents;
            }, function (\Exception $exception) use ($attempts, $method, $options, $http) {

                $http->updateStatus($exception->getCode(), str_limit($exception->getMessage(), 191 - 3));

                switch ($exception->getCode()) {
                    case 404:
                        $this->parserTask->log('404', 'not_found', $http->url);
                        break;
                    case 0:
                        $attempts++;
                        if ($attempts < $this->tries) {
                            return $this->fetch($method,$http->url, $options,$attempts);
                        }
                        break;
                }
                throw $exception;
            })->then(null, function (\Throwable $e) {
                info('parser_error!!!: ' . $e->getMessage());
                throw $e;
            });
    }

    /**
     * @param $link
     * @param Donor $donor
     * @param string $methodName
     * @param bool $unshift
     * @return \GuzzleHttp\Promise\Promise|PromiseInterface
     */
    public function getPage($link, Donor $donor, $methodName = '',$unshift = false)
    {
        return $this->fetch('GET',$link,[
            'methodName' => $methodName,
            'donor_id' => $donor->id,
            'unshift' => $unshift,
        ])
        ->then(function($contents) use ($donor, $link) {
            $contents = str_replace($donor->replace_search, $donor->replace_to, $contents);
            return new Crawler($contents,$link);
        });
    }

    public function should_stop()
    {
        if ($this->parserTask->isPausingOrPaused()) {
            $this->canceled = true;
        }
        return $this->canceled;
    }

    public function run()
    {
        $this->client->run();
    }

    public function add_visited_page($url)
    {
        if (!in_array($url, $this->visitedPages)) {
            $this->visitedPages[] = $url;
            return true;
        }
        return false;
    }

    /**
     * @param $url
     * @param Donor $donor
     * @return PromiseInterface
     */
    abstract function parseCompanyByUrl($url, Donor $donor);

    /**
     * @param Donor $donor
     * @return PromiseInterface
     */
    abstract function parseAll(Donor $donor);

    /**
     * @param $url
     * @param Donor $donor
     * @param bool $recursive
     * @param array $params
     * @return PromiseInterface
     */
    abstract function parseArchivePageRecursive($url, Donor $donor, $recursive = true,$params = []);
}