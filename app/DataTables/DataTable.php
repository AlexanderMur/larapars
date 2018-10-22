<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 22.10.2018
 * Time: 14:22
 */

namespace App\DataTables;


use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class DataTable
{
    /**
     * @var \DataTables
     */
    public $dataTables;
    /**
     * @var Builder
     */
    public $builder;

    public function __construct(DataTables $dataTables, Builder $builder)
    {

        $this->dataTables = $dataTables;
        $this->builder = $builder;

        $this->builder->parameters([
            "lengthMenu" => [[20, 50, 100, 200, 500], [20, 50, 100, 200, 500],],
            'language'   => __('datatables'),
        ]);
    }
}