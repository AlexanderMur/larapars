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
use Illuminate\Database\Query\JoinClause;
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
                if ($changed_attributes = $this->detectChanges2($parsed_company, $new_company)) {
                    //Получить последние изменения...
                    $actual_attrs = CompanyHistory
                        ::select('company_histories.*')
                        ->leftJoin('company_histories as m2', function (JoinClause $join) {
                            $join
                                ->on('company_histories.field', '=', 'm2.field')
                                ->on('company_histories.id', '<', 'm2.id')
                                ->on('company_histories.parsed_company_id', '=', 'm2.parsed_company_id');
                        })
                        ->where('company_histories.parsed_company_id', $parsed_company->id)
                        ->where('m2.id', null)
                        ->get()->keyBy('field')->toArray();

                    foreach ($changed_attributes as $changed_attribute) {
                        if(!isset($actual_attrs[$changed_attribute]) && $parsed_company->$changed_attribute == $new_company[$changed_attribute]){
                            continue;
                        }
                        if (!isset($actual_attrs[$changed_attribute])
                            || $actual_attrs[$changed_attribute]['new_value'] != $new_company[$changed_attribute]) {
                            CompanyHistory::create([
                                'field'             => $changed_attribute,
                                'old_value'         => $actual_attrs[$changed_attribute]['new_value'] ?? $parsed_company->$changed_attribute,
                                'new_value'         => $new_company[$changed_attribute],
                                'parsed_company_id' => $parsed_company->id,
                            ]);
                        }
                    }
                };


            }
            $this->saveReviews($parsed_company, $new_company);
        }
    }

    public function detectChanges(ParsedCompany $parsedCompany, $newCompany)
    {
        $changed_attributes = [];
        foreach ($parsedCompany->toArray() as $key => $attribute) {
            if (isset($newCompany[$key])) {
                if ($newCompany[$key] !== $attribute) {
                    $changed_attributes[] = $key;
                }
            }
        }
        return $changed_attributes;
    }

    public function detectChanges2(ParsedCompany $parsedCompany, $newCompany)
    {
        $changed_attributes = [];
        foreach ($parsedCompany->toArray() as $key => $attribute) {
            if (isset($newCompany[$key])) {
                $changed_attributes[] = $key;
            }
        }
        return $changed_attributes;
    }

}
