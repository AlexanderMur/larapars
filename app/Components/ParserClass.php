<?php


namespace App\Components;


use Barryvdh\Debugbar\Facade;
use DebugBar\DebugBar;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use \App\Models\Parser;
use libphonenumber\PhoneNumberUtil;

class ParserClass
{
    public $link;
    public $client;
    public $items = array();

    public function __construct()
    {
        $this->client = new Client();
    }

    public function parseData(Parser $parser, $how_many)
    {
        $html = $this->client->get($parser->donor_link)->getBody()->getContents();
        $html = str_replace($parser->replace_search, $parser->replace_to, $html);

        $crawler = new Crawler($html, $parser->donor_link);
        return $this->items = $this->getDataOnPage($crawler, $parser, $how_many);
    }

    public function getDataOnPage(Crawler $crawler, Parser $parser, $how_many)
    {
        $items = $crawler
            ->query($parser->loop_item)
            ->filter(function ($c, $i) use ($how_many) {
                return $i < $how_many;
            })
            ->map(function (Crawler $crawler, $i) use ($parser) {
                $title = $crawler->query($parser->loop_title)->getText();
                $address = $crawler->query($parser->loop_address)->getText();
                $single_page_link = $crawler->query($parser->loop_link)->link()->getUri();
                return [
                    'title'            => $title,
                    'address'          => $address,
                    'single_page_link' => $single_page_link,
                ];
            });
        return $items;
    }

    public function getDataOnSinglePage(Crawler $crawler, Parser $parser)
    {

        $link = $crawler->query($parser->single_site)->getText();
        $reviews = $this->getReviewsOnPage($crawler, $parser);
        $address = $crawler->query('//div[@class="entry-content"]/p[contains(.,"Адрес:")]/text()[1]')->getText();
        $tel = $crawler->query('//div[@class="entry-content"]/p[contains(.,"Тел")]/text()[2]')->getText();
        $numbers = PhoneNumberUtil::getInstance()->findNumbers($tel, 'RU');
        $numbersArr = [];
        foreach ($numbers as $number) {
            $numbersArr[] = $number->rawString();
        }
        return [
            'link'    => $link,
            'reviews' => $reviews,
            'tel'     => $numbersArr,
            'address' => $address,
        ];
    }

    public function getReviewsOnPage(Crawler $crawler, Parser $parser)
    {
        return $crawler
            ->query($parser->reviews_all)
            ->map(function (Crawler $crawler) use ($parser) {
                $text = $crawler->query($parser->reviews_text)->getText();
                $text = str_replace($parser->reviews_ignore_text, '', $text);
                $rating = $crawler->query($parser->reviews_rating)->getText();
                $title = $crawler->query($parser->reviews_title)->getText();
                $name = $crawler->query($parser->reviews_name)->getText();
                return [
                    'text'   => $text,
                    'rating' => $rating,
                    'title'  => $title,
                    'name'   => $name,
                ];
            });
    }

    public function getSinglePage($link, Parser $parser)
    {
        return $this->client->getAsync($link)->then(function (Response $response) use ($parser) {
            $html = $response->getBody()->getContents();
            $html = str_replace($parser->replace_search, $parser->replace_to, $html);

            $crawler = new Crawler($html, $parser->donor_link);
            $data = $this->getDataOnSinglePage($crawler, $parser);
            return $data;
        });
    }

    public function parseSinglePages(Parser $parser)
    {
        $pages = [];
        foreach ($this->items as $key => $item) {
            $single_page_link = $item['single_page_link'];
            $this->getSinglePage($single_page_link, $parser)->then(function ($data) use ($key, $parser, &$pages, $single_page_link) {
                $this->items[$key] = array_merge($this->items[$key], $data);
            })->wait();
        }
        return $this->items;
    }
}