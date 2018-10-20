<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 20.10.2018
 * Time: 17:53
 */

namespace App\ModelFilters;


use EloquentFilter\ModelFilter;

/**
 * Class ReviewFilter
 * @package App\ModelFilters
 * @mixin \Illuminate\Database\Eloquent\Builder|\App\Models\Review
 */
class ReviewFilter extends ModelFilter
{
    public function favouriteCompany(){

        return $this->whereHas('parsedCompany.company',function($query){
            $query->where('favourite',true);
        });
    }
}