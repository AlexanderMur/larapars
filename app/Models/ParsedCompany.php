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
 */
class ParsedCompany extends Model
{
    function donor(){
        return $this->belongsTo(Donor::class);
    }
}
