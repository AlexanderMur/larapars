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
use Carbon\Carbon;
use Complex\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;

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
    public $state;
    public $progress;
    public $progress_max;
    public $pid;
    protected $id;
    protected $lastConcurrencyUpdate;
    protected $tries;
    public $canceled;

    public function __construct()
    {
        $this->parserClass  = new ParserClass();
        $this->client       = new Client();
        $this->parserClient = new ParserClient();
        $this->pid          = getmypid();
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

    public function parseCompanyByUrl($url, Donor $donor)
    {


        return $this->getPage($url, $donor, 'company')
            ->then(function (Crawler $crawler) use ($url, $donor) {

                $data           = $this->parserClass->getDataOnSinglePage($crawler, $donor);
                $parsed_company = $this->handleParsedCompany($data, $donor);

                return $parsed_company;
            })
            ->then(null, function (\Throwable $e) use ($url) {
                $this->parser_task->log('info',$e->getMessage(),$url);
                info_error($e);
                throw $e;
            });
    }

    public function parseArchivePageByUrl($url, Donor $donor, $recursive = true)
    {

        $this->add_visited_page($url);
        return $this->getPage($url, $donor, 'archive')
            ->then(function (Crawler $crawler) use ($recursive, $donor, $url) {

                $promises = [];
                if (!$this->should_stop()) {
                    $archiveData = $this->parserClass->getDataOnPage($crawler, $donor);
                    if ($recursive) {
                        foreach ($archiveData['pagination'] as $page) {
                            if ($this->add_visited_page($page)) {
                                $promises[] = $this->parseArchivePageByUrl($page, $donor,$recursive);
                            }
                        }
                    }
                    foreach ($archiveData['items'] as $item) {
                        if ($this->add_visited_page($item['donor_page'])) {
                            $promises[] = $this->parseCompanyByUrl($item['donor_page'], $donor);
                        }
                    }
                }

                if (!in_array($donor->id, $this->archivePagesInQueue)) {
                    unset($this->visitedPages[$donor->id]);
                }

                return \GuzzleHttp\Promise\each($promises);
            })
            ->then(null,function(\Throwable $throwable){

                info_error($throwable);
                throw $throwable;
            });
    }
    /**
     * @param $link
     * @param Donor $donor
     * @param string $type
     * @param int $tries
     * @param null $delay
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPage($link, Donor $donor, $type = '', $tries = 0, $delay = null)
    {
        $http = $this->parser_task->createGet($link, $type, $donor->id);

        $random_proxy = $this->proxies[rand(0, count($this->proxies) - 1)];

        return $this->parserClient
            ->addGet($link, [
                'headers'     => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
                ],
                'verify'      => false,
                'proxy'       => [
                    'http'  => $random_proxy,
                    'https' => $random_proxy,
                ],
                'before_send' => function () use ($http) {
                    $http->update(['sent_at' => Carbon::now()]);
                },
                'delay'       => $delay,
            ])
            ->then(function (Response $response) use ($tries, $http, $donor) {
                $http->updateStatus($response->getStatusCode(), $response->getReasonPhrase());
                $html = $response->getBody()->getContents();
                if (str_contains($html, 'DDoS protection is checking your browser')) {
                    $tries++;
                    if ($tries < $this->tries) {
                        return $this->getPage($http->url, $donor, $http->channel, $tries, 10000);
                    }
                }
                $html = str_replace($donor->replace_search, $donor->replace_to, $html);
                return new Crawler($html, $http->url);
            }, function (\Exception $exception) use ($tries, $http, $donor) {

                $http->updateStatus($exception->getCode(), str_limit($exception->getMessage(), 191 - 3));

                switch ($exception->getCode()) {
                    case 404:
                        $this->parser_task->log('404', 'not_found', $http->url);
                        break;
                    case 0:
                        $tries++;
                        if ($tries < $this->tries) {
                            return $this->getPage($http->url, $donor, $http->channel, $tries);
                        }
                        break;
                }
                throw $exception;
            })->then(null, function ($e) {
                info('parser_error!!!: ' . $e->getMessage());
                throw $e;
            });
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
        $new_company = $this->filterNewCompany($new_company,$donor);
        $parsed_company = ParsedCompany::firstOrCreate(['donor_page' => $new_company['donor_page']], $new_company);
        if (!$parsed_company->wasRecentlyCreated) {
            info('not new!!' . $parsed_company->donor_page);
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

        foreach ($reviews as $review) {
            $this->filterReview($review);
        }
        //insert many reviews
        $parsed_company->saveReviews($new_reviews);
    }
    public function filterReview(Review $review)
    {
        $review->title = str_replace('| Ответить','',$review->title);
    }
    public function filterNewCompany($new_company,Donor $donor){

        $properties = [
            'phone',
            'site',
            'address',
            'city',
        ];

        $replace_words = [
            'адрес',
            'сайт',
            'город',
            'тел'
        ];

        foreach ($properties as $property) {
            if(isset($new_company[$property])){
                if (strlen($new_company[$property]) > 190) {
                    $new_company[$property] = '';
                    continue;
                }

                $new_company[$property] = $this->removeWords($new_company[$property],$replace_words);
                $new_company[$property] = trim($new_company[$property]);
            }
        }
        return $new_company;
    }
    public function removeWords($str,$words){
        $patterns = [];
        foreach ($words as $word) {
            $patterns[] = '(\b' . $word . '\b\.?:?)';
        }
        $pattern = implode('|',$patterns);
        return preg_replace("/$pattern/ui",'',$str);
    }


    public function add_visited_page($url)
    {
        if (!in_array($url, $this->visitedPages)) {
            $this->visitedPages[] = $url;
            return true;
        }
        return false;
    }

    public function create_task(ParserTask $task = null)
    {
        if (!$this->is_started) {

            info('start');

            $this->start       = microtime(true);
            $this->proxies     = setting()->getProxies();
            $this->parser_task = $task ?: ParserTask::create();
            $this->tries       = setting()->tries ?? 2;

            $this->parser_task->log('bold', 'Запуск парсера', null);
            $this->state = 'parsing';
            config()->set('debugbar.collectors.db', false);
            config()->set('debugbar.collectors.log', false);
            config()->set('debugbar.collectors.logs', false);
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
