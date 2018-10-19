<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\ParsedCompanyTask
 *
 * @mixin \Eloquent
 */
class ParsedCompanyTask extends Pivot
{
    protected $fillable = [
        'new_parsed_companies_count',
        'updated_companies_count',
        'new_reviews_count',
        'deleted_reviews_count',
        'restored_reviews_count',
    ];
}
