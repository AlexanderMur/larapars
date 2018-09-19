<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Review
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $company_id
 * @property string|null $title
 * @property string|null $text
 * @property string|null $rating
 * @property string|null $donor_created_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereDonorCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereUpdatedAt($value)
 */
class Review extends Model
{
    //
}
