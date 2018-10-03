<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Parser
 *
 * @property int $id
 * @property string|null $link
 * @property string|null $loop_item
 * @property string|null $loop_title
 * @property string|null $loop_address
 * @property string|null $loop_link
 * @property string|null $single_address
 * @property string|null $single_site
 * @property string|null $replace_search
 * @property string|null $replace_to
 * @property string|null $reviews_ignore_text
 * @property string|null $reviews_all
 * @property string|null $reviews_title
 * @property string|null $reviews_text
 * @property string|null $reviews_rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereLoopAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereLoopItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereLoopLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereLoopTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReplaceSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReplaceTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReviewsAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReviewsIgnoreText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReviewsRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReviewsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReviewsTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereSingleAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereSingleSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $donor_link
 * @property string|null $donor_title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereDonorLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereDonorTitle($value)
 * @property string|null $single_phone
 * @property string|null $reviews_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereReviewsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereSinglePhone($value)
 * @property int|null $donor_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereDonorId($value)
 * @property string|null $single_tel
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Parser whereSingleTel($value)
 * @property string|null $reviews_id
 */
class Parser extends Model
{
    //
}
