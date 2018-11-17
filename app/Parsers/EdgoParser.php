<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 16.11.2018
 * Time: 18:35
 */

namespace App\Parsers;


use App\Components\Crawler;
use App\Models\Donor;

class EdgoParser extends SelectorParser
{
    public function getSite(Crawler $crawler, Donor $donor)
    {
        $text  = $crawler->query($donor->single_site)->mergeTextOrNull(' ');
        $sites = get_links_from_text($text);
        $sites = array_unique($sites);
        $site  = implode(', ', $sites);
        return $site;
    }

    public function getCompanyPhone(Crawler $crawler, Donor $donor)
    {
        $numbers = find_numbers_from_text(
            $crawler->query($donor->single_tel)->mergeTextOrNull(PHP_EOL)
        );

        return implode(', ', array_unique($numbers));
    }

    /**
     * @param $url
     * @param Donor $donor
     * @param bool $recursive
     * @param int $page
     * @return \GuzzleHttp\Promise\Promise|\GuzzleHttp\Promise\PromiseInterface
     */
    public function parseArchivePageRecursive($url, Donor $donor, $recursive = true, $page = 1)
    {


        $this->add_visited_page($page);
        return $this->fetch('POST', $url, [
            'donor_id'    => $donor->id,
            'methodName'  => __FUNCTION__,
            'cookies'     => cookies([
                'antibot-hostia' => 'true',
            ], $url),
            'form_params' => [
                'action'   => 'ajax_search_tags',
                'cat_id'   => 15,
                'loc_id'   => '',
                'pageno'   => $page,
                'skeywork' => '',
            ],
        ])
            ->then('json_decode')
            ->then(function ($json) use ($recursive, $donor, $url) {

                $promises = [];
                if (!$this->should_stop()) {
                    $crawler = new Crawler($json->html, $url);

                    $archiveData = $this->getDataOnPage($crawler, $donor);

                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page'], $donor);
                        }
                    }
                    if ($recursive) {
                        $max_page = ceil($json->found / 15);
                        for ($i = 1; $i <= $max_page; $i++) {
                            if ($this->add_visited_page($i)) {
                                $promises[] = $this->parseArchivePageRecursive($donor->link, $donor, $recursive, $i);
                            }
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->then(null, function (\Throwable $throwable) {

                info_error($throwable);
                throw $throwable;
            });
    }

    public function iteratePages(Donor $donor, $fulfilled)
    {

        $promises = new \ArrayIterator();

        $promises->append($this->getJson($donor)
            ->then(function ($json) use ($donor,&$promises) {

                $max_page = ceil($json->found / 15);
                for ($i = 1; $i <= $max_page; $i++) {
                    $promises->append($this->getPage2($donor, $i));
                }

                return $this->parseJson($donor, $json);
            }));

        return \GuzzleHttp\Promise\each($promises, $fulfilled);
    }

    public function getJson(Donor $donor, $page = 1, $params = [])
    {
        return $this->fetch('POST', $donor->link, [
            'donor_id'    => $donor->id,
            'methodName'  => __FUNCTION__,
            'cookies'     => cookies([
                'antibot-hostia' => 'true',
            ], $donor->link),
            'form_params' => array_merge([
                'action'   => 'ajax_search_tags',
                'cat_id'   => 15,
                'loc_id'   => '',
                'pageno'   => $page,
                'skeywork' => '',
            ], $params),
        ])
            ->then('json_decode');
    }
    public function getPage2(Donor $donor, $page = 1, $params = []){
        return $this->getJson($donor,$page,$params)
            ->then(function($json) use ($donor) {
                return $this->parseJson($donor, $json);
            });
    }
    public function parseJson(Donor $donor,$json){

        $crawler = new Crawler($json->html, $donor->link);
        return $this->getDataOnPage($crawler, $donor);
    }
}