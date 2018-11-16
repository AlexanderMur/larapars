<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 16.11.2018
 * Time: 18:35
 */

namespace App\Parsers;


use App\Components\Crawler;
use App\Models\Donor;

class EdgoParser extends SelectorParser
{
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

    public function parseArchivePageRecursive($url, Donor $donor, $recursive = true, $params = [])
    {


        $this->add_visited_page($url);
        return $this->fetch('POST', $url, [
            'donor_id'    => $donor->id,
            'methodName' => __FUNCTION__,
            'cookies' => cookies([
                'antibot-hostia'=>'true',
            ],$url),
            'form_params' => array_merge([
                'action'   => 'ajax_search_tags',
                'cat_id'   => 15,
                'loc_id'   => '',
                'pageno'   => 1,
                'skeywork' => '',
            ],$params),
        ])
            ->then('json_decode')
            ->then(function ($json) use ($recursive, $donor, $url) {

                $promises = [];
                if (!$this->should_stop()) {
                    $crawler = new Crawler($json->html, $url);

                    $archiveData = $this->getDataOnPage($crawler, $donor);

                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page'], $donor);
                        }
                    }
                    if ($recursive) {
                        for ($i = 1; $i <= $json->found / 15; $i++) {
                            if ($this->add_visited_page($i)) {
                                $promises[] = $this->parseArchivePageRecursive($donor->link, $donor, $recursive, [
                                    'pageno' => $i,
                                ]);
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