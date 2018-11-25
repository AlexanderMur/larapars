<?php

namespace App\Http\Controllers;

use App\DataTables\CompanyDataTable;
use App\Exports\CompanyExport;
use App\Models\Company;
use App\Models\HttpLog;
use App\Models\ParsedCompany;
use App\Models\ParserTask;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param CompanyDataTable $dataTable
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(CompanyDataTable $dataTable)
    {
        if (request()->ajax()) {
            return $dataTable->ajax();
        }
        $html = $dataTable->html();

        return view('admin.companies.index', ['html' => $html]);
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
            $parsed_companies = ParsedCompany::whereIn('id', $ids)->withStats()->get();
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
        $company = Company::where('id', $id)
            ->with([
                'parsed_companies' => function ($query) {
                    $query->withStats();
                },
                'parsed_companies.donor',
                'parsed_companies.history',
                'reviews'          => function ($query) {
                    $query->withTrashed();
                },
                'reviews.donor',
            ])
            ->withStats()
            ->first();


        return view('admin.companies.show', [
            'company' => $company,
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
     * @param Company $company
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

    /**
     * @param Request $request
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function bulk(Request $request)
    {

        if ($request->action2 === 'export' || $request->action === 'export') {
            return \Excel::download(new CompanyExport($request->ids), 'model.xls');
        }
        if ($request->action === 'favourite') {
            Company::whereIn('id',$request->ids)->update(['favourite'=>true]);
        }
        return redirect()->back();
    }

    /**
     * @param Company $company
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function logs(Company $company)
    {
        $task = ParserTask::latest('id')->withStats()->first();
        $http_logs = HttpLog::latest('id')->paginate();
        return response()->json([
            'table'        => view('admin.partials.logs',
                [
                    'logs' => $company->getRelatedLogs(),
                    'http_logs'=>$http_logs
                ]
            )->render(),
            'progress'     => $task->progress->progress ?? 0,
            'progress_max' => $task->progress->progress_max ?? 0,
        ]);
    }
}
