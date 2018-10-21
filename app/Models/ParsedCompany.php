<?php

namespace App\Models;

use App\CompanyHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

/**
 * Class ParsedCompany
 *
 * @package App\Models
 * @see \CreateParsedCompaniesTable
 * @property int $id
 * @property string|null $phone
 * @property string|null $site
 * @property string|null $title
 * @property string|null $address
 * @property string|null $donor_page
 * @property string|null $donor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Donor|null $donor
 * @mixin \Eloquent
 * @property int|null $company_id
 * @property-read \App\Models\Company|null $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @property string|null $city
 * @property-write mixed $phones
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CompanyHistory[] $history
 * @property int good_reviews_count
 * @property int bad_reviews_count
 * @property int unrated_reviews_count
 * @property int deleted_reviews_count
 * @property int trashed_reviews_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParsedCompany withStats()
 */
class ParsedCompany extends Model
{
    protected $fillable = [
        'donor_page',
        'donor_id',
        'phone',
        'site',
        'title',
        'address',
        'phones',
        'city',
    ];
    protected $appends = [
        'phones',
    ];

    function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function setPhonesAttribute($phones)
    {
        $this->attributes['phone'] = implode(', ', $phones);
    }

    public function history()
    {
        return $this->hasMany(CompanyHistory::class);
    }

    public function lastHistoryRecord()
    {
        return $this->history()
            ->select('company_histories.*')
            ->leftJoin('company_histories as m2', function (JoinClause $join) {
                $join
                    ->on('company_histories.field', '=', 'm2.field')
                    ->on('company_histories.id', '<', 'm2.id')
                    ->on('company_histories.parsed_company_id', '=', 'm2.parsed_company_id');
            })
            ->where('m2.id', null)
            ->get()->keyBy('field')->toArray();
    }
    public function scopeWithStats(Builder $query){
        $query->withCount([
            'reviews',
            'reviews as good_reviews_count'    => function (Builder $query) {
                $query->where('good', '=', true);
            },
            'reviews as bad_reviews_count'     => function (Builder $query) {
                $query->where('good', '!=', false);
            },
            'reviews as unrated_reviews_count' => function (Builder $query) {
                $query->where('good', '=', null);
            },
            'reviews as deleted_reviews_count' => function (Builder $query) {
                $query->withTrashed()->where('deleted_at', '!=', null)->where('trashed_at', '=', null);
            },
            'reviews as trashed_reviews_count' => function (Builder $query) {
                $query->withTrashed()->where('trashed_at', '!=', null);
            },
        ]);
    }
    public function getActualAttrs(){

        return array_merge(
            $this->getAttributes(),
            array_pluck($this->lastHistoryRecord(),'new_value','field')
        );
    }
    public function getPhonesAttribute()
    {

        return $this->attributes['phone'] ? explode(', ', $this->attributes['phone']) : [];
    }
    /**
     * @param Collection|Review[] $reviews
     * @return bool
     */
    public function saveReviews($reviews)
    {
        foreach ($reviews as $review) {
            $review->parsed_company_id = $this->id;
            $review->created_at        = Carbon::now();
            $review->updated_at        = $review->created_at;
        }
        return $this->reviews()->insert($reviews instanceof Collection ? $reviews->toArray() : $reviews);
    }
}
