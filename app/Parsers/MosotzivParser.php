<?php


namespace App\Parsers;


use App\Components\Crawler;
use App\Models\Donor;

class MosotzivParser extends SelectorParser
{

    public $per_page = 50;

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
    public function parseArchivePageRecursive($url, Donor $donor, $recursive = true, $page = 1)
    {

        $this->add_visited_page($page);
        return $this->fetch('POST', $url, [
            'donor_id'    => $donor->id,
            'methodName'  => __FUNCTION__,
            'form_params' => [
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
                'page'            => $page,
                'show_pagination' => 'true',
            ],
        ])
            ->then('json_decode')
            ->then(function ($json) use ($recursive, $donor, $url) {

                $promises = [];
                if (!$this->should_stop()) {

                    $archiveData = $this->parseJson($json, $donor);

                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page'], $donor);
                        }
                    }
                    if ($recursive) {
                        $max_page = ceil($json->found_posts / $this->per_page);
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

    public function parseJson($json, $donor)
    {
        $items = [];
        foreach ($json->listings as $listing) {
            $items[] = [
                'title'      => $listing->title,
                'address'    => null,
                'donor_page' => $listing->permalink,
                'donor'      => $donor,
                'donor_id'   => $donor->id,
            ];
        }
        return [
            'items' => $items,
            'pagination' => [],
        ];
    }

}