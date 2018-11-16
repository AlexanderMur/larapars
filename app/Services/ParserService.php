<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 03.10.2018
 * Time: 19:51
 */

namespace App\Services;


use App\Components\ParserClient;
use App\Models\ParsedCompany;
use App\Models\ParserTask;
use App\Models\Review;

class ParserService
{

    public $parserClass;
    public $is_started;


    public $donors = [];
    public $visitedPages = [];


    public $pagesInQueue = [];

    public $start = 0;
    /**
     * @var ParserTask $parser_task
     */
    public $parser_task;
    public $parsed_companies_counts = [];
    public $client;
    public $proxies;
    /**
     * @var ParserClient
     */
    public $parserClient;
    public $concurrency = 25;
    public $archivePagesInQueue = [];
    public $companyPagesInQueue = [];
    protected $id;
    protected $lastConcurrencyUpdate;
    protected $tries;
    public $canceled;





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
