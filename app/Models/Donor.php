<?php

namespace App\Models;

use App\ParserLog;
use App\Parsers\SelectorParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Donor
 *
 * @property int $id
 * @property string $link
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @mixin \Eloquent
 * @see \CreateDonorsTable
 * @property string|null $loop_item
 * @property string|null $loop_title
 * @property string|null $loop_address
 * @property string|null $loop_link
 * @property string|null $single_site
 * @property string|null $single_address
 * @property string|null $single_tel
 * @property string|null $replace_search
 * @property string|null $replace_to
 * @property string|null $reviews_ignore_text
 * @property string|null $reviews_all
 * @property string|null $reviews_title
 * @property string|null $reviews_text
 * @property string|null $reviews_rating
 * @property string|null $reviews_name
 * @property string|null $reviews_id
 * @property string|null $single_title
 * @property string|null $single_city
 * @property string|null $reviews_pagination
 * @property string|null $archive_pagination
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor massParsing()
 * @property int $mass_parsing
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ParsedCompany[] $parsed_companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ParserLog[] $logs
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor withTaskStats($task_id)
 * @property int $decode_url
 * @property string|null $parser
 * @property string|null $single_address_find_by_regex
 * @property string|null $s_address_regex
 * @property string|null $s_city_regex
 * @property string|null $loop_links
 */
class Donor extends Model
{
    protected $parserObj;

    /**
     * @param $urls
     * @return array
     */
    public static function mapUrls($urls)
    {
        if ($urls) {
            $donorsQuery = static::select();
            $mappedUrls  = [];
            foreach ($urls as $key => $url) {
                $host         = parse_url($url)['host'];
                $mappedUrls[] = ['donor_page' => $url, 'host' => $host];
                $donorsQuery->orWhere('link', 'like', "%$host%");
            }
            $donors = $donorsQuery->get()->keyBy(function (Donor $donor) {
                return parse_url($donor->link)['host'];
            });
            foreach ($mappedUrls as $key => $url) {
                $mappedUrls[$key]['donor'] = $donors[$url['host']];
            }
            return $mappedUrls;
        } else {
            return [];
        }
    }
    public function getParser($client, $task, $proxies, $tries){
        if(!$this->parserObj){
            if(!$this->parser){
                return $this->parserObj = new SelectorParser($this,$client, $task,$proxies,$tries);
            }
            return $this->parserObj = new $this->parser($this,$client, $task, $proxies, $tries);
        }
        return $this->parserObj;
    }
    public function scopeWithTaskStats($query,$task_id){
        return $query->withCount([
            'logs as new_reviews_count' => function(Builder $query) use ($task_id) {
                $query->select(\DB::raw('sum(`details`)'))->where('type','new_reviews')->where('parser_task_id',$task_id);
            },
            'logs as deleted_reviews_count' => function(Builder $query) use ($task_id) {
                $query->where('type','review_deleted')->where('parser_task_id',$task_id);
            },
            'logs as restored_reviews_count' => function(Builder $query) use ($task_id) {
                $query->where('type','review_restored')->where('parser_task_id',$task_id);
            },
            'logs as new_companies_count' => function(Builder $query) use ($task_id) {
                $query->where('type','company_created')->where('parser_task_id',$task_id);
            },
            'logs as updated_companies_count' => function(Builder $query) use ($task_id) {
                $query->where('type','company_updated')->where('parser_task_id',$task_id);
            },
        ]);
    }

    protected $fillable = [
        'link',
        'title',
        'mass_parsing',
        'loop_links',
        'loop_address',
        'loop_item',
        'loop_link',
        'loop_title',
        'replace_search',
        'replace_to',
        'reviews_all',
        'reviews_ignore_text',
        'reviews_rating',
        'reviews_text',
        'reviews_title',
        'reviews_name',
        'reviews_id',
        'single_site',
        'single_address',
        'single_tel',
        'single_title',
        'single_city',
        'archive_pagination',
    ];

    function companies(){
        return $this->BelongsToMany(Company::class,'parsed_companies')->groupBy('id');
    }
    function reviews(){
        return $this->hasManyThrough(Review::class,ParsedCompany::class);
    }
    function parsed_companies(){
        return $this->hasMany(ParsedCompany::class);
    }
    function logs(){
        return $this->hasManyThrough(ParserLog::class,ParsedCompany::class);
    }
    function scopeMassParsing(Builder $query){
        $query->where('mass_parsing',true);
    }
}
