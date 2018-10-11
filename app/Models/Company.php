<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
 *
 * @property int reviews_count
 * @property int good_reviews_count
 * @property int bad_reviews_count
 * @property int unrated_reviews_count
 * @property int deleted_reviews_count
 * @property int trashed_reviews_count
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
        return $this->hasMany(Review::class);
    }


    public function parsed_companies()
    {
        return $this->hasMany(ParsedCompany::class);
    }
}
