<?php


namespace App\Components;


use App\Models\Parser;
use App\Services\LogService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use libphonenumber\PhoneNumberUtil;

class ParserClass
{
    public $link;
    public $client;
    public $items = array();
    /**
     * @var Parser
     */
    public $parser;

    public function __construct(Parser $parser)
    {
        $this->client = new Client();

        $this->parser = $parser;
    }

    public function parseData(Parser $parser, $how_many = 10)
    {

        LogService::log(
            'ok',
            'загрузка компаний...',
            $parser->donor->link,
            $parser->id
        );


        $response = $this->client->get($parser->donor->link);
        $html     = $response->getBody()->getContents();
        $html     = str_replace($parser->replace_search, $parser->replace_to, $html);

        $crawler     = new Crawler($html, $parser->donor->link);
        $this->items = $this->getDataOnPage($crawler, $parser, $how_many);

        LogService::log(
            'ok',
            'загрузка компаний завершена (' . count($this->items) . ') ',
            $parser->donor->link,
            $parser->id
        );

        return $this->items;
    }

    public function getDataOnPage(Crawler $crawler, Parser $parser, $how_many)
    {
        $items = $crawler
            ->query($parser->loop_item)
            ->filter(function ($c, $i) use ($how_many) {
                return $i < $how_many;
            })
            ->map(function (Crawler $crawler, $i) use ($parser) {
                $title            = $crawler->query($parser->loop_title)->getText();
                $address          = $crawler->query($parser->loop_address)->getText();
                $single_page_link = $crawler->query($parser->loop_link)->link()->getUri();
                return [
                    'title'            => $title,
                    'address'          => $address,
                    'single_page_link' => $single_page_link,
                    'parser'           => $parser,
                ];
            });
        return $items;
    }

    public function getDataOnSinglePage(Crawler $crawler, Parser $parser)
    {

        $site    = $crawler->query($parser->single_site)->getText();
        $reviews = $this->getReviewsOnPage($crawler, $parser);
        $address = $crawler->query($parser->single_address)->getText();

        //getting phone...
        $tel        = $crawler->query($parser->single_tel)->getText();
        $numbers    = PhoneNumberUtil::getInstance()->findNumbers($tel, 'RU');
        $numbersArr = [];
        foreach ($numbers as $number) {
            $numbersArr[] = $number->rawString();
        }

        return [
            'site'    => $site,
            'reviews' => $reviews,
            'phones'  => $numbersArr,
            'address' => $address,
        ];
    }

    public function getReviewsOnPage(Crawler $crawler, Parser $parser)
    {
        return $crawler
            ->query($parser->reviews_all)
            ->map(function (Crawler $crawler) use ($parser) {
                $text             = $crawler->query($parser->reviews_text)->getText();
                $text             = str_replace($parser->reviews_ignore_text, '', $text);
                $rating           = $crawler->query($parser->reviews_rating)->getText();
                $title            = $crawler->query($parser->reviews_title)->getText();
                $name             = $crawler->query($parser->reviews_name)->getText();
                $donor_comment_id = $crawler->query($parser->reviews_id)->getText();
                if ($donor_comment_id === null) {
                    $donor_comment_id = md5($text . $title);
                }
                return [
                    'text'             => $text,
                    'rating'           => $rating,
                    'title'            => $title,
                    'name'             => $name,
                    'donor_comment_id' => $donor_comment_id,
                    'donor_id'         => $parser->donor->id,
                    'donor_link'       => $crawler->getUri(),
                ];
            });
    }

    public function parseSinglePage($link, Parser $parser)
    {
        return $this->client->getAsync($link)->then(function (Response $response) use ($link, $parser) {
            $html = $response->getBody()->getContents();
            $html = str_replace($parser->replace_search, $parser->replace_to, $html);

            $crawler = new Crawler($html, $link);
            $data    = $this->getDataOnSinglePage($crawler, $parser);
            return $data;
        });
    }

    public function parseSinglePages(Parser $parser)
    {
        $pages = [];
        foreach ($this->items as $key => $item) {
            $single_page_link = $item['single_page_link'];


            $parser->log(
                'ok',
                'загрузка отзывов...',
                $single_page_link
            );


            $this->parseSinglePage($single_page_link, $parser)->then(function ($data) use ($key, $parser, &$pages, $single_page_link) {
                $this->items[$key] = array_merge($this->items[$key], $data);


                $parser->log('ok', 'загрузка отзывов завершена (' . count($data['reviews']) . ')', $single_page_link);

            })->wait();

        }
        return $this->items;
    }
}