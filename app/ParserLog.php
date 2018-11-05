<?php

namespace App;

use App\Models\ParsedCompany;
use App\Models\ParserTask;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

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
 * @property-read mixed $donor
 * @property-read \App\Models\ParsedCompany|null $parsed_company
 * @property-read \App\Models\ParserTask $task
 */
class ParserLog extends Model
{

    use HasRelationships;
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
    public function parsed_company(){
        return $this->belongsTo(ParsedCompany::class);
    }
    public function donor(){
        return $this->parsed_company->donor ?? null;
    }
    public function task(){
        return $this->belongsTo(ParserTask::class);
    }
}
