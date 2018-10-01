<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
 */
class ParsedCompany extends Model
{
    function donor(){
        return $this->belongsTo(Donor::class);
    }
    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
