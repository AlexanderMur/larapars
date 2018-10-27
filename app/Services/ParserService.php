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

    public $new_parsed_companies_count = 0;
    public $updated_companies_count = 0;
    public $new_company_count = 0;
    public $updated_company_count = 0;
    public $new_reviews_count = 0;
    public $deleted_reviews_count = 0;
    public $restored_reviews_count = 0;
    public $counts = [];
    public $donors = [];
    public $visitedPages = [];
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

    public function __construct()
    {
        $this->parserClass  = new ParserClass();
        $this->client       = new Client();
        $this->parserClient = new ParserClient();
        $this->counts       = [
            'new_parsed_companies_count' => 0,
            'updated_companies_count'    => 0,
            'new_reviews_count'          => 0,
            'deleted_reviews_count'      => 0,
            'restored_reviews_count'     => 0,
        ];
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        try {
            if ($this->is_started) {
                $this->parserClient->run();
                info('ENDPARSING');
                $this->parser_task->log('bold', '
            Работа парсера завершена. Найдено новых компаний: (' . $this->new_parsed_companies_count . ')
            Обновлено компаний: (' . $this->updated_companies_count . ')
            Новых отзывов: (' . $this->new_reviews_count . ')
            Удалено отзывов: (' . $this->deleted_reviews_count . ')
            Возвращено отзывов: (' . $this->restored_reviews_count . ')
            ', null);
                $this->stop();
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }

    /**
     * @param $link
     * @param Donor $donor
     * @param null $proxy
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPage($link, Donor $donor, $proxy = null)
    {
        $this->mb_register_donor($donor);

        $this->parser_task->log('get', '', $link);
        return $this->parserClient
            ->addGet($link, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.67 Safari/537.36',
                ],
                'connect_timeout' => 10,
                'proxy'   => [
                    'http'  => '127.0.0.1:8080',
                    'https' => '127.0.0.1:8080',
                ],
                'verify'  => false,
            ])
            ->then(function (Response $response) use ($link, $donor) {
                $html = $response->getBody()->getContents();
                $html = str_replace($donor->replace_search, $donor->replace_to, $html);
                info(memory_get_usage(true) / 1024 / 1024 . 'MB');
                return new Crawler($html, $link);
            }, function (RequestException $exception) use ($donor, $link) {
                info('AAAAAAAAAA OSHIBKAA');
                $this->parser_task->log($exception->getCode(), 'Ошибка с соедением: ' . $exception->getCode(), $link);
                switch ($exception->getCode()) {
                    case 404:
                        $this->parser_task->log('404', 'not_found', $link);
                        throw $exception;
                        break;
                    case 0:
                        info($link . ' trying another proxy...');
                        return $this->getPage($link, $donor);
                        break;
                }
                throw $exception;
            });
    }

    public function parseCompanyByUrl($url, Donor $donor)
    {
        $this->mb_start($url);

        return $this->getPage($url, $donor)
            ->then(function (Crawler $crawler) use ($donor) {
                $data = $this->parserClass->getDataOnSinglePage($crawler, $donor);
                $this->handleParsedCompany($data, $donor);
            });
    }

    public function parseArchivePageByUrl($url, Donor $donor)
    {
        $this->mb_start($url);

        $this->getPage($url, $donor)
            ->then(function (Crawler $crawler) use ($donor, $url) {
                $archiveData = $this->parserClass->getDataOnPage($crawler, $donor);
                foreach ($archiveData['pagination'] as $page) {
                    if (!in_array($page, $this->visitedPages) && $this->is_parsing()) {
                        $this->visitedPages[] = $page;
                        $this->parseArchivePageByUrl($page, $donor);
                    }
                }
                foreach ($archiveData['items'] as $page) {
                    if ($this->is_parsing()) {
                        $this->parseCompanyByUrl($page['donor_page'], $donor);
                    };
                }
            });
    }


    /**
     * @param Donor[] $donors
     */
    public function parseDonors($donors = null)
    {
        if ($donors === null) {
            $donors = Donor::all();
        }
        $this->mb_start($donors);
        foreach ($donors as $donor) {
            if ($this->is_parsing()) {
                $this->parseArchivePageByUrl($donor->link, $donor);
                $this->parser_task->progress()->increment('progress');
            }
        }
    }

    public function parseCompaniesByUrls($urls, $need_mapping = true)
    {
        if ($need_mapping) {
            $urls = $this->mapUrlsWithDonor($urls);
        }
        $this->mb_start($urls);
        foreach ($urls as $url) {
            if ($this->is_parsing()) {
                $this->parseCompanyByUrl($url['donor_page'], $url['donor']);
                $this->parser_task->progress()->increment('progress');
            }
        }
    }

    public function parseArchivePagesByUrls($urls, $need_mapping = true)
    {
        if ($need_mapping) {
            $urls = $this->mapUrlsWithDonor($urls);
        }
        $this->mb_start($urls);
        foreach ($urls as $url) {
            if ($this->is_parsing()) {
                $this->parseArchivePageByUrl($url['donor_page'], $url['donor']);
                $this->parser_task->progress()->increment('progress');
            }
        }
    }

    public function mb_start($urls = [])
    {
        if (!$this->is_started) {
            info('start');
            $this->proxies     = setting()->getProxies();
            $this->parser_task = ParserTask::create();
            $this->parser_task->createProgress(count($urls));
            $this->parser_task->log('bold', 'Запуск парсера', null);

            if (!is_dir($this->folder_path())) {
                mkdir($this->folder_path());
            }
            if (!file_exists($this->file_path())) {
                fopen($this->file_path(), 'w');
            }

            config()->set('debugbar.collectors.db', false);
            $this->is_started = true;
        }
    }

    public function stop()
    {
        if ($this->is_parsing()) {
            unlink($this->file_path());
        }
    }

    public function is_parsing()
    {
        return file_exists($this->file_path());
    }

    public function file_path()
    {
        return $this->folder_path() . '/' . 'progress';
    }

    public function folder_path()
    {
        return storage_path('parser');
    }


    /**
     * @param ParsedCompany $parsed_company
     * @param array $new_company
     * @param Donor $donor
     */
    function handleParsedReviews($parsed_company, $new_company, Donor $donor)
    {
        $this->mb_register_donor($donor);
        $reviews = Review::withTrashed()->where('donor_link', $new_company['donor_page'])->get();

        $new_review_ids = Arr::pluck($new_company['reviews'], 'donor_comment_id');


        foreach ($reviews as $review) {
            $in_array = in_array($review->donor_comment_id, $new_review_ids);
            if (!$in_array && $review->deleted_at === null) {
                $review->delete();
                $this->deleted_reviews_count++;
                $this->donors[$donor->id]['deleted_reviews_count']++;
                $this->parser_task->log('review_deleted', 'Отзыв удален', $parsed_company);
            }
            if ($in_array && $review->deleted_at !== null) {
                $review->restore();
                $this->restored_reviews_count++;
                $this->donors[$donor->id]['restored_reviews_count']++;
                $this->parser_task->log('review_restored', 'Отзыв возвращен', $parsed_company);
            }
        }

        $new_reviews = collect();
        foreach ($new_company['reviews'] as $new_review) {
            if (!$reviews->contains('donor_comment_id', $new_review['donor_comment_id'])) {
                $new_reviews[] = new Review($new_review);
            }
        }

        $this->new_reviews_count                       += $new_reviews->count();
        $this->donors[$donor->id]['new_reviews_count'] += $new_reviews->count();
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
     */
    public function handleParsedCompany($new_company, Donor $donor)
    {
        if (!$new_company) {
            return null;
        }
        $this->mb_register_donor($donor);
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
                    $this->updated_companies_count++;
                    $this->donors[$donor->id]['updated_companies_count']++;
                }
            }
        } else {
            $this->parser_task->log('company_created', 'Новая компания: ' . $parsed_company->title, $parsed_company);
            $this->new_parsed_companies_count++;
            $this->donors[$donor->id]['new_parsed_companies_count']++;
        }

        $this->handleParsedReviews($parsed_company, $new_company, $donor);
        return $parsed_company;
    }


    public function mapUrlsWithDonor($urls)
    {
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
    }

    public function getStatistics()
    {

        return [
            'parsed_companies_count' => ParsedCompany::where('company_id', null)->count(),
            'reviews_count'          => Review::where('good', null)->count(),
            'rated_reviews_count'    => Review::where('good', '!=', null)->count(),

        ];
    }

    public function mb_register_donor(Donor $donor)
    {
        if (!isset($this->donors[$donor->id])) {
            $this->donors[$donor->id] = [
                'link'                       => $donor->link,
                'new_parsed_companies_count' => 0,
                'updated_companies_count'    => 0,
                'new_reviews_count'          => 0,
                'deleted_reviews_count'      => 0,
                'restored_reviews_count'     => 0,
                'tries'                      => 0,
            ];
        }
    }
}
