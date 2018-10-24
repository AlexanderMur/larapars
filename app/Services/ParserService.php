<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 03.10.2018
 * Time: 19:51
 */

namespace App\Services;


use App\CompanyHistory;
use App\Components\ParserClass;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\ParserTask;
use App\Models\Review;
use GuzzleHttp\Exception\ClientException;
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
    public $donor_counts = [];
    public $visitedPages = [];
    /**
     * @var ParserTask $parser_task
     */
    public $parser_task;
    public $parsed_companies_counts = [];
    public $client;

    public function __construct()
    {
        $this->parserClass = new ParserClass();
        $this->parserClass->onError([$this,'handleError']);
        $this->counts      = [
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
        if ($this->is_started) {

            $this->parser_task->log('bold', '
            Работа парсера завершена. Найдено новых компаний: (' . $this->new_parsed_companies_count . ')
            Обновлено компаний: (' . $this->updated_companies_count . ')
            Новых отзывов: (' . $this->new_reviews_count . ')
            Удалено отзывов: (' . $this->deleted_reviews_count . ')
            Возвращено отзывов: (' . $this->restored_reviews_count . ')
            ', null, ['donor_stats' => $this->donor_counts]);
            $this->stop();
        }
    }

    public function parseCompanyByUrl($url, Donor $donor)
    {
        $this->mb_start($url);

        $this->parser_task->log('info', 'парсим компанию...', $url);
        return $this->parserClass->parseCompany($url, $donor)
            ->then(function ($data) use ($donor) {
                info('handle');
                $this->handleParsedCompany($data, $donor);
            },function (ClientException $exception) use ($url) {
                if($exception->getCode() == 404){
                    $this->parser_task->log('not_found','404',$url);
                } else {
                    $this->parser_task->log('error','Ошибка с подключением:'.$exception->getCode(),$url);
                }
                return [];
            });
    }

    public function parseArchivePageByUrl($url, Donor $donor)
    {
        $this->mb_start($url);

        $pending = $this->parser_task->log('info', 'парсинг ссылок из архива...',$url);
        try{
            $archiveData = $this->parserClass->getArchiveData($url, $donor)->wait();
        } catch (ClientException $exception){
            if($exception->getCode() == 404){
                $pending->updateStatus('not_found','404');
            } else {
                $pending->updateStatus('error','Ошибка с подключением:'.$exception->getCode());
            }
            $archiveData = [
                'pagination' => [],
                'items' => [],
            ];
        }

        $pending->updateStatus('ok', 'получили ссылки на компании из архива (' . count($archiveData['items']) . ')');


        foreach ($archiveData['pagination'] as $page) {
            if (!in_array($page, $this->visitedPages) && $this->can_parse()) {
                $this->visitedPages[] = $page;
                $this->parseArchivePageByUrl($page, $donor);
            }
        }
        foreach ($archiveData['items'] as $page) {
            if ($this->can_parse()) {
                $this->parseCompanyByUrl($page['donor_page'], $donor)->wait();
            };
        }
    }

    public function mb_start($urls = [])
    {
        if (!$this->is_started) {
            info('start');
            $this->parser_task = ParserTask::create();
            $this->parser_task->createProgress(count($urls));
            $this->parser_task->log('bold', 'Запуск парсера', null);
            if (!file_exists('check_file')) {
                fopen(storage_path(getmypid().'-check_file'), 'w');
            }
            config()->set('debugbar.collectors.db',false);
            $this->is_started = true;
        }
    }

    public function stop()
    {
        if (file_exists('check_file')) {
            unlink(storage_path(getmypid().'-check_file'));
        }
    }

    public function can_parse()
    {
        return file_exists(storage_path(getmypid().'-check_file'));
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
            if ($this->can_parse()) {
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

            if ($this->can_parse()) {
                $this->parseCompanyByUrl($url['donor_page'], $url['donor'])->wait();
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

            if ($this->can_parse()) {
                $this->parseArchivePageByUrl($url['donor_page'], $url['donor']);
                $this->parser_task->progress()->increment('progress');
            }
        }
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
                $this->donor_counts[$donor->id]['deleted_reviews_count']++;
                $this->parser_task->log('review_deleted', 'Отзыв удален', $parsed_company);
            }
            if ($in_array && $review->deleted_at !== null) {
                $review->restore();
                $this->restored_reviews_count++;
                $this->donor_counts[$donor->id]['restored_reviews_count']++;
                $this->parser_task->log('review_restored', 'Отзыв возвращен', $parsed_company);
            }
        }

        $new_reviews = collect();
        foreach ($new_company['reviews'] as $new_review) {
            if (!$reviews->contains('donor_comment_id', $new_review['donor_comment_id'])) {
                $new_reviews[] = new Review($new_review);
            }
        }

        $this->new_reviews_count                             += $new_reviews->count();
        $this->donor_counts[$donor->id]['new_reviews_count'] += $new_reviews->count();
        $this->parser_task->log('new_reviews',
            'Добавлено новых отзывов (' . count($new_reviews) . ')', $parsed_company, count($new_reviews));

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
        if(!$new_company){
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
                    $this->donor_counts[$donor->id]['updated_companies_count']++;
                }
            }
        } else {
            $this->parser_task->log('company_created', 'Новая компания: ' . $parsed_company->title, $parsed_company);
            $this->new_parsed_companies_count++;
            $this->donor_counts[$donor->id]['new_parsed_companies_count']++;
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
        if (!isset($this->donor_counts[$donor->id])) {
            $this->donor_counts[$donor->id] = [
                'link'                       => $donor->link,
                'new_parsed_companies_count' => 0,
                'updated_companies_count'    => 0,
                'new_reviews_count'          => 0,
                'deleted_reviews_count'      => 0,
                'restored_reviews_count'     => 0,
            ];
        }
    }
}
