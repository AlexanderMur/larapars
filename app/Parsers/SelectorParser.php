<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.11.2018
 * Time: 2:35
 */

namespace App\Parsers;


use App\Components\Crawler;
use App\Models\Donor;
use App\Models\ParsedCompany;

class SelectorParser extends Parser
{
    public function parseCompanyByUrl($url, Donor $donor)
    {


        return $this->getPage($url, $donor, __FUNCTION__)
            ->then(function (Crawler $crawler) use ($url, $donor) {
                return $this->getDataOnSinglePage($crawler, $donor);
            })
            ->then(function($data) use ($donor) {
                $parsed_company = ParsedCompany::handleParsedCompany($data, $donor,$this->parserTask);
                info('done');
                return $parsed_company;
            })
            ->otherwise(function (\Throwable $e) use ($url) {
                $this->parserTask->log('info',$e->getMessage(),$url);
                info_error($e);
                throw $e;
            });
    }

    function parseAll(Donor $donor)
    {
        return $this->parseArchivePageRecursive($donor->link,$donor);
    }
    public function parseArchivePageRecursive($url, Donor $donor, $recursive = true, $params = [])
    {

        $this->add_visited_page($url);
        return $this->getPage($url, $donor, __FUNCTION__)
            ->then(function (Crawler $crawler) use ($recursive, $donor, $url) {

                $promises = null;
                if (!$this->should_stop()) {
                    $archiveData = $this->getDataOnPage($crawler, $donor);
                    if ($recursive) {
                        foreach ($archiveData['pagination'] as $page) {
                            if ($this->add_visited_page($page)) {
                                $promises[] = $this->parseArchivePageRecursive($page, $donor,$recursive);
                            }
                        }
                    }
                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page'], $donor);
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

    public function getDataOnPage(Crawler $crawler, Donor $donor)
    {
        $items      = $crawler
            ->query($donor->loop_item)
            ->map(function (Crawler $crawler, $i) use ($donor) {
                return [
                    'title'      => $crawler->query($donor->loop_title)->getText(),
                    'address'    => $crawler->query($donor->loop_address)->getText(),
                    'donor_page' => $crawler->query($donor->loop_link)->link()->getUri(),
                    'donor'      => $donor,
                    'donor_id'   => $donor->id,
                ];
            });
        $pagination = $this->getUniqueLinks($crawler->query($donor->archive_pagination));
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


    public function getCompanyPhone(Crawler $crawler, Donor $donor)
    {
        return get_phones_from_text(
            $crawler->query($donor->single_tel)->getText()
        );
    }

    public function getDataOnSinglePage(Crawler $crawler, Donor $donor)
    {

        $reviews = [];
        return $this->iterateReviews($crawler,$donor,function($data) use(&$reviews){
            $reviews = array_merge($reviews, $data);
        })->then(function() use (&$reviews, $donor, $crawler) {
            return (object) [
                'site'       => $this->getSite($crawler, $donor),
                'reviews'    => $reviews,
                'phone'      => $this->getCompanyPhone($crawler, $donor),
                'address'    => $this->getAddress($crawler, $donor),
                'title'      => $this->getTitle($crawler, $donor),
                'city'       => $this->getCity($crawler, $donor),
                'donor_page' => $crawler->getBaseHref(),
                'donor_id'   => $donor->id,
            ];
        });

    }
    public function iterateReviews(Crawler $crawler, Donor $donor,$fn){
        $fn($this->getReviewsOnPage($crawler, $donor));
        $urls = $this->getUniqueLinks($crawler->query($donor->reviews_pagination));
        $promises = null;
        foreach ($urls as $url) {
            $promises[] = $this->getPage($url, $donor, 'getReviewsByUrls',true)
                ->then(function(Crawler $crawler) use ($fn, $donor) {
                    return $fn($this->getReviewsOnPage($crawler, $donor));
                });
        }
        return \GuzzleHttp\Promise\all($promises);
    }
    public function getSite(Crawler $crawler, Donor $donor){
        $site_text = $crawler->query($donor->single_site)->getText();
        if($donor->decode_url){//carsguru.net
            $site_text = urldecode(base64_decode($site_text));
        }
        $site       = get_links_from_text($site_text);
        $site       = implode(', ', $site);
        return $site;
    }
    public function getTitle(Crawler $crawler, Donor $donor){
        return $crawler->query($donor->single_title)->getText();
    }
    public function getAddress(Crawler $crawler, Donor $donor){
        $text = $crawler->query($donor->single_address)->getText();
        $raw_addresses = [];
        if($donor->s_address_regex){
            preg_match($donor->s_address_regex,$text,$raw_addresses);
        } else {
            $raw_addresses = [$text];
        }
        $found = preg_replace('/\bадрес\b\s*:?/ui','',$raw_addresses);
        $found = trim_and_implode($found,PHP_EOL);
        return $found;
    }
    public function getCity(Crawler $crawler, Donor $donor){
        $text = $crawler->query($donor->single_city)->getText();
        $raw_cities = [];
        if($donor->s_city_regex){
            preg_match($donor->s_city_regex,$text,$raw_cities);
        } else {
            $raw_cities = [$text];
        }
        $found = preg_replace('/\bгород\b\s*:?/ui','',$raw_cities);
        $found = trim_and_implode($found,PHP_EOL);
        return $found;
    }
    public function getReviewsOnPage(Crawler $crawler, Donor $donor)
    {
        return (array)$crawler
            ->query($donor->reviews_all)
            ->map(function (Crawler $crawler) use ($donor) {

                $text  = $this->getReviewText($crawler, $donor);
                $title = $crawler->query($donor->reviews_title)->getText();

                $donor_comment_id = $crawler->query($donor->reviews_id)->getText();
                if ($donor_comment_id === null) {
                    $donor_comment_id = md5($text . $title);
                }
                return [
                    'text'             => $text,
                    'rating'           => $crawler->query($donor->reviews_rating)->getText(),
                    'title'            => $title,
                    'name'             => $crawler->query($donor->reviews_name)->getText(),
                    'donor_comment_id' => $donor_comment_id,
                    'donor_id'         => $donor->id,
                    'donor_link'       => $crawler->getUri(),
                ];
            });
    }

    public function getReviewText(Crawler $crawler, Donor $donor)
    {
        $html = $crawler->query($donor->reviews_text)->html();
        $html = strip_tags($html, '<blockquote><b>');

        $html = str_replace($donor->reviews_ignore_text, '', $html);
        return $html;
    }

}