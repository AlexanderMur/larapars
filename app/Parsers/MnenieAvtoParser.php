<?php


namespace App\Parsers;




class MnenieAvtoParser extends SelectorParser
{

    public $per_page = 30;
    public function parseArchivePageRecursive($url, $recursive = true, $page = 1)
    {


        $this->add_visited_page($page);
        return $this->fetch('GET', $url . '?per_page='.$this->per_page, [
            'donor_id'    => $this->donor->id,
            'methodName'  => __FUNCTION__,
        ])
            ->then('json_decode')
            ->then(function ($json) use ($recursive,  $url) {

                $promises = [];
                if (!$this->should_stop()) {


                    $archiveData = $this->parseJson($json);

                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page']);
                        }
                    }
//                    if ($recursive) {
//                        $max_page = ceil($json->data->total / $this->per_page);
//                        for ($i = 1; $i <= $max_page; $i++) {
//                            if ($this->add_visited_page($i)) {
//                                $promises[] = $this->parseArchivePageRecursive($this->donor->link,  $recursive, $i);
//                            }
//                        }
//                    }
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->then(null, function (\Throwable $throwable) {

                info_error($throwable);
                throw $throwable;
            });
    }



    public function parseJson($json)
    {
        $items = [];
        foreach ($json as $listing) {
            $items[] = [
                'title'      => $listing->title->rendered,
                'address'    => null,
                'donor_page' => $listing->link,
                'donor'      => $this,
                'donor_id'   => $this->donor->id,
            ];
        }
        return [
            'items' => $items,
            'pagination' => [],
        ];
    }
}