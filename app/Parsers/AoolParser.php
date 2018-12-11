<?php


namespace App\Parsers;


use App\Components\Crawler;


class AoolParser extends SelectorParser
{

    public $per_page = 100;

    public function getPage2($params, $options = [])
    {
        return $this->post($this->donor->link, [
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
            'page'              => $params['page'] ?? 0,
            'show_pagination'   => 'false',
        ],$options)
            ->then('json_decode')
            ->then(function ($json) use ($params) {
                $crawler     = new Crawler($json->html, $this->donor->link);
                $archiveData = $this->getDataOnPage($crawler);

                $archiveData['max_page'] = ceil($json->total_found / $this->per_page);
                return $archiveData;
            });

    }
}