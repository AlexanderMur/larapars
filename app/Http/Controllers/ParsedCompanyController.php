<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ParsedCompany;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\Html\Builder;

class ParsedCompanyController extends Controller
{
    /**
     * @var \DataTables
     */
    /**
     * @var \DataTables|Builder
     */
    public $builder;

    /**
     * Display a listing of the resource.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {

        $this->builder = $builder;
    }

    public function index()
    {
        if (request()->ajax()) {
            return \DataTables
                ::eloquent(
                    ParsedCompany
                        ::select([
                            'parsed_companies.*',
                        ])
                )
                ->editColumn('id', function (ParsedCompany $parsedCompany) {
                    return new HtmlString("<input type='checkbox' value='$parsedCompany->id' name='ids[]'/>");
                })
                ->toJson();
        }
        $html = $this->builder
            ->columns([
                'id' => ['orderable' => false, 'title' => ''],
                'title',
                'phone',
                'donor_page',
                'site',
                'address',
                'created_at',
                'updated_at',
            ]);

        return view('admin.parsed_companies.index', compact('html'));
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

    public function bulk(Request $request){
        $action = $request->get('action');

        if($action == 'group'){
            $ids = $request->get('ids');
            //
            return view('admin.parsed_companies.new',['ids'=>$ids,'action'=>$action]);
        }
        if($action == 'new_company'){
            $ids = $request->get('ids');
            //
            return view('admin.parsed_companies.new',['ids'=>$ids,'action'=>$action]);
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
