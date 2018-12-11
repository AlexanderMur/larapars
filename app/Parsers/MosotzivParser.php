<?php


namespace App\Parsers;


use App\Components\Crawler;


class MosotzivParser extends SelectorParser
{

    public $per_page = 50;

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

    public function getReviewText(Crawler $crawler)
    {
        $crawler->filter('script')->remove();
        $text = $crawler->query($this->donor->reviews_text)->getText();
        return $text;
    }


    public function getCompanies($params, $options = [])
    {
        return $this->post($this->donor->link, [
            'lang'            => '',
            'search_keywords' => '',
            'search_location' => '',
            'filter_job_type' => [
                'freelance',
                'full-time',
                'internship',
                'part-time',
                'temporary',
            ],
            'per_page'        => $this->per_page,
            'orderby'         => 'featured',
            'order'           => 'DESC',
            'page'            => $params['page'] ?? 0,
            'show_pagination' => 'true',
        ],$options)
            ->then('json_decode')
            ->then(function ($json) use ($params) {
                $archiveData             = $this->parseJson($json);
                $archiveData['max_page'] = ceil($json->found_posts / $this->per_page);

                return $archiveData;
            });

    }

    public function parseJson($json)
    {
        $items = [];
        foreach ($json->listings as $listing) {
            $items[] = [
                'title'      => $listing->title,
                'address'    => null,
                'donor_page' => $listing->permalink,
                'donor'      => $this,
                'donor_id'   => $this->donor->id,
            ];
        }
        return [
            'items'      => $items,
            'pagination' => [],
            'max_page' => null,
        ];
    }

}