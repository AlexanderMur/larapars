<?php

namespace App\Models;

use App\ParserLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Company
 *
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $donor_id
 * @property int $id
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $single_page_link
 * @property string|null $site
 * @property string|null $title
 * @property-read \App\Models\Donor $donor
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Donor[] $donors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @see \CreateCompaniesTable
 * @see CompanyController
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ParsedCompany[] $parsed_companies
 * @property string|null $city
 * @property int reviews_count
 * @property int good_reviews_count
 * @property int bad_reviews_count
 * @property int unrated_reviews_count
 * @property int deleted_reviews_count
 * @property int trashed_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ParserLog[] $logs
 * @property int $favourite
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ParserTask[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company withStats()
 */
class Company extends Model
{
    protected $fillable = [
        'title',
        'address',
        'single_page_link',
        'site',
        'phone',
        'city',
    ];

    function reviews()
    {
        return $this->hasManyThrough(Review::class, ParsedCompany::class);
    }


    public function parsed_companies()
    {
        return $this->hasMany(ParsedCompany::class);
    }

    public function logs()
    {
        return $this->hasManyThrough(
            ParserLog::class,
            ParsedCompany::class,
            'company_id', // Foreign key on parsed_company table...
            'url', // Foreign key on parser_logs table...
            'id', // Local key on this table...
            'donor_page' // Local key on parsed_company table...
        )->latest('id');
    }

    public function getTasks()
    {
        $donor_pages = $this->parsed_companies->pluck('donor_page');

        return ParserTask
            ::whereHas('logs', function (Builder $query) use ($donor_pages) {
                $query->whereIn('url', $donor_pages)->limit(5);
            })
            ->with(['logs' => function (HasMany $query) use ($donor_pages) {
                $query->whereIn('url', $donor_pages)
                    ->orWhere('url', null)->limit(15)->latest('id');
            }])
            ->get();
    }
    public function getRelatedLogs()
    {

        return $this->getTasks()->flatMap->logs;
    }

    public function scopeWithStats(Builder $query)
    {
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
            'reviews as deleted_reviews_count' => function ($query) {
                $query->withTrashed()->where('deleted_at', '!=', null)->where('trashed_at', '=', null);
            },
            'reviews as trashed_reviews_count' => function ($query) {
                $query->withTrashed()->where('trashed_at', '!=', null);
            },
        ]);
    }
}
