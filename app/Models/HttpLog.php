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
 */
class HttpLog extends Model
{

    protected $fillable = [

        'url',
        'status',
        'task_id',
        'channel',
        'message',
    ];
    public function updateStatus($status,$message){
        $this->update(['status' => $status,'message'=>$message]);
    }
}
