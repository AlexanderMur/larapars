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
            return \DataTables::eloquent(
                Company::query()
            )
                ->toJson();
        }
        $html = $builder->columns([
            ['data' => 'id', 'name' => 'id', 'title' => 'id'],
            ['data' => 'phone', 'name' => 'phone', 'title' => 'phone'],
            ['data' => 'single_page_link', 'name' => 'single_page_link', 'title' => 'single_page_link'],
            ['data' => 'site', 'name' => 'site', 'title' => 'site'],
            ['data' => 'title', 'name' => 'title', 'title' => 'title'],
            ['data' => 'address', 'name' => 'address', 'title' => 'address'],
            ['data' => 'donor_id', 'name' => 'donor_id', 'title' => 'donor_id'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'created_at'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'updated_at'],
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
