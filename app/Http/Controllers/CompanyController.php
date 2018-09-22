<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
                    Company::with(['donor'])
                        ->select([
                            'companies.*',
                            \DB::raw('COUNT(reviews.id) as reviews_count')
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
                'action'        => ['searchable' => false, 'orderable' => false],
                'title',
                'id',
                'phone',
                'single_page_link',
                'site',
                'address',
                'donor.link',
                'donor.title',
                'reviews_count' => ['searchable' => false],
                'created_at',
                'updated_at',
            ])
            ->addCheckbox([],true);;

        return view('users.index', compact('html'));
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
     * @param Company $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return view('admin.companies.show', [
            'company' => $company
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
        return view('admin.companies.edit', [
            'company' => $company
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
        $this->validate($request,[
            'title'=>'required',
            'site'=>'required',
            'single_page_link'=>'required',
            'address'=>'required',
        ]);
        $company->update($request->all());
        return redirect()->back()->with('success','Компания изменена!');
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
