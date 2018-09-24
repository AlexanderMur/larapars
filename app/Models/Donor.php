<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Donor
 *
 * @property CompanyDonor|DonorReview $pivot
 * @property int $id
 * @property string $link
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 * @mixin \Eloquent
 * @see \CreateDonorsTable
 */
class Donor extends Model
{





    protected $fillable = ['link','title'];
    function companies(){
        return $this->hasMany(Company::class);
    }
    function reviews(){
        return $this->hasMany(Review::class);
    }
}
