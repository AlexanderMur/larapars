<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 03.10.2018
 * Time: 19:51
 */

namespace App\Services;


use App\Models\ParsedCompany;
use App\Models\Review;

class ParserService
{




    public function getStatistics()
    {
        return
            [
                'parsed_companies_count' => ParsedCompany::where('company_id', null)->count(),
                'reviews_count'          => Review::where('good', null)->count(),
                'rated_reviews_count'    => Review::where('good', '!=', null)->count(),
            ];
    }



}
