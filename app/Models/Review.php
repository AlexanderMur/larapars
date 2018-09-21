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
 * @property int $donor_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereDonorId($value)
 * @property string|null $name
 * @property-read \App\Models\Donor $donor
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereName($value)
 * @property-read \App\Models\Company $company
 */
class Review extends Model
{
    protected $fillable = [
        'text',
        'title',
        'name',
    ];

    function donor(){
        return $this->belongsTo(Donor::class);
    }
    function company(){
        return $this->belongsTo(Company::class);
    }
}
