<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Doctrine\DBAL\Query\QueryBuilder;
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
                            'reviews as good_reviews_count' => function (Query $query) {
                                $query->where('good',1);
                            },
                            'reviews as bad_reviews_count' => function (Query $query) {
                                $query->where('good',0);
                            }
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
                'action'              => ['searchable' => false, 'orderable' => false],
                'id',
                'title',
                'phone',
                'single_page_link',
                'site',
                'address',
                'created_at',
                'good_reviews_count' => ['searchable' => false],
                'bad_reviews_count'   => ['searchable' => false],
                'reviews_count'       => ['searchable' => false],
                'updated_at',
            ])
            ->addCheckbox([], true);;

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
        $company->load(['reviews' => function ($query) {
            $query->withTrashed();
        }]);
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
        $this->validate($request, [
            'title'            => 'required',
            'site'             => 'required',
            'single_page_link' => 'required',
            'address'          => 'required',
        ]);
        $company->update($request->all());
        return redirect()->back()->with('success', 'Компания изменена!');
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
