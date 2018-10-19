<?php

namespace App\Models;

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
 */
class Donor extends Model
{





    protected $fillable = [
        'link',
        'title',
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
        return $this->hasMany(Company::class);
    }
    function reviews(){
        return $this->hasMany(Review::class);
    }

    function scopeMassParsing(Builder $query){
        $query->where('mass_parsing',true);
    }
}
