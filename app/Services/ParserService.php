<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 03.10.2018
 * Time: 19:51
 */

namespace App\Services;


use App\CompanyHistory;
use App\Components\Crawler;
use App\Components\ParserClass;
use App\Components\ParserClient;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\ParserTask;
use App\Models\Review;
use Complex\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;

class ParserService
{

    public $parserClass;
    public $is_started;


    public $donors = [];
    public $visitedPages = [];


    public $pagesInQueue = [];

    public $count_pages = 0;
    public $start = 0;
    /**
     * @var ParserTask $parser_task
     */
    public $parser_task;
    public $parsed_companies_counts = [];
    public $client;
    protected $id;
    public $proxies;
    /**
     * @var ParserClient
     */
    public $parserClient;

    public $archivePagesInQueue = [];
    public $companyPagesInQueue = [];
    public $state;
    public $progress;
    public $progress_max;
    public $pid;

    public function __construct()
    {
        $this->parserClass  = new ParserClass();
        $this->client       = new Client();
        $this->parserClient = new ParserClient();
        $this->pid = getmypid();
        $this->parserClient->onEachRequest(function () {
            return !$this->should_stop();
        });
    }

    public function log_end()
    {
        if ($this->is_started) {
            info('ENDPARSING');

            $this->parser_task = $this->parser_task->getFresh();

            $this->parser_task->log('bold', '
            Работа парсера завершена. Найдено новых компаний: (' . $this->parser_task->new_companies_count . ')
            Обновлено компаний: (' . $this->parser_task->updated_companies_count . ')
            Новых отзывов: (' . $this->parser_task->new_reviews_count . ')
            Удалено отзывов: (' . $this->parser_task->deleted_reviews_count . ')
            Возвращено отзывов: (' . $this->parser_task->restored_reviews_count . ')
            ', null);
            if ($this->should_stop()) {
                $this->state = 'paused';
            } else {
                $this->state = 'done';
            }
            if (file_exists($this->stop_file_path())) {
                unlink($this->stop_file_path());
            }
            $this->saveProgress();
        }
    }

    public function run()
    {
        $this->parserClient->run();
    }

    /**
     * @param $link
     * @param Donor $donor
     * @param null $proxy
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPage($link, Donor $donor, $type = '')
    {
        $http = $this->parser_task->createGet($link, $type);
        $random_proxy = $this->proxies[rand(0, count($this->proxies) - 1)];
        return $this->parserClient
            ->addGet($link, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
                ],
                'verify'  => false,
                'proxy'=>[
                    'http'=>$random_proxy,
                    'https'=>$random_proxy,
                ],
                'curl' => [
                    CURLOPT_USERPWD => 'eekexm:AE7TdLHBEU',
                    CURLOPT_HTTPAUTH => CURLAUTH_ANY,
                ]
            ])
            ->then(function (Response $response) use ($http, $link, $donor) {

                $http->updateStatus($response->getStatusCode());


                $html = $response->getBody()->getContents();
                $html = str_replace($donor->replace_search, $donor->replace_to, $html);
//                info(memory_get_usage(true) / 1024 / 1024 . 'MB');
                $this->count_pages++;
                $speed = $this->count_pages / (microtime(true) - $this->start);
                info('page SPEED!!! ' . $speed . ' ' . $link);
                return new Crawler($html, $link);
            }, function (RequestException $exception) use ($http, $type, $donor, $link) {

                $http->updateStatus($exception->getCode());

                info('ERRORR!!!!!'.$exception->getCode(). ' ' .$link);
                $this->parser_task->log($exception->getCode(), 'Ошибка с соедением: ' . $exception->getCode(), $link);
                switch ($exception->getCode()) {
                    case 404:
                        $this->parser_task->log('404', 'not_found', $link);
                        throw $exception;
                        break;
                    case 0:
                        info($link . ' trying another proxy...');
                        return $this->getPage($link, $donor,$type);
                        break;
                }
                throw $exception;
            });
    }

    public function parseCompanyByUrl($url, Donor $donor)
    {
        $this->check_status();

        $this->companyPagesInQueue[$url] = $donor->id;


        return $this->getPage($url, $donor,'company')
            ->then(function (Crawler $crawler) use ($url, $donor) {

                $data           = $this->parserClass->getDataOnSinglePage($crawler, $donor);
                $parsed_company = $this->handleParsedCompany($data, $donor);

                unset($this->companyPagesInQueue[$url]);
                return $parsed_company;
            });
    }


    public function parseArchivePageByUrl($url, Donor $donor, callable $eachRequest = null)
    {

        $this->check_status();
        $this->add_visited_page($url, $donor->id);
        $this->archivePagesInQueue[$url] = $donor->id;
        return $this->getPage($url, $donor,'archive')
            ->then(function (Crawler $crawler) use ($eachRequest, $donor, $url) {

                if (!$this->should_stop()) {
                    $archiveData = $this->parserClass->getDataOnPage($crawler, $donor);
                    foreach ($archiveData['pagination'] as $page) {
                        if ($this->add_visited_page($page, $donor->id)) {
                            $this->parseArchivePageByUrl($page, $donor, $eachRequest);
                        }
                    }
                    foreach ($archiveData['items'] as $page) {
                        $this->parseCompanyByUrl($page['donor_page'], $donor)
                            ->then($eachRequest);
                    }
                    unset($this->archivePagesInQueue[$url]);
                    if (!in_array($donor->id, $this->archivePagesInQueue)) {
                        unset($this->visitedPages[$donor->id]);
                    }
                }

                $eachRequest();

                return $url;
            });
    }

    public function isDonorLoaded($donor_id)
    {
        return !in_array($donor_id, $this->archivePagesInQueue) && !in_array($donor_id, $this->companyPagesInQueue);
    }

    public function add_visited_page($url, $donor_id)
    {
        $this->visitedPages[$donor_id] = $this->visitedPages[$donor_id] ?? [];
        if (!in_array($url, $this->visitedPages[$donor_id])) {
            $this->visitedPages[$donor_id][] = $url;
            return true;
        }
        return false;
    }

    /**
     * @param Donor[] $donors
     */
    public function parseDonors($donors = null)
    {
        if ($donors === null) {
            $donors = Donor::all();
        }
        foreach ($donors as $donor) {
            $this->parseArchivePageByUrl($donor->link, $donor);
        }
        $this->parserClient->run();
    }

    public function parse($urls, $type)
    {
        $this->progress_max = count($urls);
        $urls               = $this->mapUrlsWithDonor($urls);
        $this->check_status();

        foreach ($urls as $url) {
            if ($type == 'companies') {
                $this->parseCompanyByUrl($url['donor_page'], $url['donor'])
                    ->then(function () {
                        $this->progress++;
                        $this->saveProgress();
                    });
            }
            if ($type == 'archivePages') {
                $this->parseArchivePageByUrl($url['donor_page'], $url['donor'], function ($url2) use ($url) {
                    if ($this->isDonorLoaded($url['donor']->id)) {
                        $this->progress++;
                    }
                    $this->saveProgress();
                });
            }
        }
        $this->parserClient->run();
        $this->log_end();
    }

    public function check_status()
    {
        if (!$this->is_started) {
            info('start');
            $this->start       = microtime(true);
            $this->proxies     = setting()->getProxies();
            $this->parser_task = ParserTask::create();
            $this->parser_task->log('bold', 'Запуск парсера', null);
            $this->state = 'parsing';
            $this->saveProgress();
            if (file_exists($this->stop_file_path())) {
                unlink($this->stop_file_path());
            }
            config()->set('debugbar.collectors.db', false);
            config()->set('debugbar.collectors.log', false);
            config()->set('debugbar.collectors.logs', false);
            config()->set('debugbar.enabled', false);
            $this->is_started = true;
        }
        if (file_exists($this->stop_file_path())) {
            $this->state = 'stopping';
        }
    }


    public function folder_path()
    {
        return storage_path('parser');
    }

    public function progress_file_path()
    {
        return $this->folder_path() . '/' . 'progress.json';
    }

    public function stop_file_path()
    {
        return $this->folder_path() . '/' . 'you-should-stop';
    }

    public function should_stop()
    {
        return file_exists($this->stop_file_path());
    }

    public function stop()
    {
        file_put_contents($this->stop_file_path(), null);
    }

    /**
     * @return object
     */
    public function getProgress()
    {
        $default = (object)[
            'archivePagesInQueue' => [],
            'companyPagesInQueue' => [],
            'visitedPages'        => [],
            'state'               => 'done',
            'progress'            => null,
            'progress_max'        => null,
            'send_links' => 0,
            'pid' => null,
        ];
        if (file_exists($this->progress_file_path())) {
            return \GuzzleHttp\json_decode(
                file_get_contents($this->progress_file_path())
            );
        }
        return $default;
    }

    public function saveProgress()
    {
        if (!is_dir($this->folder_path())) {
            mkdir($this->folder_path());
        }
        file_put_contents($this->progress_file_path(), \GuzzleHttp\json_encode([
            'archivePagesInQueue' => $this->archivePagesInQueue,
            'companyPagesInQueue' => $this->companyPagesInQueue,
            'send_links' => $this->parserClient->getPendingCount(),
            'visitedPages'        => $this->visitedPages,
            'state'               => $this->state,
            'progress'            => $this->progress,
            'progress_max'        => $this->progress_max,
            'pid' => $this->pid,
        ]));
    }

    public function resume()
    {

        $old_state          = $this->getProgress();
        $this->visitedPages = (array)$old_state->visitedPages;
        $urls               = $this->mapUrlsWithDonor(array_keys((array)$old_state->companyPagesInQueue));

        foreach ($urls as $url) {
            $this->parseCompanyByUrl($url['donor_page'], $url['donor'])
                ->then(function () {
                    $this->saveProgress();
                });
        }

        $this->parse(array_keys((array)$old_state->archivePagesInQueue), 'archivePages');


    }

    /**
     * @param ParsedCompany $parsed_company
     * @param array $new_company
     * @param Donor $donor
     */
    function handleParsedReviews($parsed_company, $new_company, Donor $donor)
    {
        $reviews = Review::withTrashed()->where('donor_link', $new_company['donor_page'])->get();

        $new_review_ids = Arr::pluck($new_company['reviews'], 'donor_comment_id');


        foreach ($reviews as $review) {
            $in_array = in_array($review->donor_comment_id, $new_review_ids);
            if (!$in_array && $review->deleted_at === null) {
                $review->delete();
                $this->parser_task->log('review_deleted', 'Отзыв удален', $parsed_company);
            }
            if ($in_array && $review->deleted_at !== null) {
                $review->restore();
                $this->parser_task->log('review_restored', 'Отзыв возвращен', $parsed_company);
            }
        }

        $new_reviews = collect();
        foreach ($new_company['reviews'] as $new_review) {
            if (!$reviews->contains('donor_comment_id', $new_review['donor_comment_id'])) {
                $new_reviews[] = new Review($new_review);
            }
        }

        if (count($new_reviews)) {
            $this->parser_task->log('new_reviews',
                'Добавлено новых отзывов (' . count($new_reviews) . ')', $parsed_company, count($new_reviews));
        }

        //insert many reviews
        $parsed_company->saveReviews($new_reviews);
    }

    /**
     * @param $new_company
     * @param Donor $donor
     * @return ParsedCompany|\Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function handleParsedCompany($new_company, Donor $donor)
    {
        if (!$new_company) {
            return null;
        }
        if (!isset($new_company['title']) && $new_company['title'] === null) {
            throw new Exception('company must have title');
        }
        if (strlen($new_company['address']) > 190) {
            $new_company['address'] = '';
        }
        $parsed_company = ParsedCompany::firstOrCreate(['donor_page' => $new_company['donor_page']], $new_company);
        if (!$parsed_company->wasRecentlyCreated) {
            foreach ($parsed_company->getActualAttrs() as $key => $attribute) {
                if (!isset($new_company[$key])) {
                    continue;
                }
                if ($attribute != $new_company[$key]) {
                    CompanyHistory::create([
                        'field'             => $key,
                        'old_value'         => $attribute,
                        'new_value'         => $new_company[$key],
                        'parsed_company_id' => $parsed_company->id,
                    ]);
                    $translate_field = __('company.' . $key);
                    $this->parser_task->log(
                        'company_updated',
                        "$parsed_company->title Поменяла поле \"$translate_field\" – было \"$attribute\" стало \"$new_company[$key]\"",
                        $parsed_company
                    );
                }
            }
        } else {
            $this->parser_task->log('company_created', 'Новая компания: ' . $parsed_company->title, $parsed_company);
        }

        $this->handleParsedReviews($parsed_company, $new_company, $donor);
        return $parsed_company;
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

    public function getStatistics()
    {
        $progress = (array)$this->getProgress();
        if ($this->should_stop()) {
            $progress['state'] = 'stopping';
        }
        return
            [
                'parsed_companies_count' => ParsedCompany::where('company_id', null)->count(),
                'reviews_count'          => Review::where('good', null)->count(),
                'rated_reviews_count'    => Review::where('good', '!=', null)->count(),
            ]
            + $progress;
    }



}
