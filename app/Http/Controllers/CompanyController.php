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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Builder $builder)
    {
        if (request()->ajax()) {
            return \DataTables
                ::eloquent(
                    Company::with(['donor', 'reviews'])
                        ->select([
                            'companies.*',
                            \DB::raw('COUNT(reviews.id) as reviews_count')
                        ])
                        ->join('reviews', 'reviews.company_id', '=', 'companies.id')
                        ->groupBy('id')
                )

                ->toJson();
        }
        $html = $builder->columns([
            'id',
            'phone',
            'single_page_link',
            'site',
            'title',
            'address',
            'donor.link',
            'donor.title',
            'reviews_count'=>['searchable'=>false],
            'created_at',
            'updated_at',
        ]);

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
