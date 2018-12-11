<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.11.2018
 * Time: 2:35
 */

namespace App\Parsers;


use App\Components\Crawler;


class SelectorParser extends Parser
{


    public function getCompanies($params, $options = []){
        return $this->getPage( $params['uri'], $options)
            ->then(function (Crawler $crawler) {
                return $this->getDataOnPage($crawler);
            });

    }
    public function getCompany($url, $options = []){
        return $this->getPage($url,$options)
            ->then(function (Crawler $crawler) use ($url) {
                return $this->getDataOnSinglePage($crawler);
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

        $loop_links      = $crawler
            ->query($this->donor->loop_links)
            ->map(function (Crawler $crawler)  {
                return [
                    'title'      => $crawler->getText(),
                    'address'    => null,
                    'donor_page' => $crawler->link()->getUri(),
                    'donor'      => $this,
                    'donor_id'   => $this->donor->id,
                ];
            });

        $items = array_merge($items,$loop_links);
        $pagination = $this->getUniqueLinks($crawler->query($this->donor->archive_pagination));
        return [
            'items'      => $items,
            'pagination' => $pagination,
            'max_page' => null,
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
    public function iterateReviews(Crawler $crawler,$fn,$params = []){
        $fn($this->getReviewsOnPage($crawler));
        $urls = $this->getUniqueLinks($crawler->query($this->donor->reviews_pagination));
        $promises = null;
        foreach ($urls as $url) {
            $promises[] = $this->getPage($url, $params)
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