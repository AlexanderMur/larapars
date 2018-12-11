<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 16.11.2018
 * Time: 18:35
 */

namespace App\Parsers;


use App\Components\Crawler;

class EdgoParser extends SelectorParser
{
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


    public function getPage2($params, $options = []){
        return $this->fetch('POST', $this->donor->link, [
            'methodName' => 'parseAll',
            'cookies'     => cookies([
                'antibot-hostia' => 'true',
            ], $this->donor->link),
            'form_params' => [
                'action'   => 'ajax_search_tags',
                'cat_id'   => 15,
                'loc_id'   => '',
                'pageno'   => $params['page'] ?? 0,
                'skeywork' => '',
            ],
        ])
            ->then('json_decode')
            ->then(function ($json) use ($params) {
                $crawler = new Crawler($json->html, $this->donor->link);
                $archiveData = $this->getDataOnPage($crawler);

                $archiveData['max_page'] = ceil($json->found / 15);
                return $archiveData;
            });

    }
}