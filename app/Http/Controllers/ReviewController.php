<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Builder $builder
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Builder $builder)
    {
        if (request()->ajax()) {
            return \DataTables
                ::eloquent(
                    Review::with(['donor','company'])->select('reviews.*')
                )
                ->toJson();
        }

        $html = $builder
            ->columns([
                'id',
                'title',
                'text',
                'good',
                'donor_created_at',
                'created_at',
                'updated_at',
                'name',
                'company.title',
                'company.site',
                'company.single_page_link',

            ])
            ->addCheckbox([],true);

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
        $review = Review::withTrashed()->whereId($id)->first();
        return view('admin.reviews.edit',[
            'review' => $review,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Review $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {

        $review = Review::withTrashed()->whereId($id)->first();
        $review->deleted_at = $request->get('deleted_at');
        $review->update($request->all());
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
