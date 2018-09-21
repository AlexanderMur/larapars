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
            return \DataTables::eloquent(
                    Review::query()
                )
                ->toJson();
        }

        $html = $builder->columns([
            ['data' => 'id', 'name' => 'id', 'title' => 'id'],
            ['data' => 'company_id', 'name' => 'company_id', 'title' => 'company_id'],
            ['data' => 'title', 'name' => 'title', 'title' => 'title'],
            ['data' => 'text', 'name' => 'text', 'title' => 'text'],
            ['data' => 'rating', 'name' => 'rating', 'title' => 'rating'],
            ['data' => 'donor_created_at', 'name' => 'donor_created_at', 'title' => 'donor_created_at'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'created_at'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'updated_at'],
            ['data' => 'donor_id', 'name' => 'donor_id', 'title' => 'donor_id'],
            ['data' => 'name', 'name' => 'name', 'title' => 'name'],
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
