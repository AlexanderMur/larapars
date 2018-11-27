<?php


namespace App\Parsers;


use App\Components\Crawler;


class AoolParser extends SelectorParser
{

    public $per_page = 100;

    public function iteratePages($fn, $url = '', $params = [], $page = 1)
    {


        $this->add_visited_page($page);
        $params = array_merge($params, [
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
//            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
        ]);
        return $this->fetch('POST', $this->donor->link, $params)
            ->then('json_decode')
            ->then(function ($json) use ($params, $page, $fn) {

                $promises = null;
                $crawler  = new Crawler($json->html, $this->donor->link);

                $archiveData = $this->getDataOnPage($crawler);
                $promises[]  = $fn($archiveData,$page);
                if (!$this->should_stop()) {

                    $max_page = ceil($json->total_found / $this->per_page);
                    for ($i = 1; $i <= $max_page; $i++) {
                        if ($this->add_visited_page($i)) {
                            $promises[] = $this->iteratePages($fn, '',$params,$i);
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->otherwise(function(\Throwable $throwable) use ($page) {
                $this->parserTask->log('error',$throwable->getMessage(),$page);
                info_error($throwable);
                throw  $throwable;
            });
    }

}