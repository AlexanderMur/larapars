<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 16.11.2018
 * Time: 18:35
 */

namespace App\Parsers;


use App\Components\Crawler;

class EdgoParser extends SelectorParser
{
    public function getSite(Crawler $crawler)
    {
        $text  = $crawler->query($this->donor->single_site)->mergeTextOrNull(' ');
        $sites = get_links_from_text($text);
        $sites = array_unique($sites);
        $site  = implode(', ', $sites);
        return $site;
    }

    public function getCompanyPhone(Crawler $crawler)
    {
        $numbers = find_numbers_from_text(
            $crawler->query($this->donor->single_tel)->mergeTextOrNull(PHP_EOL)
        );

        return implode(', ', array_unique($numbers));
    }

    /**
     * @param $url
     * @param bool $recursive
     * @param int $page
     * @return \GuzzleHttp\Promise\Promise|\GuzzleHttp\Promise\PromiseInterface
     */
    public function parseArchivePageRecursive($url,  $recursive = true, $page = 1)
    {


        $this->add_visited_page($page);
        return $this->fetch('POST', $url, [
            'donor_id'    => $this->donor->id,
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
            ->then(function ($json) use ($recursive,  $url) {

                $promises = [];
                if (!$this->should_stop()) {
                    $crawler = new Crawler($json->html, $url);

                    $archiveData = $this->getDataOnPage($crawler);

                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page']);
                        }
                    }
                    if ($recursive) {
                        $max_page = ceil($json->found / 15);
                        for ($i = 1; $i <= $max_page; $i++) {
                            if ($this->add_visited_page($i)) {
                                $promises[] = $this->parseArchivePageRecursive($this->donor->link,  $recursive, $i);
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

    public function iteratePages($fulfilled)
    {


        return $this->getJson()
            ->then(function ($json) use ($fulfilled, &$promises) {

                $max_page = ceil($json->found / 15);
                for ($i = 1; $i <= $max_page; $i++) {
                    $this->getPage2($i)
                        ->then($fulfilled);
                }

                return $fulfilled($this->parseJson($json));
            });
    }

    public function getJson($page = 1, $params = [])
    {
        return $this->fetch('POST', $this->donor->link, [
            'donor_id'    => $this->donor->id,
            'methodName'  => __FUNCTION__,
            'cookies'     => cookies([
                'antibot-hostia' => 'true',
            ], $this->donor->link),
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
    public function getPage2($page = 1, $params = []){
        return $this->getJson($page,$params)
            ->then(function($json) {
                return $this->parseJson($json);
            });
    }
    public function parseJson($json){

        $crawler = new Crawler($json->html, $this->donor->link);
        return $this->getDataOnPage($crawler);
    }
}