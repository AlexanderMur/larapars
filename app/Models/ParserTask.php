<?php

namespace App\Models;

use App\Jobs\ParsePages;
use App\Jobs\ResumeParsePages;
use App\ParserLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

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
 * @property integer $progress_max
 * @property integer $progress_now
 * @property integer $concurrent_links
 * @property integer $not_sent_links
 * @property integer $http_logs_count
 * @property-read \App\Models\ProgressBar $progress
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ParserTask whereParsedCompanies($arr)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HttpLog[] $http_logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ParsedCompany[] $parsed_companies2
 * @property-read mixed $companies
 * @property int $job_id
 * @property string $state
 * @property string|null $type
 * @property string|null $parser
 */
class ParserTask extends Model
{

    use HasRelationships;
    public $last_state_update;
    protected $fillable = ['state', 'progress_now', 'progress_max', 'type'];

    public function logs()
    {
        return $this->hasMany(ParserLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function http_logs()
    {
        return $this->hasMany(HttpLog::class);
    }

    public function scopeWhereParsedCompanies(Builder $query, $arr)
    {
        return $query->whereIn('url', $arr);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\App\Models\ParserTask $query
     */
    public function getGlobal($query)
    {
        $query->withStats();
    }

    public function createProgress($count)
    {
        return $this->progress()->create([
            'progress_max' => $count,
        ]);
    }

    public function scopeWithStats(Builder $query)
    {
        return $query->withCount([
            'logs as new_reviews_count'       => function (Builder $query) {
                $query->select(\DB::raw('sum(`details`)'))->where('type', 'new_reviews');
            },
            'logs as deleted_reviews_count'   => function (Builder $query) {
                $query->where('type', 'review_deleted');
            },
            'logs as restored_reviews_count'  => function (Builder $query) {
                $query->where('type', 'review_restored');
            },
            'logs as new_companies_count'     => function (Builder $query) {
                $query->where('type', 'company_created');
            },
            'logs as updated_companies_count' => function (Builder $query) {
                $query->where('type', 'company_updated');
            },
            'http_logs as progress_max'       => function (Builder $query) {
                $query->select(\DB::raw('COUNT(DISTINCT http_logs.donor_id)'));
            },
            'http_logs as concurrent_links'   => function (Builder $query) {
                $query->where('sent_at', '!=', null)->where('status', null);
            },
            'http_logs as not_sent_links'     => function (Builder $query) {
                $query->where('sent_at', '=', null);
            },
            'http_logs as http_logs_count' => function(Builder $query){
                $query->where('sent_at', '!=', null);
            },
        ]);
    }
    public function parsed_companies2()
    {
        return $this->belongsToMany(ParsedCompany::class, 'parser_logs');
    }


    public function getCompaniesAttribute()
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
         */
        $companies = $this->parsed_companies2->map->company->filter();
        if ($companies->count()) {
            return $companies->unique->id;
        }
        return $companies;
    }

    public function getFresh()
    {
        return static::whereKey($this->id)->withStats()->first();
    }

    /**
     * @param $type
     * @param $message
     * @param ParsedCompany|string|null $parsedCompany
     * @param null $details
     * @return ParserLog
     */
    public function log($type, $message, $parsedCompany, $details = null)
    {
        if ($parsedCompany instanceof ParsedCompany) {
            $url = $parsedCompany->donor_page;
        } else {
            $url = $parsedCompany;
        }
        return $this->logs()->create([
            'type'              => $type,
            'message'           => $message,
            'url'               => $url,
            'details'           => $details === null ? null : json_encode($details),
            'parsed_company_id' => $parsedCompany->id ?? null,
        ]);
    }

    /**
     * @param $url
     * @param string $channel
     * @return HttpLog|Model
     */
    public function createGet($url, $channel = '', $donor_id)
    {
        return $this->http_logs()->create([
            'url'      => $url,
            'channel'  => $channel,
            'donor_id' => $donor_id,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function getParsedCompaniesAttribute()
    {
        return $this->logs->map->parsed_company;
    }

    public function donors()
    {
        return $this->belongsToMany(Donor::class, 'http_logs')
            ->groupBy('donors.id');
    }

    public function setDone()
    {
        $this->update(['state' => 'Done', 'progress_now' => $this->progress_max]);
    }

    public function setPaused()
    {
        $this->update(['state' => 'Paused']);
    }

    public function setPausing()
    {
        if (!$this->state !== 'Done') {
            $this->update(['state' => 'Pausing']);

        }
    }

    public function setParsing()
    {
        $this->update(['state' => 'Parsing']);
    }

    public function setProgressNow($progress_now = 0)
    {
        $this->update(['progress_now' => $progress_now]);
    }

    public function refreshState()
    {
        $this->state = static::find($this->id)->state;
        return $this;
    }

    public function getState()
    {
        $cur_time = microtime(true);
        if ($cur_time - $this->last_state_update > 2) {
            $this->last_state_update = $cur_time;
            $this->refreshState();
        }
        return $this->state;
    }

    public function resume()
    {
        $task = $this->replicate();
        $task->save();
        dispatch(new ResumeParsePages($task->id, $this->id));
        return $task;
    }

    public function tickProgress()
    {
        $this->progress_now++;
        $this->save();
    }

    public static function dispatch($links, $type)
    {
        $task      = self::create(['type' => $type]);
        dispatch(new ParsePages($task->id, $links));
        return $task;
    }

    /**
     * @param $links
     * @param $type
     * @return $this|Model
     */
    public static function dispatch_now($links, $type)
    {
        $task      = self::create(['type' => $type]);
        dispatch_now(new ParsePages($task->id, $links));
        return $task;
    }
}
