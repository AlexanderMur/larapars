<?php

namespace App\Models;

use App\CompanyHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
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

    /**
     * @param $new_company
     * @param Donor $donor
     * @param ParserTask $parserTask
     * @return ParsedCompany|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public static function handleParsedCompany($new_company, Donor $donor, ParserTask $parserTask)
    {
        if (!$new_company) {
            return null;
        }
        if (!isset($new_company['title']) && $new_company['title'] === null) {
            throw new \Exception('company must have title');
        }
        $new_company    = static::filterNewCompany($new_company);
        $parsed_company = ParsedCompany::firstOrCreate(['donor_page' => $new_company['donor_page']], $new_company);
        if (!$parsed_company->wasRecentlyCreated) {
            info('not new!!' . $parsed_company->donor_page);
            foreach ($parsed_company->getActualAttrs() as $key => $attribute) {
                if (!isset($new_company[$key])) {
                    continue;
                }
                if ($attribute != $new_company[$key]) {
                    CompanyHistory::create([
                        'field'             => $key,
                        'old_value'         => $attribute,
                        'new_value'         => $new_company[$key],
                        'parsed_company_id' => $parsed_company->id,
                    ]);
                    $translate_field = __('company.' . $key);
                    $parserTask->log(
                        'company_updated',
                        "$parsed_company->title Поменяла поле \"$translate_field\" – было \"$attribute\" стало \"$new_company[$key]\"",
                        $parsed_company
                    );
                }
            }
        } else {
            $parserTask->log('company_created', 'Новая компания: ' . $parsed_company->title, $parsed_company);
        }

        static::handleParsedReviews($parsed_company, $new_company, $parserTask);
        return $parsed_company;
    }

    /**
     * @param ParsedCompany $parsed_company
     * @param array $new_company
     * @param ParserTask $parserTask
     */
    public static function handleParsedReviews($parsed_company, $new_company, ParserTask $parserTask)
    {
        $reviews = Review::withTrashed()->where('donor_link', $new_company['donor_page'])->get();

        $new_review_ids = Arr::pluck($new_company['reviews'], 'donor_comment_id');


        foreach ($reviews as $review) {
            $in_array = in_array($review->donor_comment_id, $new_review_ids);
            if (!$in_array && $review->deleted_at === null) {
                $review->delete();
                $parserTask->log('review_deleted', 'Отзыв удален', $parsed_company);
            }
            if ($in_array && $review->deleted_at !== null) {
                $review->restore();
                $parserTask->log('review_restored', 'Отзыв возвращен', $parsed_company);
            }
        }

        $new_reviews = collect();
        foreach ($new_company['reviews'] as $new_review) {
            if (!$reviews->contains('donor_comment_id', $new_review['donor_comment_id'])) {
                $new_reviews[] = new Review($new_review);
            }
        }

        if (count($new_reviews))
            $parserTask->log('new_reviews',
                'Добавлено новых отзывов (' . count($new_reviews) . ')', $parsed_company, count($new_reviews));


        foreach ($reviews as $review) {
            static::filterReview($review);
        }
        //insert many reviews
        $parsed_company->saveReviews($new_reviews);
    }

    public static function filterReview(Review $review)
    {
        $review->title = str_replace('| Ответить', '', $review->title);
    }

    public static function filterNewCompany($new_company)
    {

        $properties = [
            'phone',
            'site',
            'address',
            'city',
        ];


        foreach ($properties as $property) {
            if (isset($new_company[$property])) {
                if (strlen($new_company[$property]) > 255) {
                    $new_company[$property] = '';
                    continue;
                }

                $new_company[$property] = trim($new_company[$property]);
            }
        }
        return $new_company;
    }


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
            'reviews as deleted_reviews_count' => function (Builder $query) {
                $query->withTrashed()->where('deleted_at', '!=', null)->where('trashed_at', '=', null);
            },
            'reviews as trashed_reviews_count' => function (Builder $query) {
                $query->withTrashed()->where('trashed_at', '!=', null);
            },
        ]);
    }

    public function getActualAttrs()
    {

        return array_merge(
            $this->getAttributes(),
            array_pluck($this->lastHistoryRecord(), 'new_value', 'field')
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
