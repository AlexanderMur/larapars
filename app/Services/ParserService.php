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
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Arr;


class ParserService
{

    public $parserClass;
    public $is_started;

    public $new_company_count = 0;
    public $updated_company_count = 0;
    public $new_reviews_count = 0;
    public $deleted_reviews_count = 0;
    public $restored_reviews_count = 0;
    public $counts = [];

    public function __construct()
    {
        $this->parserClass = new ParserClass();
        $this->counts      = [
            'new_parsed_companies_count' => 0,
            'updated_companies_count'    => 0,
            'new_reviews_count'          => 0,
            'deleted_reviews_count'      => 0,
            'restored_reviews_count'     => 0,
        ];
    }

    public function __destruct()
    {
        if ($this->is_started) {
            LogService::log('bold', '
            Работа парсера завершена. Найдено новых компаний: (' . $this->counts['new_parsed_companies_count'] . ')
            Обновлено компаний: (' . $this->counts['updated_companies_count'] . ')
            Новых отзывов: (' . $this->counts['new_reviews_count'] . ')
            Удалено отзывов: (' . $this->counts['deleted_reviews_count'] . ')
            Возвращено отзывов: (' . $this->counts['restored_reviews_count'] . ')
            ');
            SettingService::set('last_parse_date', Carbon::now());
            SettingService::set('last_parse_counts', $this->counts);
        }
    }

    /**
     * @param ParsedCompany $parsed_company
     * @param array $new_company
     */
    function handleParsedReviews($parsed_company, $new_company)
    {
        $reviews = Review::withTrashed()->where('donor_link', $new_company['donor_page'])->get();

        $new_review_ids = Arr::pluck($new_company['reviews'], 'donor_comment_id');


        foreach ($reviews as $review) {
            $in_array = in_array($review->donor_comment_id, $new_review_ids);
            if (!$in_array && $review->deleted_at === null) {
                $review->delete();
                $this->counts['deleted_reviews_count']++;
                LogService::log(
                    'info',
                    'Отзыв удален',
                    $parsed_company->donor_page
                );
            }
            if ($in_array && $review->deleted_at !== null) {
                $review->restore();
                $this->counts['restored_reviews_count']++;
                LogService::log(
                    'info',
                    'Отзыв возвращен',
                    $parsed_company->donor_page
                );
            }
        }


        $not_existing_reviews = Arr::where($new_company['reviews'], function ($new_review) use ($reviews) {
            return !$reviews->contains('donor_comment_id', $new_review['donor_comment_id']);
        });
        $new_reviews          = collect();
        foreach ($not_existing_reviews as $not_existing_review) {
            $new_reviews[] = new Review($not_existing_review);
        }


        $this->counts['new_reviews_count'] += $new_reviews->count();
        LogService::log(
            'info',
            'Добавлено новых отзывов (' . count($new_reviews) . ')',
            $parsed_company->donor_page
        );

        //insert many reviews
        $parsed_company->saveReviews($new_reviews);
    }

    /**
     * @param $new_company
     */
    public function handleParsedCompany($new_company)
    {

        $parsed_company = ParsedCompany::firstOrCreate(['donor_page' => $new_company['donor_page']], $new_company);
        if (!$parsed_company->wasRecentlyCreated) {
            foreach ($parsed_company->getActualAttrs() as $key => $attribute) {
                if (!isset($new_company[$key])) {
                    continue;
                }
                if ($attribute != $new_company[$key]) {
                    $this->counts['updated_companies_count']++;
                    CompanyHistory::create([
                        'field'             => $key,
                        'old_value'         => $attribute,
                        'new_value'         => $new_company[$key],
                        'parsed_company_id' => $parsed_company->id,
                    ]);
                    $translate_field = __('company.' . $key);
                    LogService::log(
                        'info',
                        "$parsed_company->title Поменяла поле \"{{$translate_field}}\" – было \"{{$attribute}}\" стало \"$new_company[$key]\"",
                        $parsed_company->donor_page
                    );
                }
            }
        } else {
            LogService::log('info', 'Новая компания: ' . $parsed_company->title, $parsed_company->donor_page);
            $this->counts['new_parsed_companies_count']++;
        }
        $this->handleParsedReviews($parsed_company, $new_company);
    }

    public function parseCompanyByUrl($url, Donor $donor)
    {
        $this->is_started = true;
        LogService::log('info', 'парсим компанию...', $url);
        return $this->parserClass->parseCompany($url, $donor)
            ->then(function ($data) {
                $this->handleParsedCompany($data);

            });

    }

    public function parseCompaniesByUrls($urls, $need_mapping = true)
    {
        if ($need_mapping) {
            $urls = $this->mapUrlsWithDonor($urls);
        }
        foreach ($urls as $url) {

            $this->parseCompanyByUrl($url['donor_page'], $url['donor'])->wait();
        }
    }

    public function parseArchivePagesByUrls($urls, $need_mapping = true)
    {
        if ($need_mapping) {
            $urls = $this->mapUrlsWithDonor($urls);
        }

        foreach ($urls as $url) {
            LogService::log('info', 'получаем ссылки на компании из архива...', $url['donor_page']);
            $pages = $this->parserClass->getCompanyUrlsOnArchive($url['donor_page'], $url['donor'])->wait();

            LogService::log('ok', 'получили ссылки на компании из архива (' . count($pages) . ')', $url['donor_page']);
            $this->parseCompaniesByUrls(
                $pages,
                false
            );
        }
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
            'last_parse_date'        => SettingService::get('last_parse_date'),
            'last_parse_counts'      => SettingService::get('last_parse_counts', []),
        ];
    }
}
