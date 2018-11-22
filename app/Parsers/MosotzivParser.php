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

    public function iteratePages3($fn, $url = '',$params = [],$page = 1)
    {

        $this->add_visited_page($page);
        $params = array_merge($params, [
            'donor_id'    => $this->donor->id,
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
        ]);
        return $this->fetch('POST', $url, $params)
            ->then('json_decode')
            ->then(function ($json) use ($params, $page, $fn) {

                $promises = [];
                if (!$this->should_stop()) {

                    $promises = null;
                    $archiveData = $this->parseJson($json);
                    $promises[] = $fn($archiveData,$page);

                    $max_page = ceil($json->found_posts / $this->per_page);
                    for ($i = 1; $i <= $max_page; $i++) {
                        if ($this->add_visited_page($i)) {
                            $promises[] = $this->iteratePages3($fn, '',$params,$i);
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->otherwise(function (\Throwable $throwable) {

                info_error($throwable);
                throw $throwable;
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
        ];
    }

}