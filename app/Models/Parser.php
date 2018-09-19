<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Parser
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
 * @mixin \Eloquent
 * @property string|null $donor_link
 * @property string|null $donor_title
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereDonorLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereDonorTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReplaceSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReplaceTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsIgnoreText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSingleAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSingleSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereUpdatedAt($value)
 * @property string|null $single_phone
 * @property string|null $reviews_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSinglePhone($value)
 */
class Parser extends Model
{
    //
}
