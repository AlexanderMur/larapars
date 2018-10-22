<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 22.10.2018
 * Time: 15:37
 */

namespace App\DataTables;


use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CompanyDataTable extends DataTable
{
    public function query()
    {
        return Company
            ::select([
                'companies.*',
            ])
            ->withCount([
                'reviews',
                'parsed_companies as donors_count',
                'reviews as good_reviews_count' => function (Builder $query) {
                    $query->where('good', 1);
                },
                'reviews as bad_reviews_count'  => function (Builder $query) {
                    $query->where('good', 0);
                },
            ])
            ->groupBy('id');
    }

    public function ajax()
    {
        return $this->dataTables
            ->eloquent(
                $this->query()
            )
            ->editColumn('id', function (Company $company) {
                return new HtmlString("<input type='checkbox' value='$company->id' name='ids[]'/>");
            })
            ->addColumn('action', function (Company $company) {
                return view('admin.companies.actions', ['company' => $company,]);
            })
            ->toJson();
    }

    public function html()
    {
        return $this->builder
            ->columns([
                'id'                 => ['orderable' => false, 'title' => ''],
                'action'             => ['searchable' => false, 'orderable' => false],
                'title'              => ['title' => __('company.title')],
                'phone'              => ['title' => __('company.phone')],
                'site'               => ['title' => __('company.site')],
                'city'               => ['title' => __('company.city')],
                'address'            => ['title' => __('company.address')],
                'donors_count'       => ['title' => __('company.donors_count'), 'searchable' => false],
                'good_reviews_count' => ['title' => __('company.good_reviews_count'), 'searchable' => false],
                'bad_reviews_count'  => ['title' => __('company.bad_reviews_count'), 'searchable' => false],
                'reviews_count'      => ['title' => __('company.reviews_count'), 'searchable' => false],
                'created_at'         => ['title' => __('company.created_at')],
                'updated_at'         => ['title' => __('company.updated_at')],
            ]);
    }
}