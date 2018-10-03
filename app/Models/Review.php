<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Review
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Review onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Review withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Review withoutTrashed()
 * @method static bool|null forceDelete()
 * @method static bool|null restore()
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $company_id
 * @property int $donor_id
 * @property int $id
 * @property string|null $deleted_at
 * @property string|null $donor_created_at
 * @property string|null $good
 * @property string|null $name
 * @property string|null $rating
 * @property string|null $text
 * @property string|null $title
 * @property-read \App\Models\Company $company
 * @property-read \App\Models\Donor $donor
 * @see \CreateReviewsTable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Donor[] $donors
 * @property string|null $trashed_at
 * @property int|null $group_id
 * @property-read \App\Models\Group|null $group
 * @property string|null $donor_link
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review unrated($unrated = true)
 * @property \Illuminate\Support\Carbon|null $rated_at
 * @property int|null $parsed_company_id
 * @property string|null $donor_comment_id
 */
class Review extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'text',
        'title',
        'name',
        'good',
        'deleted_at',
        'donor_link',
        'donor_id',
        'donor_comment_id',
    ];
    protected $dates = [
        'deleted_at',
        'rated_at',
    ];
    protected $casts = [
        'good' => 'boolean'
    ];

    function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
    public function group(){
        return $this->belongsTo(Group::class);
    }

    function scopeUnrated($builder, $unrated = true){
        if($unrated){
            $builder->where('good','=',null);
        } else {
            $builder->where('good','!=',null);
        }
    }
    public function setGoodAttribute($value){
        if(!isset($this->attributes['good'])){
            $this->attributes['good'] = null;
        }

        if($value !== null && $this->attributes['good'] === null){
            $this->attributes['rated_at'] = Carbon::now();
        }
        $this->attributes['good'] = $value;
    }
    /**
     * @throws \Exception
     */
    function trash()
    {
        $this->delete();
        $this->trashed_at = Carbon::now();
        $this->save();
    }
}
