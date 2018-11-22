<?php


namespace App\Parsers;


use App\Components\Crawler;


class AvtosalonOtziviRf extends SelectorParser
{
    public function iterateReviews(Crawler $crawler,$fn,$params = [])
    {
        $fn($this->getReviewsOnPage($crawler));
        $page_numbers = $crawler->query($this->donor->reviews_pagination)
            ->map(function (Crawler $crawler) {
                return $crawler->getText();
            });

        $promises = null;
        if ($page_numbers) {
            $post_id = $this->getPostId($crawler);
            $url     = 'https://xn----7sbahc3a0aqgcc4ali0lb.xn--p1ai/component/jcomments/';
            foreach ($page_numbers as $page_number) {
                $promises[] = $this->fetch('POST', $url, [
                    'donor_id'    => $this->donor->id,
                    'methodName'  => __FUNCTION__,
                    'form_params' => [
                        'jtxf' => 'JCommentsShowPage',
                        'jtxa' =>
                            [
                                $post_id,
                                'com_content',
                                $page_number,
                            ],
                    ],
                ])
                    ->then(function ($json) use ($fn) {
                        $html = json_decode($json)[0]->d;
                        $html = str_replace('jcomments.updateList', '', $html);
                        $html = str_replace('\\', '', $html);

                        $crawler = new Crawler($html);
                        return $fn($this->getReviewsOnPage($crawler));
                    });
            }
        }
        return \GuzzleHttp\Promise\all($promises);
    }

    private function getPostId(Crawler $crawler)
    {
        $text = $crawler->query($this->donor->reviews_pagination)->attr('onclick');

        $text = str_replace('jcomments.showPage(', '', $text);
        return intval($text);
    }


}