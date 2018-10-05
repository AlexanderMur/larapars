<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
    public function setPhonesAttribute($phones){
        $this->attributes['phone'] = implode(', ',$phones);
    }
    /**
     * @param Collection|Review[] $reviews
     * @return bool
     */
    public function saveReviews($reviews){
        foreach ($reviews as $review) {
            $review->parsed_company_id = $this->id;
            $review->created_at = Carbon::now();
            $review->updated_at = $review->created_at;
        }
        return $this->reviews()->insert($reviews instanceof Collection ? $reviews->toArray() : $reviews);
    }
}
