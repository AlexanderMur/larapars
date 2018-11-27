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

    /**
     * @param $fn
     * @param string $url
     * @param array $params
     * @param int $page
     * @return \GuzzleHttp\Promise\Promise|\GuzzleHttp\Promise\PromiseInterface
     */
    public function iteratePages($fn, $url='', $params = [], $page = 1)
    {


        $this->add_visited_page($page);
        $params = array_merge($params, [
            'cookies'     => cookies([
                'antibot-hostia' => 'true',
            ], $this->donor->link),
            'form_params' => [
                'action'   => 'ajax_search_tags',
                'cat_id'   => 15,
                'loc_id'   => '',
                'pageno'   => $page,
                'skeywork' => '',
            ],
        ]);
        return $this->fetch('POST', $this->donor->link, $params)
            ->then('json_decode')
            ->then(function ($json) use ($params, $page, $fn) {

                $promises = null;
                $crawler = new Crawler($json->html, null);

                $archiveData = $this->getDataOnPage($crawler);

                $promises[] = $fn($archiveData,$page);
                if (!$this->should_stop()) {

                    $max_page = ceil($json->found / 15);
                    for ($i = 1; $i <= $max_page; $i++) {
                        if ($this->add_visited_page($i)) {
                            $promises[] = $this->iteratePages($fn,'', $params,$i);
                        }
                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            });
    }

}