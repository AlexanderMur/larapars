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
 * @property string|null $details
 * @property int|null $donor_id
 * @property int|null $parsed_company_id
 * @property string|null $type
 * @property int|null $parser_task_id
 */
class ParserLog extends Model
{

    const UPDATED_AT = null;
    protected $fillable = [
        'url',
        'type',
        'message',
        'details',
        'parsed_company_id',
    ];
    public function updateStatus($status,$message){
        return $this->update([
            'type' => $status,
            'message' => $message,
        ]);
    }
}
