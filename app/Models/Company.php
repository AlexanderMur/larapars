<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Company
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $phone
 * @property string|null $site
 * @property string|null $title
 * @property string|null $address
 * @property int $donor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereDonorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUpdatedAt($value)
 */
class Company extends Model
{
    //
}