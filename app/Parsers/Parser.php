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
    public $canceled;

    public function __construct(ParserClient $client, ParserTask $parserTask,$proxies,$tries)
    {
        $this->client = $client;
        $this->parserTask = $parserTask;

        $this->proxies = $proxies;
        $this->tries   = $tries;
    }

    /**
     * @param $link
     * @param Donor $donor
     * @param string $methodName
     * @param int $tries
     * @param null $delay
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPage($link, Donor $donor, $methodName = '', $tries = 0, $delay = null)
    {
        $http = $this->parserTask->createGet($link, $methodName, $donor->id);

        $random_proxy = $this->proxies[rand(0, count($this->proxies) - 1)];

        return $this->client
            ->addGet($link, [
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
                },
                'delay'       => $delay,
            ])
            ->then(function (Response $response) use ($tries, $http, $donor) {
                $http->updateStatus($response->getStatusCode(), $response->getReasonPhrase());
                $html = $response->getBody()->getContents();
                $html = str_replace($donor->replace_search, $donor->replace_to, $html);
                return new Crawler($html, $http->url);
            }, function (\Exception $exception) use ($tries, $http, $donor) {

                $http->updateStatus($exception->getCode(), str_limit($exception->getMessage(), 191 - 3));

                switch ($exception->getCode()) {
                    case 404:
                        $this->parserTask->log('404', 'not_found', $http->url);
                        break;
                    case 0:
                        $tries++;
                        if ($tries < $this->tries) {
                            return $this->getPage($http->url, $donor, $http->channel, $tries);
                        }
                        break;
                }
                throw $exception;
            })->then(null, function (\Throwable $e) {
                info('parser_error!!!: ' . $e->getMessage());
                throw $e;
            });
    }

    public function should_stop()
    {
        if ($this->parserTask->getState() === 'Paused' || $this->parserTask->getState() === 'Pausing') {
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
     * @return PromiseInterface
     */
    abstract function parseArchivePageRecursive($url, Donor $donor, $recursive = true);
}