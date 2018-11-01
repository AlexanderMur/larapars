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
 * @property-read \Illuminate\Database\Eloquent\Builder|\App\Models\Review $query
 */
class ReviewFilter extends ModelFilter
{

    public function favouriteCompany(){

        return $this->query->favouriteCompany();
    }
}