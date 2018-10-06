<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CompanyHistory
 *
 * @property int $id
 * @property string $field
 * @property string $value
 * @property \Illuminate\Support\Carbon $created_at
 * @mixin \Eloquent
 * @property string $old_value
 * @property string $new_value
 * @property string $parsed_company_id
 */
class CompanyHistory extends Model
{
    protected $fillable = [
        'field',
        'old_value',
        'new_value',
        'parsed_company_id'
    ];
    const UPDATED_AT = null;
}
