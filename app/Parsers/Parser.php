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
use App\Models\ParsedCompany;
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
    /**
     * @var Donor
     */
    protected $donor;

    public function __construct(Donor $donor, ParserClient $client, ParserTask $parserTask, $proxies, $tries)
    {
        $this->client     = $client;
        $this->parserTask = $parserTask;

        $this->proxies = $proxies;
        $this->tries   = $tries;
        $this->donor   = $donor;
    }

    public function fetch($method = 'GET', $url, $options = [], $attempts = 0)
    {

        $http = $this->parserTask->createGet($url, $options['methodName'] ?? null, $this->donor->id, $options['form_params'] ?? null);

        $random_proxy = $this->proxies[rand(0, count($this->proxies) - 1)];

        return $this->client->sendToQueue($method, $url, array_merge([
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
        ], $options))
            ->then(function (Response $response) use ($options, $http) {
                $http->updateStatus($response->getStatusCode(), $response->getReasonPhrase());
                $contents = $response->getBody()->getContents();

                return $contents;
            }, function (\Exception $exception) use ($attempts, $method, $options, $http) {

                $http->updateStatus($exception->getCode(), $exception->getMessage());

                switch ($exception->getCode()) {
                    case 404:
                        $this->parserTask->log('404', 'not_found', $http->url);
                        break;
                    case 0:
                        $attempts++;
                        if ($attempts < $this->tries) {
                            return $this->fetch($method, $http->url, $options, $attempts);
                        }
                        break;
                }
                throw $exception;
            })->then(null, function (\Throwable $e) {
                info_error($e);
                throw $e;
            });
    }

    public function post($url = '', $data = [], $options = [])
    {
        $options['form_params'] = $data;

        return $this->fetch('POST',$url,$options);
    }

    /**
     * @param $link
     * @param array $params
     * @return \GuzzleHttp\Promise\Promise|PromiseInterface
     */
    public function getPage($link, $params = [])
    {
        return $this->fetch('GET', $link, $params)
            ->then(function ($contents) use ($link) {
                $contents = str_replace($this->donor->replace_search, $this->donor->replace_to, $contents);
                return new Crawler($contents, $link);
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

    public function parseCompanyByUrl($url, $params = [])
    {


        return $this->getCompany($url, [
            'methodName' => 'parseCompanyByUrl',
        ])
            ->then(function ($data) {
                $parsed_company = ParsedCompany::handleParsedCompany($data, $this->parserTask);
                return $parsed_company;
            })
            ->otherwise(function (\Throwable $e) use ($url) {
                $this->parserTask->log('error', $e->getMessage(), $url);
                info_error($e);
                throw $e;
            });
    }

    public function parseAll($params = [],$options = [])
    {
        if (!isset($params['uri'])) {
            $params['uri'] = $this->donor->link;
        }
        return $this->iteratePages(function ($archiveData, $page = '') {
            $promises = null;
            if (!$this->should_stop()) {
                foreach ($archiveData['items'] as $item) {
                    if ($this->add_visited_page($item['donor_page'])) {
                        $promises[] = $this->parseCompanyByUrl($item['donor_page']);
                    } else {
                        $details = $this->parserTask->details;
                        $details['duplicated_companies']++;
                        $this->parserTask->details = $details;
                    }
                }
                if (empty($archiveData['items'])) {
                    $this->parserTask->log('info', 'ссылок не найдено', $page);
                }
                $this->parserTask->save();
            }

            return \GuzzleHttp\Promise\each($promises);
        }, $params, [
            'methodName' => 'parserAll',
        ]);
    }
    public function iteratePages($fn, $params = [],$options = []){
        if(!isset($params['uri'])){
            $params['uri'] = $this->donor->link;
        }
        $this->add_visited_page($params['uri']);
        return $this->getCompanies($params,$options)
            ->then(function ($archiveData) use ($options, $params, $fn) {

                if(!isset($archiveData['max_page'])){
                    $archiveData['max_page'] = 0;
                }

                $promises = null;
                $promises[] = $fn($archiveData,$params);
                if (!$this->should_stop()) {
                    foreach ($archiveData['pagination'] as $page) {
                        if ($this->add_visited_page($page)) {
                            $params['uri'] = $page;
                            $promises[] = $this->iteratePages($fn,$params,$options);
                        }
                    }
                    for ($i = 0; $i < $archiveData['max_page']; $i++) {
                        $params['page'] = $i;
                        $promises[] = $this->getCompanies($params,$options);
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            });
    }

    /**
     * @param $url
     * @param array $options
     * @return PromiseInterface
     */
    abstract function getCompany($url, $options = []);


    /**
     * @param $params
     * @param array $options
     * @return PromiseInterface
     */
    abstract function getCompanies($params, $options = []);
}