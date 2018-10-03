<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 03.10.2018
 * Time: 19:51
 */

namespace App\Services;


use App\Models\ParsedCompany;
use App\Models\Review;
use Illuminate\Support\Arr;


class ParserService
{
    /**
     * @param ParsedCompany $parsed_company
     * @param array $new_company
     */
    function saveReviews($parsed_company,$new_company)
    {
        $reviews = Review::withTrashed()->where('donor_link',$new_company['single_page_link'])->get();

        $new_review_ids = Arr::pluck($new_company['reviews'],'donor_comment_id');


        foreach ($reviews as $review) {

            $in_array = in_array($review->donor_comment_id,$new_review_ids);

            if( !$in_array && $review->deleted_at === null){
                $review->delete();
            }
            if($in_array && $review->deleted_at !== null){
                $review->restore();
            }
        }



        $not_existing_reviews = Arr::where($new_company['reviews'], function($new_review) use ($reviews) {
            return !$reviews->contains('donor_comment_id',$new_review['donor_comment_id']);
        });

        $new_reviews = collect();


        foreach ($not_existing_reviews as $not_existing_review) {
            $new_reviews[] = new Review($not_existing_review);
        }


        $parsed_company->saveReviews($new_reviews);
    }

    public function handleParsedData($new_companies,$donor_id)
    {
        foreach ($new_companies as $new_company) {
            $parsed_company = ParsedCompany::updateOrCreate(['donor_page'=>$new_company['single_page_link']],$new_company);
            $this->saveReviews($parsed_company,$new_company,$donor_id);
        }
    }
}
