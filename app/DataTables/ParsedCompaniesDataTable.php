<?php


namespace App\DataTables;


use App\Models\ParsedCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ParsedCompaniesDataTable extends DataTable
{
    public function query()
    {
        return ParsedCompany
            ::select([
                'parsed_companies.*',
            ])
            ->withCount('reviews')
            ->where('company_id', null);
    }

    public function ajax()
    {
        return $this->dataTables
            ->eloquent(
                $this->query()
            )
            ->filter(function (Builder $query) {

                $query->where(function (Builder $query) {
                    $phones = request('phone') ? explode(' ', request('phone')) : [];

                    foreach ($phones as $phone) {
                        // language=MySQL prefix=SELECT/**/*/**/from/**/parsed_companies/**/WHERE/**/
                        $query->orWhereRaw("replace(replace(replace(replace(replace(phone,' ',''),'(',''),')',''),'+',''),'-','') LIKE '%$phone%'");
                    }
                    $sites = request('site') ? explode(' ', request('site')) : [];

                    foreach ($sites as $site) {
                        // language=MySQL prefix=SELECT/**/*/**/from/**/parsed_companies/**/WHERE/**/
                        $query->orWhereRaw("site LIKE '%$site%'");
                    }
                });
            }, true)
            ->editColumn('id', function (ParsedCompany $parsedCompany) {
                return new HtmlString("<input type='checkbox' value='$parsedCompany->id' name='ids[]'/>");
            })
            ->addColumn('action', function (ParsedCompany $parsedCompany) {
                return new HtmlString("<a href='" . route('parsed_companies.bulk') . "'>Найти дубли</a>");
            })
            ->editColumn('site', function (ParsedCompany $parsedCompany) {
                return new HtmlString("<a href='" . external_link($parsedCompany->site) . "' target='_blank'>" . $parsedCompany->site . "</a>");
            })
            ->editColumn('donor_page', function (ParsedCompany $parsedCompany) {
                return new HtmlString("<a href='" . $parsedCompany->donor_page . "' target='_blank'>" . str_limit($parsedCompany->donor_page, 50) . "</a>");
            })
            ->editColumn('reviews_count', function (ParsedCompany $parsedCompany) {
                return new HtmlString("<a href='" . route('companies.create', ['ids' => $parsedCompany->id]) . "' target='_blank'>" . $parsedCompany->reviews_count . "</a>");
            })
            ->toJson();
    }

    public function html()
    {
        return $this->builder
            ->columns([
                'id'            => ['orderable' => false, 'title' => ''],
                'action'        => ['orderable' => false, 'title' => ''],
                'title'         => ['title' => __('company.title')],
                'phone'         => ['title' => __('company.phone')],
                'donor_page'    => ['title' => __('company.donor_page')],
                'site'          => ['title' => __('company.site')],
                'city'          => ['title' => __('company.city')],
                'address'       => ['title' => __('company.address')],
                'reviews_count' => ['title' => __('company.reviews_count'), 'searchable' => false],
                'created_at'    => ['title' => __('company.created_at')],
                'updated_at'    => ['title' => __('company.updated_at')],
            ])
            ->ajax([
                'data' => 'function (d) {
    d.site = $(\'input[name=site]\').val()
    d.phone = $(\'input[name=phone]\').val()
}',
            ])
            ->parameters([
                'order' => [[8, "desc"]],
            ]);
    }
}