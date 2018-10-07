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
use Illuminate\Support\Arr;


class ParserService
{

    public $parserClass;

    public function __construct()
    {
        $this->parserClass = new ParserClass();
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
            }
            if ($in_array && $review->deleted_at !== null) {
                $review->restore();
            }
        }


        $not_existing_reviews = Arr::where($new_company['reviews'], function ($new_review) use ($reviews) {
            return !$reviews->contains('donor_comment_id', $new_review['donor_comment_id']);
        });

        $new_reviews = collect();


        foreach ($not_existing_reviews as $not_existing_review) {
            $new_reviews[] = new Review($not_existing_review);
        }

        //insert many reviews
        $parsed_company->saveReviews($new_reviews);
    }

    /**
     * @param array $new_companies
     */
    public function handleParsedData($new_companies)
    {
        foreach ($new_companies as $new_company) {

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
                    }
                }
            }
            $this->handleParsedReviews($parsed_company, $new_company);
        }
    }

    public function parseCompaniesByUrls($urls)
    {
        $urls = $this->mapUrlsWithDonor($urls);
        $companies = [];
        foreach ($urls as $url) {
            $data = $this->parserClass->parseCompany($url['url'],$url['donor'])->wait();
            $companies[] = $data;
            dump($data);
        }
        $this->handleParsedData($companies);
    }
    public function parseArchivePagesByUrls($urls){
        $urls = $this->mapUrlsWithDonor($urls);

        foreach ($urls as $url) {
            /** @var Donor $donor */
            $donor     = $url['donor'];
            $companies = $this->parserClass->parseData($url['url'], $donor)->wait();
            LogService::log('info', 'спарсено ' . count($companies) . ' компаний', $url['url']);
            foreach ($companies as $key => $company) {
                $companies[$key] = array_merge(
                    $companies[$key],
                    $this->parserClass->parseCompany($company['donor_page'], $donor)->wait()
                );
                LogService::log('info', 'спарсено ' . count($companies[$key]['reviews']) . ' отзывов', $companies[$key]['donor_page']);
            }
            $this->handleParsedData($companies);
        }
    }
    public function mapUrlsWithDonor($urls) {
        $donorsQuery = Donor::select();
        $mappedUrls = [];
        foreach ($urls as $key => $url) {
            $host       = parse_url($url)['host'];
            $mappedUrls[] = ['url' => $url, 'host' => $host];
            $donorsQuery->orWhere('link', 'like', "%$host%");
        }
        $donors = $donorsQuery->get()->keyBy(function(Donor $donor){
            return parse_url($donor->link)['host'];
        });
        foreach ($mappedUrls as $key => $url) {
            $mappedUrls[$key]['donor'] = $donors[$url['host']];
        }
        return $mappedUrls;
    }
}
