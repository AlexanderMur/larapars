<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 22.10.2018
 * Time: 15:37
 */

namespace App\DataTables;


use App\Models\Donor;
use Illuminate\Support\HtmlString;

class DonorDataTable extends DataTable
{
    public function query()
    {
        return Donor
            ::select([
                'donors.*',
            ])
            ->withCount([
                'reviews',
                'companies',
            ])
            ->groupBy('id');
    }

    public function ajax()
    {
        return $this->dataTables
            ->eloquent(
                $this->query()
            )
            ->editColumn('link', function (Donor $donor) {
                return new HtmlString("<a href='" . route('donors.edit', $donor) . "' target='_blank'>" . $donor->link . "</a>");
            })
            ->editColumn('id', function (Donor $donor) {
                return new HtmlString("<input type='checkbox' value='$donor->id' name='ids[]'/>");
            })
            ->toJson();
    }

    public function html()
    {
        $donor = Donor::find(15);
        $donor->companies;
        return $this->builder
            ->columns([
                'id'              => ['orderable' => false, 'title' => ''],
                'link'            => ['title' => __('company.link')],
                'title'           => ['title' => __('company.title')],
                'reviews_count'   => ['title' => __('company.reviews_count')],
                'companies_count' => ['title' => __('company.companies_count')],
            ]);
    }
}