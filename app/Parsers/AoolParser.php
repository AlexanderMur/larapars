<?php


namespace App\Parsers;


use App\Components\Crawler;


class AoolParser extends SelectorParser
{

    public $per_page = 100;
    public function parseArchivePageRecursive($url, $recursive = false, $page = 1)
    {


        $this->add_visited_page($page);
        return $this->fetch('POST', $url, [
            'donor_id'    => $this->donor->id,
            'methodName'  => __FUNCTION__,
            'form_params' => [
                'lang'              => '',
                'search_keywords'   => '',
                'search_location'   => '',
                'search_categories' => ['91'],
                'filter_job_type'   => [
                    '%d0%b0%d0%b2%d1%82%d0%be%d1%81%d0%b0%d0%bb%d0%be%d0%bd%d1%8b',
                    'vremenno',
                    'eda',
                    'internatura',
                    'magazini',
                    'nepolnaja-zanjatost',
                    'ostanovitsja',
                    'polnaja-zanjatost',
                    'poseshhenie',
                    'freelance',
                ],
                'per_page'          => $this->per_page,
                'orderby'           => 'featured',
                'order'             => 'DESC',
                'page'              => $page,
                'show_pagination'   => 'false',
            ],
        ])
            ->then('json_decode')
            ->then(function ($json) use ($recursive,  $url) {

                $promises = [];
                if (!$this->should_stop()) {
                    $crawler = new Crawler($json->html, $url);

                    $archiveData = $this->getDataOnPage($crawler);

                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page']);
                        }
                    }
                    if ($recursive) {
                        $max_page = ceil($json->total_found / $this->per_page);
                        for ($i = 1; $i <= $max_page; $i++) {
                            if ($this->add_visited_page($i)) {
                                $promises[] = $this->parseArchivePageRecursive($this->donor->link,  $recursive, $i);
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

}