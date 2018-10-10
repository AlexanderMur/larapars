<?php


namespace App\Components;


use App\Models\Donor;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
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

    /**
     * parse archive page
     *
     * @param $link
     * @param Donor $donor
     * @param int $how_many
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getCompanyUrlsOnArchive($link, Donor $donor, $how_many = 2)
    {

        return $this->client->getAsync($link)
            ->then(function (Response $response) use ($link, $how_many, $donor) {
                $html    = $response->getBody()->getContents();
                $html    = str_replace($donor->replace_search, $donor->replace_to, $html);
                $crawler = new Crawler($html, $link);
                return $this->getDataOnPage($crawler, $donor, $how_many);
            });
    }

    /**
     * parse single page
     *
     * @param $link
     * @param Donor $donor
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function parseCompany($link, Donor $donor)
    {
        return $this->client->getAsync($link)
            ->then(function (Response $response) use ($link, $donor) {
                $html    = $response->getBody()->getContents();
                $html    = str_replace($donor->replace_search, $donor->replace_to, $html);
                $crawler = new Crawler($html, $link);
                return $this->getDataOnSinglePage($crawler, $donor);
            });
    }

    public function getDataOnPage(Crawler $crawler, Donor $donor, $how_many)
    {
        $items = $crawler
            ->query($donor->loop_item)
            ->filter(function ($c, $i) use ($how_many) {
                return $i < $how_many;
            })
            ->map(function (Crawler $crawler, $i) use ($donor) {
                return [
                    'title'      => $crawler->query($donor->loop_title)->getText(),
                    'address'    => $crawler->query($donor->loop_address)->getText(),
                    'donor_page' => $crawler->query($donor->loop_link)->link()->getUri(),
                    'donor'      => $donor,
                    'donor_id'   => $donor->id,
                ];
            });
        return $items;
    }

    public function getPhonesFromText($text)
    {
        $numbers    = PhoneNumberUtil::getInstance()->findNumbers($text, 'RU');
        $numbersArr = [];
        foreach ($numbers as $number) {
            $numbersArr[] = $number->rawString();
        }
        return implode(', ', $numbersArr);
    }

    public function getCompanyPhone(Crawler $crawler, Donor $donor)
    {
        return $this->getPhonesFromText(
            $crawler->query($donor->single_tel)->getText()
        );
    }

    public function getDataOnSinglePage(Crawler $crawler, Donor $donor)
    {
        return [
            'site'       => $crawler->query($donor->single_site)->getText(),
            'reviews'    => $this->getReviewsOnPage($crawler, $donor),
            'phone'      => $this->getCompanyPhone($crawler, $donor),
            'address'    => $crawler->query($donor->single_address)->getText(),
            'title'      => $crawler->query($donor->single_title)->getText(),
            'donor_page' => $crawler->getBaseHref(),
            'donor_id'   => $donor->id,
        ];
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
        $html = strip_tags($html,'<blockquote>');

        $html = str_replace($donor->reviews_ignore_text, '', $html);
        return $html;
    }

    public function getDonorCommentId(Crawler $crawler, Donor $donor)
    {

    }

}