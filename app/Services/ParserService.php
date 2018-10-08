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

    public function parseCompanyByUrl($url, Donor $donor)
    {

        return $this->parserClass->parseCompany($url, $donor)
            ->then(function ($data) {
                dump($data);
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
            $this->parseCompaniesByUrls(
                $this->parserClass->parseData($url['donor_page'], $url['donor'])->wait(),
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
}
