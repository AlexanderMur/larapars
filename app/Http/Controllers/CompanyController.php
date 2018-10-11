<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ParsedCompany;
use App\Models\Review;
use App\ParserLog;
use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Builder $builder
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            return \DataTables
                ::eloquent(
                    Company
                        ::select([
                            'companies.*',
                        ])
                        ->withCount([
                            'reviews',
                            'parsed_companies as donors_count',
                            'reviews as good_reviews_count' => function (Query $query) {
                                $query->where('good', 1);
                            },
                            'reviews as bad_reviews_count'  => function (Query $query) {
                                $query->where('good', 0);
                            },
                        ])
                        ->leftJoin('reviews', 'reviews.company_id', '=', 'companies.id')
                        ->groupBy('id')
                )
                ->addColumn('action', function (Company $company) {
                    return view('admin.companies.actions', ['company' => $company,]);
                })
                ->toJson();
        }
        $html = $builder
            ->columns([
                'action'             => ['searchable' => false, 'orderable' => false],
                'id',
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
            ])
            ->parameters([
                "lengthMenu" => [[20, 50, 100, 200, 500], [20, 50, 100, 200, 500],],
                'language'   => __('datatables'),
            ])
            ->addCheckbox([], true);

        return view('users.index', compact('html'));
    }

    public function search()
    {

        $companies  = Company::select();
        $attributes = Company::first()->getAttributes();
        $term       = request()->get('term');

        foreach (explode(' ', $term) as $word) {
            $companies->where(function ($query) use ($word, $attributes) {
                foreach ($attributes as $key => $attribute) {
                    $query->orWhereRaw("LOWER(`$key`) LIKE '%$word%'");
                }
            });
        }
        $companies = $companies->paginate(10);
        return response()->json($companies);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (request()->has('ids')) {
            $ids              = explode(',', request()->get('ids'));
            $parsed_companies = ParsedCompany::whereIn('id', $ids)->get();
            return view('admin.companies.create', [
                'parsed_companies' => $parsed_companies,
            ]);
        }
        return view('admin.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $company              = Company::create(\request()->all());
        $parsed_companies_ids = \request()->get('parsed_companies_ids');
        ParsedCompany::whereIn('id', $parsed_companies_ids)->update(['company_id' => $company->id]);
        Review::whereIn('parsed_company_id', $parsed_companies_ids)->update(['company_id' => $company->id]);
        return redirect()->route('companies.show', $company);
    }

    /**
     * Display the specified resource.
     *
     * @param Company $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::find($id)
            ->with([
                'parsed_companies' => function ($query) {
                    $query->withCount([
                        'reviews',
                        'reviews as good_reviews_count'    => function ($query) {
                            $query->where('good', '=', true);
                        },
                        'reviews as bad_reviews_count'     => function ($query) {
                            $query->where('good', '!=', false);
                        },
                        'reviews as unrated_reviews_count' => function ($query) {
                            $query->where('good', '=', null);
                        },
                        'reviews as deleted_reviews_count' => function ($query) {
                            $query->where('deleted_at', '!=', null)->where('trashed_at', '=', null);
                        },
                        'reviews as trashed_reviews_count' => function ($query) {
                            $query->where('trashed_at', '!=', null);
                        },
                    ]);
                },
                'parsed_companies.donor',
                'parsed_companies.history',
                'reviews',
                'reviews.donor',
            ])
            ->withCount([
                'reviews',
                'reviews as good_reviews_count'    => function ($query) {
                    $query->where('good', '=', true);
                },
                'reviews as bad_reviews_count'     => function ($query) {
                    $query->where('good', '!=', false);
                },
                'reviews as unrated_reviews_count' => function ($query) {
                    $query->where('good', '=', null);
                },
                'reviews as deleted_reviews_count' => function ($query) {
                    $query->where('deleted_at', '!=', null)->where('trashed_at', '=', null);
                },
                'reviews as trashed_reviews_count' => function ($query) {
                    $query->where('trashed_at', '!=', null);
                },
            ])
            ->first();
        $logs = ParserLog::paginate();
        return view('admin.companies.show', [
            'company' => $company,
            'logs'    => $logs,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Company $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {

        if (request()->ajax()) {
            return view('admin.companies.edit-form', [
                'company' => $company,
            ]);
        }
        return view('admin.companies.edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $company->update($request->all());


        $request->session()->flash('success', 'Компания изменена');
        if ($request->ajax()) {
            return view('admin.companies.edit-form', ['company' => $company]);
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
