<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ParserLog
 *
 * @property int $id
 * @property int $parser_id
 * @property string $url
 * @property string $message
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class ParserLog extends Model
{

    const UPDATED_AT = null;
    protected $fillable = [
        'url',
        'status',
        'message',
    ];
}
