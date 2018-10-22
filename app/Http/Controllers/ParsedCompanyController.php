<?php

namespace App\Http\Controllers;

use App\DataTables\ParsedCompaniesDataTable;
use App\Exports\Export;
use App\Http\Requests\Request;
use App\Models\Donor;
use App\Models\ParsedCompany;
use App\ParserLog;

class ParsedCompanyController extends Controller
{


    /**
     * @param ParsedCompaniesDataTable $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(ParsedCompaniesDataTable $dataTable)
    {
        if (request()->ajax()) {
            return $dataTable->ajax();
        }

        return view('admin.parsed_companies.index', [
            'html'   => $dataTable->html(),
            'logs'   => ParserLog::paginate(),
            'donors' => Donor::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function bulk(Request $request)
    {


        if ($request->action2 === 'export' || $request->action === 'export') {
            return \Excel::download(new Export($request->ids), 'model.xls');
        }
        if ($request->action == 'group') {
            $ids        = $request->get('ids');
            $company_id = $request->get('company_id');
            ParsedCompany::whereIn('id', $ids)->update(['company_id' => $company_id]);

            return redirect()->back()->with('companies_grouped', $company_id);
        }
        if ($request->action == 'new_company') {
            $ids = $request->get('ids');
            //
            return redirect()->route('companies.create', ['ids' => implode(',', $ids)]);
        }
        return redirect()->back();
    }

    public function detach(ParsedCompany $parsedCompany)
    {
        $parsedCompany->company_id = null;
        $parsedCompany->save();
        return redirect()->back()->with('success', 'Компания отвязана!');
    }

    public function getReviews($id)
    {
        $parsed_company = ParsedCompany::find($id)->withCount([
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
        ])->first();
        $scope          = request('scope');

        $reviews = collect();
        if ($scope == 'all') {
            $reviews = $parsed_company->reviews();
        }
        if ($scope == 'good') {
            $reviews = $parsed_company->reviews()->where('good', '=', true);
        }
        if ($scope == 'bad') {
            $reviews = $parsed_company->reviews()->where('good', '!=', false);
        }
        if ($scope == 'unrated') {
            $reviews = $parsed_company->reviews()->where('good', '=', null);
        }
        if ($scope == 'deleted') {
            $reviews = $parsed_company->reviews()->where('deleted_at', '!=', null)->where('trashed_at', '=', null);
        }
        if ($scope == 'trashed') {
            $reviews = $parsed_company->reviews()->where('trashed_at', '!=', null);
        }
        if ($reviews->count() > 0) {
            $reviews = $reviews->paginate(3)->appends(['scope' => $scope]);
        }
        return view('admin.reviews.partials._tabs', [
            'company' => $parsed_company,
            'reviews' => $reviews,
        ]);
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
