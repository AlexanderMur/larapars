<?php

namespace App\Models;

use App\ModelFilters\ReviewFilter;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property int|null $parsed_company_id
 * @property string|null $title
 * @property string|null $name
 * @property string|null $text
 * @property bool|null $good
 * @property int|null $group_id
 * @mixin \Eloquent
 * @property string|null $donor_link
 * @property int|null $donor_id
 * @property string|null $donor_comment_id
 * @property string|null $donor_created_at
 * @property \Illuminate\Support\Carbon|null $trashed_at
 * @property \Illuminate\Support\Carbon|null $rated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Donor|null $donor
 * @property-read \App\Models\Company|null $company
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\ParsedCompany|null $parsed_company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review filter($input = array(), $filter = null)
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Review onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review paginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review simplePaginateFilter($perPage = null, $columns = array(), $pageName = 'page', $page = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review unrated($unrated = true)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereBeginsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereEndsWith($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review whereLike($column, $value, $boolean = 'and')
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Review withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Review withoutTrashed()
 * @see ReviewFilter
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Review favouriteCompany()
 */
class Review extends Model
{
    use Filterable;
    use SoftDeletes;

    protected $fillable = [
        'text',
        'title',
        'name',
        'good',
        'deleted_at',
        'trashed_at',
        'donor_link',
        'donor_id',
        'donor_comment_id',
    ];
    protected $dates = [
        'deleted_at',
        'trashed_at',
        'rated_at',
    ];
    protected $casts = [
        'good' => 'boolean',
    ];

    /**
     * @return \App\Models\Company|null
     */
    public function getCompanyAttribute(){
        return $this->parsed_company->company;
    }
    public function parsed_company()
    {
        return $this->belongsTo(ParsedCompany::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    function scopeUnrated($builder, $unrated = true)
    {
        if ($unrated) {
            $builder->where('good', '=', null);
        } else {
            $builder->where('good', '!=', null);
        }
    }

    public function setGoodAttribute($value)
    {
        if (!isset($this->attributes['good'])) {
            $this->attributes['good'] = null;
        }

        if ($value !== null && $this->attributes['good'] === null) {
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
    public function scopeFavouriteCompany($query){

        return $query->whereHas('parsed_company.company',function($query){
            $query->where('favourite',true);
        });
    }

    public function dislike()
    {
        $this->good = false;

        return $this;
    }

    public function like()
    {
        $this->good = true;

        return $this->save();
    }
}
