<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Parser
 *
 * @property int $id
 * @property int|null $donor_id
 * @property string|null $loop_item
 * @property string|null $loop_title
 * @property string|null $loop_address
 * @property string|null $loop_link
 * @property string|null $single_address
 * @property string|null $single_site
 * @property string|null $single_phone
 * @property string|null $replace_search
 * @property string|null $replace_to
 * @property string|null $reviews_ignore_text
 * @property string|null $reviews_all
 * @property string|null $reviews_title
 * @property string|null $reviews_text
 * @property string|null $reviews_rating
 * @property string|null $reviews_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereDonorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereLoopTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReplaceSearch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReplaceTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsAll($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsIgnoreText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereReviewsTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSingleAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSinglePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSingleSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Donor|null $donor
 * @property string|null $single_tel
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Parser whereSingleTel($value)
 */
class Parser extends Model
{
    function donor(){
        return $this->belongsTo(Donor::class);
    }
}
