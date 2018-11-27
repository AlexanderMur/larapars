<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HttpLog
 *
 * @property int $id
 * @property string $url
 * @property int|null $status
 * @property int $parser_task_id
 * @property string|null $channel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 * @property string|null $message
 * @property string|null $sent_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HttpLog sent()
 * @property-read \App\Models\Donor $donor
 * @property int $donor_id
 * @property string|null $params
 */
class HttpLog extends Model
{

    protected $fillable = [

        'url',
        'status',
        'task_id',
        'donor_id',
        'channel',
        'message',
        'sent_at',
    ];
    public function updateStatus($status,$message){
        $this->update(['status' => $status,'message'=>str_limit($message,190 - 3)]);
    }
    public function donor(){
        return $this->belongsTo(Donor::class);
    }
}
