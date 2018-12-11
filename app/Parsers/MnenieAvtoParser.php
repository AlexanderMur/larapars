<?php


namespace App\Parsers;


class MnenieAvtoParser extends SelectorParser
{

    public $per_page = 30;


    public function getCompanies($params, $options = []){
        return $this->fetch('GET', $this->donor->link . '?per_page=' . $this->per_page, $options)
            ->then('json_decode')
            ->then(function ($json){
                return $this->parseJson($json);
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
            'max_page' => null,
        ];
    }

}