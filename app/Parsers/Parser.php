<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.11.2018
 * Time: 2:37
 */

namespace App\Parsers;


use App\Models\Donor;
use GuzzleHttp\Promise\PromiseInterface;

abstract class Parser
{
    /**
     * @param $link
     * @param Donor $donor
     * @param string $type
     * @param int $tries
     * @param null $delay
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPage($link, Donor $donor, $type = '', $tries = 0, $delay = null)
    {
        $http = $this->parser_task->createGet($link, $type, $donor->id);

        $random_proxy = $this->proxies[rand(0, count($this->proxies) - 1)];

        return $this->parserClient
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
                        $this->parser_task->log('404', 'not_found', $http->url);
                        break;
                    case 0:
                        $tries++;
                        if ($tries < $this->tries) {
                            return $this->getPage($http->url, $donor, $http->channel, $tries);
                        }
                        break;
                }
                throw $exception;
            })->then(null, function ($e) {
                info('parser_error!!!: ' . $e->getMessage());
                throw $e;
            });
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
    abstract function parseAll(Donor $donor);

}