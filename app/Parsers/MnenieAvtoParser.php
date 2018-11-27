<?php


namespace App\Parsers;


class MnenieAvtoParser extends SelectorParser
{

    public $per_page = 30;

    public function iteratePages($fn, $url = '', $params = [], $page = 1)
    {


        $this->add_visited_page($page);
        return $this->fetch('GET', $this->donor->link . '?per_page=' . $this->per_page, $params)
            ->then('json_decode')
            ->then(function ($json) use ($fn) {
                return $fn($this->parseJson($json));
            })
            ->otherwise(function (\Throwable $throwable) {

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
            'items'      => $items,
            'pagination' => [],
        ];
    }
}