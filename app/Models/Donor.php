<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Donor
 *
 * @property int $id
 * @property string $link
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Donor whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
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
