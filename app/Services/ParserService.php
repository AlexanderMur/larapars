<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 03.10.2018
 * Time: 19:51
 */

namespace App\Services;


use App\Components\ParserClass;
use App\Components\ParserClient;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\ParserTask;
use App\Models\Review;
use GuzzleHttp\Client;

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

    public function __construct()
    {
        $this->parserClass  = new ParserClass();
        $this->client       = new Client();
        $this->parserClient = new ParserClient();
        $this->parserClient
            ->onEachRequest(function () {
                return !$this->should_stop();
            })
            ->onConcurrency(function () {
                if (microtime(true) - $this->lastConcurrencyUpdate > 2) {
                    $this->lastConcurrencyUpdate = microtime(true);
                    return $this->concurrency = setting()->concurrency;
                };
                return $this->concurrency;
            });
    }

    public function should_stop()
    {
        if ($this->parser_task->getState() === 'Paused' || $this->parser_task->getState() === 'Pausing') {
            $this->canceled = true;
        }
        return $this->canceled;
    }

    public function log_end()
    {
        if ($this->is_started) {
            info('ENDPARSING');

            $this->parser_task = $this->parser_task->getFresh();

            $this->parser_task->log('bold', '
            Работа парсера ' . ($this->canceled ? 'приостановлена' : 'завершена') . '. Найдено новых компаний: (' . $this->parser_task->new_companies_count . ')
            Обновлено компаний: (' . $this->parser_task->updated_companies_count . ')
            Новых отзывов: (' . $this->parser_task->new_reviews_count . ')
            Удалено отзывов: (' . $this->parser_task->deleted_reviews_count . ')
            Возвращено отзывов: (' . $this->parser_task->restored_reviews_count . ')
            ', null);
            if ($this->canceled) {
                $this->parser_task->setPaused();
            } else {
                $this->parser_task->setDone();
            }
        }
    }

    public function run()
    {
        $this->parserClient->run();
    }

    public function parse($urls, $type)
    {
        $urls = $this->mapUrlsWithDonor($urls);
        foreach ($urls as $url) {
            if ($type == 'companies') {
                $this->parseCompanyByUrl($url['donor_page'], $url['donor']);
            }
            if ($type == 'archivePages') {
                $this->parseArchivePageByUrl($url['donor_page'], $url['donor']);
            }
        }
    }

    public function mapUrlsWithDonor($urls)
    {
        if ($urls) {
            $donorsQuery = Donor::select();
            $mappedUrls  = [];
            foreach ($urls as $key => $url) {
                $host         = parse_url($url)['host'];
                $mappedUrls[] = ['donor_page' => $url, 'host' => $host];
                $donorsQuery->orWhere('link', 'like', "%$host%");
            }
            $donors = $donorsQuery->get()->keyBy(function (Donor $donor) {
                return parse_url($donor->link)['host'];
            });
            foreach ($mappedUrls as $key => $url) {
                $mappedUrls[$key]['donor'] = $donors[$url['host']];
            }
            return $mappedUrls;
        } else {
            return [];
        }
    }

    public function create_task(ParserTask $task = null)
    {
        if (!$this->is_started) {

            info('start');

            $this->start       = microtime(true);
            $this->proxies     = setting()->getProxies();
            $this->parser_task = $task ?: ParserTask::create();
            $this->tries       = setting()->tries ?? 2;

            config()->set('debugbar.enabled', false);
            $this->is_started = true;
            return $this->parser_task;
        }
    }

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
