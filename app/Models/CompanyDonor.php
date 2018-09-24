<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 24.09.2018
 * Time: 14:25
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\CompanyDonor
 *
 * @property int $id
 * @property int $company_id
 * @property int $donor_id
 * @property string|null $site
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 * @see \CreateCompanyDonorTable
 */
class CompanyDonor extends Pivot
{

    public $hello = 'hello';
}