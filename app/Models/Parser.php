<?php

namespace App\Models;

use App\ParserLog;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Parser
 *
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $id
 * @property int|null $donor_id
 * @property string|null $loop_address
 * @property string|null $loop_item
 * @property string|null $loop_link
 * @property string|null $loop_title
 * @property string|null $replace_search
 * @property string|null $replace_to
 * @property string|null $reviews_all
 * @property string|null $reviews_ignore_text
 * @property string|null $reviews_name
 * @property string|null $reviews_rating
 * @property string|null $reviews_text
 * @property string|null $reviews_title
 * @property string|null $single_address
 * @property string|null $single_phone
 * @property string|null $single_site
 * @property string|null $single_tel
 * @property-read \App\Models\Donor|null $donor
 * @see \CreateParsersTable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ParserLog[] $logs
 */
class Parser extends Model
{
    function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    function logs()
    {
        return $this->hasMany(ParserLog::class);
    }

    function log($message, $url, $status = 'info')
    {
        $this->logs()->create([
            'url' => $url,
            'message' => $message,
            'status' => $status,
        ]);
    }
}
