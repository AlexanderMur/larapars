<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProgressBar
 *
 * @package App\Models
 * @see \CreateProgressBarsTable
 * @mixin \Eloquent
 * @property int $id
 * @property int $parser_task_id
 * @property int $progress
 * @property int $progress_max
 */
class ProgressBar extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $fillable = [
        'progress_max',
    ];
}
