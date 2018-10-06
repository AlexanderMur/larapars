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
    function saveReviews($parsed_company, $new_company)
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
                $actual_attrs = $parsed_company->lastHistoryRecord();
                foreach ($parsed_company->getAttributes() as $key => $attribute) {
                    if(!isset($new_company[$key])){
                        continue;
                    }
                    //если в истории ничего нет и первоначальные данные такие же
                    if (!isset($actual_attrs[$key]) && $attribute == $new_company[$key]) {
                        continue;
                    }
                    //если в истории ничего нет или записи в истории не совпадают
                    if (!isset($actual_attrs[$key]) || $actual_attrs[$key]['new_value'] != $new_company[$key]) {
                        CompanyHistory::create([
                            'field'             => $key,
                            'old_value'         => $actual_attrs[$key]['new_value'] ?? $attribute,
                            'new_value'         => $new_company[$key],
                            'parsed_company_id' => $parsed_company->id,
                        ]);
                    }

                }


            }
            $this->saveReviews($parsed_company, $new_company);
        }
    }
}
