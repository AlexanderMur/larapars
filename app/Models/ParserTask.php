<?php

namespace App\Models;

use App\ParserLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ParserTask
 *
 * @mixin \Eloquent
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Donor[] $donors
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ParserLog[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ParsedCompany[] $parsed_companies
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserTask withStats()
 * @property integer $new_reviews_count
 * @property integer $deleted_reviews_count
 * @property integer $restored_reviews_count
 * @property integer $new_companies_count
 * @property integer $updated_companies_count
 * @property-read \App\Models\ProgressBar $progress
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserTask whereParsedCompanies($arr)
 */
class ParserTask extends Model
{
    public function logs(){
        return $this->hasMany(ParserLog::class);
    }
    public function scopeWhereParsedCompanies(Builder $query,$arr){
        return $query->whereIn('url',$arr);
    }
    /**
     * @param \Illuminate\Database\Eloquent\Builder|\App\Models\ParserTask $query
     */
    public function getGlobal($query)
    {
        $query->withStats();
    }
    public function createProgress($count){
        return $this->progress()->create([
            'progress_max' => $count,
        ]);
    }

    public function scopeWithStats(Builder $query){
        return $query->withCount([
            'logs as new_reviews_count' => function(Builder $query){
                $query->select(\DB::raw('sum(`details`)'))->where('type','new_reviews');
            },
            'logs as deleted_reviews_count' => function(Builder $query){
                $query->where('type','review_deleted');
            },
            'logs as restored_reviews_count' => function(Builder $query){
                $query->where('type','review_restored');
            },
            'logs as new_companies_count' => function(Builder $query){
                $query->where('type','company_created');
            },
            'logs as updated_companies_count' => function(Builder $query){
                $query->where('type','company_updated');
            },
        ]);
    }

    /**
     * @param $type
     * @param $message
     * @param ParsedCompany|null $parsedCompany
     * @param null $details
     * @return ParserLog
     */
    public function log($type, $message, $parsedCompany, $details = null)
    {
        if($parsedCompany instanceof ParsedCompany){
            $url = $parsedCompany->donor_page;
        } else {
            $url = $parsedCompany;
        }
        return $this->logs()->create([
            'type'  => $type,
            'message' => $message,
            'url'     => $url,
            'details' => $details === null ? null : json_encode($details),
            'parsed_company_id' => $parsedCompany->id ?? null,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function donors(){
        return $this->belongsToMany(Donor::class)->using(DonorTask::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parsed_companies()
    {
        return $this->belongsToMany(ParsedCompany::class)->using(ParsedCompanyTask::class);
    }

    public function progress(){
        return $this->hasOne(ProgressBar::class);
    }
}
