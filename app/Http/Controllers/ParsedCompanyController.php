<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\ParsedCompany;
use App\Models\Review;
use App\ParserLog;
use Illuminate\Http\Request;
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
                        ->withCount('reviews')
                        ->where('company_id', null)
                )
                ->editColumn('id', function (ParsedCompany $parsedCompany) {
                    return new HtmlString("<input type='checkbox' value='$parsedCompany->id' name='ids[]'/>");
                })
                ->editColumn('site', function (ParsedCompany $parsedCompany) {
                    return new HtmlString("<a href='" . external_link($parsedCompany->site) . "' target='_blank'>" . $parsedCompany->site . "</a>");
                })
                ->editColumn('donor_page', function (ParsedCompany $parsedCompany) {
                    return new HtmlString("<a href='" . $parsedCompany->donor_page . "' target='_blank'>" . str_limit($parsedCompany->donor_page, 50) . "</a>");
                })
                ->toJson();
        }
        $html   = $this->builder
            ->columns([
                'id' => ['orderable' => false, 'title' => ''],
                'title',
                'phone',
                'donor_page',
                'site',
                'city',
                'address',
                'reviews_count',
                'created_at',
                'updated_at',
            ])
            ->parameters([
                "lengthMenu" => [[20, 50, 100, 200, 500],[20, 50, 100, 200, 500],],
            ]);
        $logs   = ParserLog::paginate();
        $donors = Donor::all();
        return view('admin.parsed_companies.index', [
            'html'   => $html,
            'logs'   => $logs,
            'donors' => $donors,
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
        $action = $request->get('action');

        if ($action == 'group') {
            $ids        = $request->get('ids');
            $company_id = $request->get('company_id');
            ParsedCompany::whereIn('id', $ids)->update(['company_id' => $company_id]);
            Review::whereIn('parsed_company_id', $ids)->update(['company_id' => $company_id]);

            return redirect()->back()->with('companies_grouped', $company_id);
        }
        if ($action == 'new_company') {
            $ids = $request->get('ids');
            //
            return redirect()->route('companies.create', ['ids' => implode(',', $ids)]);
        }
        return redirect()->back();
    }

    public function detach(ParsedCompany $parsedCompany)
    {
        $parsedCompany->company_id = null;
        $parsedCompany->reviews()->update(['company_id' => null]);
        $parsedCompany->save();
        return redirect()->back()->with('success', 'Компания отвязана!');
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
