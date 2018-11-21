<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.11.2018
 * Time: 2:35
 */

namespace App\Parsers;


use App\Components\Crawler;
use App\Models\ParsedCompany;

class SelectorParser extends Parser
{
    public function parseCompanyByUrl($url)
    {


        return $this->getPage($url, __FUNCTION__)
            ->then(function (Crawler $crawler) use ($url) {
                return $this->getDataOnSinglePage($crawler);
            })
            ->then(function($data)  {
                $parsed_company = ParsedCompany::handleParsedCompany($data,$this->parserTask);
                info('done');
                return $parsed_company;
            })
            ->otherwise(function (\Throwable $e) use ($url) {
                $this->parserTask->log('info',$e->getMessage(),$url);
                info_error($e);
                throw $e;
            });
    }

    function parseAll()
    {
        return $this->parseArchivePageRecursive($this->donor->link);
    }
    function iteratePages3($fn,$start = ''){

        if($start === ''){
            $start = $this->donor->link;
        }
        return $this->getPage($start)
            ->then(function (Crawler $crawler) use ($fn) {

                $promises = null;
                $archiveData = $this->getDataOnPage($crawler);
                $promises[] = $fn($archiveData);
                if (!$this->should_stop()) {
                    foreach ($archiveData['pagination'] as $page) {
                        if ($this->add_visited_page($page)) {
                            $promises[] = $this->iteratePages3($fn,$page);
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->then(null,function(\Throwable $throwable){

                info_error($throwable);
                throw $throwable;
            });
    }
    public function parseArchivePageRecursive($url, $recursive = true, $params = [])
    {

        $this->add_visited_page($url);
        return $this->getPage($url, __FUNCTION__)
            ->then(function (Crawler $crawler) use ($recursive, $url) {

                $promises = null;
                if (!$this->should_stop()) {
                    $archiveData = $this->getDataOnPage($crawler);
                    if ($recursive) {
                        foreach ($archiveData['pagination'] as $page) {
                            if ($this->add_visited_page($page)) {
                                $promises[] = $this->parseArchivePageRecursive($page,$recursive);
                            }
                        }
                    }
                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page']);
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->then(null,function(\Throwable $throwable){

                info_error($throwable);
                throw $throwable;
            });
    }

    public function getDataOnPage(Crawler $crawler)
    {
        $items      = $crawler
            ->query($this->donor->loop_item)
            ->map(function (Crawler $crawler)  {
                return [
                    'title'      => $crawler->query($this->donor->loop_title)->getText(),
                    'address'    => $crawler->query($this->donor->loop_address)->getText(),
                    'donor_page' => $crawler->query($this->donor->loop_link)->link()->getUri(),
                    'donor'      => $this,
                    'donor_id'   => $this->donor->id,
                ];
            });
        $pagination = $this->getUniqueLinks($crawler->query($this->donor->archive_pagination));
        return [
            'items'      => $items,
            'pagination' => $pagination,
        ];
    }

    public function getUniqueLinks(Crawler $crawler)
    {
        $pagination = [];
        $crawler
            ->each(function (Crawler $crawler) use (&$pagination) {
                $url = $crawler->link()->getUri();
                if (!in_array($url, $pagination)) {
                    $pagination[] = $url;
                }
            });
        return $pagination;
    }


    public function getCompanyPhone(Crawler $crawler)
    {
        return get_phones_from_text(
            $crawler->query($this->donor->single_tel)->getText()
        );
    }

    public function getDataOnSinglePage(Crawler $crawler)
    {

        $reviews = [];
        return $this->iterateReviews($crawler,function($data) use(&$reviews){
            $reviews = array_merge($reviews, $data);
        })->then(function() use (&$reviews, $crawler) {
            return (object) [
                'site'       => $this->getSite($crawler),
                'reviews'    => $reviews,
                'phone'      => $this->getCompanyPhone($crawler),
                'address'    => $this->getAddress($crawler),
                'title'      => $this->getTitle($crawler),
                'city'       => $this->getCity($crawler),
                'donor_page' => $crawler->getBaseHref(),
                'donor_id'   => $this->donor->id,
            ];
        });

    }
    public function iterateReviews(Crawler $crawler,$fn){
        $fn($this->getReviewsOnPage($crawler));
        $urls = $this->getUniqueLinks($crawler->query($this->donor->reviews_pagination));
        $promises = null;
        foreach ($urls as $url) {
            $promises[] = $this->getPage($url, 'getReviewsByUrls',true)
                ->then(function(Crawler $crawler) use ($fn) {
                    return $fn($this->getReviewsOnPage($crawler));
                });
        }
        return \GuzzleHttp\Promise\all($promises);
    }
    public function getSite(Crawler $crawler){
        $site_text = $crawler->query($this->donor->single_site)->getText();
        if($this->donor->decode_url){//carsguru.net
            $site_text = urldecode(base64_decode($site_text));
        }
        $site       = get_links_from_text($site_text);
        $site       = implode(', ', $site);
        return $site;
    }
    public function getTitle(Crawler $crawler){
        return $crawler->query($this->donor->single_title)->getText();
    }
    public function getAddress(Crawler $crawler){
        $text = $crawler->query($this->donor->single_address)->getText();
        $raw_addresses = [];
        if($this->donor->s_address_regex){
            preg_match($this->donor->s_address_regex,$text,$raw_addresses);
        } else {
            $raw_addresses = [$text];
        }
        $found = preg_replace('/\bадрес\b\s*:?/ui','',$raw_addresses);
        $found = trim_and_implode($found,PHP_EOL);
        return $found;
    }
    public function getCity(Crawler $crawler){
        $text = $crawler->query($this->donor->single_city)->getText();
        $raw_cities = [];
        if($this->donor->s_city_regex){
            preg_match($this->donor->s_city_regex,$text,$raw_cities);
        } else {
            $raw_cities = [$text];
        }
        $found = preg_replace('/\bгород\b\s*:?/ui','',$raw_cities);
        $found = trim_and_implode($found,PHP_EOL);
        return $found;
    }
    public function getReviewsOnPage(Crawler $crawler)
    {
        return (array)$crawler
            ->query($this->donor->reviews_all)
            ->map(function (Crawler $crawler){

                $text  = $this->getReviewText($crawler);
                $title = $crawler->query($this->donor->reviews_title)->getText();

                $donor_comment_id = $crawler->query($this->donor->reviews_id)->getText();
                if ($donor_comment_id === null) {
                    $donor_comment_id = md5($text . $title);
                }
                return [
                    'text'             => $text,
                    'rating'           => $crawler->query($this->donor->reviews_rating)->getText(),
                    'title'            => $title,
                    'name'             => $crawler->query($this->donor->reviews_name)->getText(),
                    'donor_comment_id' => $donor_comment_id,
                    'donor_id'         => $this->donor->id,
                    'donor_link'       => $crawler->getUri(),
                ];
            });
    }

    public function getReviewText(Crawler $crawler)
    {
        $html = $crawler->query($this->donor->reviews_text)->html();
        $html = strip_tags($html, '<blockquote><b>');
        $html = str_replace($this->donor->reviews_ignore_text, '', $html);
        return $html;
    }

}