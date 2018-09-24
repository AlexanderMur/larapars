<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\DonorReview
 *
 * @property int $id
 * @property int $review_id
 * @property int $donor_id
 * @property string $site
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class DonorReview extends Pivot
{
    //
}
