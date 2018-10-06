<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 07.10.2018
 * Time: 3:03
 */

namespace App\Services;


use App\Models\Review;

class ReviewService
{
    public function likeReview(Review $review){
        $review->good = true;
        return $review->save();
    }
    public function dislikeReview(Review $review){

        $review->good = false;
        return $review->save();
    }
}