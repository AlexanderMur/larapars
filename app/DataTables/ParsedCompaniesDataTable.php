<?php


namespace App\DataTables;


use App\Models\ParsedCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use libphonenumber\PhoneNumberUtil;

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

    /**
     * @param $phone
     * @return string
     * @throws \libphonenumber\NumberParseException
     */
    public function normalizePhone($phone)
    {
        $phones = $phone ? explode(', ', $phone) : [];

        $parsed_phones = [];
        foreach ($phones as $phone) {
            $parsed_phones[] = PhoneNumberUtil::getInstance()->parse($phone, 'RU')->getNationalNumber();
        }
        return implode(', ', $parsed_phones);
    }

    public function normalizeTitle($title)
    {
        $title           = mb_convert_case($title, MB_CASE_LOWER, "UTF-8");
        $words_to_remove = ['автосалон', 'отзывы', 'дилерский центр', 'покупателей'];

        foreach ($words_to_remove as $word_to_remove) {
            $title = str_replace($word_to_remove, '', $title);
        }
        $title = preg_replace('/[^a-zA-Zа-яА-Я0-9& ]/ui', '', $title);
        $title = preg_replace('/\b\w\b/ui', '', $title);
        $title = trim($title);
        return $title;
    }

    /**
     * @param $site
     * @return string
     */
    public function normalizeSite($site)
    {
        $phones = $site ? explode(', ', $site) : [];

        $parsed_sites = [];
        foreach ($phones as $site) {
            $parsed_sites[] = str_replace('www.', '', str_replace('http://', '', str_replace('https://', '', $site)));
        }
        return implode(', ', $parsed_sites);
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
                    $titles = request('title') ? explode(' ', request('title')) : [];
                    foreach ($titles as $title) {
                        // language=MySQL prefix=SELECT/**/*/**/from/**/parsed_companies/**/WHERE/**/
                        $query->orWhereRaw("replace(replace(replace(replace(LOWER(title),'автосалон',''), 'отзывы',''), 'дилерский центр',''), 'покупателей','') LIKE '%$title%'");
                    }
                });
            }, true)
            ->editColumn('id', function (ParsedCompany $parsedCompany) {
                return new HtmlString("<input type='checkbox' value='$parsedCompany->id' name='ids[]'/>");
            })
            ->addColumn('action', function (ParsedCompany $parsedCompany) {

                $phone = $this->normalizePhone($parsedCompany->phone);
                $site  = $this->normalizeSite($parsedCompany->site);
                $title = $this->normalizeTitle($parsedCompany->title);
                return new HtmlString("<a href='" . route('parsed_companies.index', ['phone' => $phone, 'site' => $site, 'title' => $title]) . "' target='_blank'>Найти дубли</a>");
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
                'data' => // language=JavaScript prefix=( suffix=)
                    'function (d) {
    d.site = $(\'input[name=site]\').val()
    d.phone = $(\'input[name=phone]\').val()
    d.title = $(\'input[name=title]\').val()
}',
            ])
            ->parameters([
                'order'        => [[8, "desc"]],
                'drawCallback' => // language=JavaScript prefix=( suffix=)
                    'function (e) {
    $(e.nTable).find(\'tbody tr td:nth-child(3)\').mark($(\'input[name=title]\').val())
    $(e.nTable).find(\'tbody tr td:nth-child(4)\').mark($(\'input[name=phone]\').val())
    $(e.nTable).find(\'tbody tr td:nth-child(6)\').mark($(\'input[name=site]\').val())
}',
            ]);
    }
}