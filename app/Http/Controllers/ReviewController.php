<?php

namespace App\Http\Controllers;

use App\DataTables\ReviewsDataTable;
use App\Models\Donor;
use App\Models\Group;
use App\Models\Review;
use App\ParserLog;
use App\Services\ParserService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;

class ReviewController extends Controller
{

    /**
     * @var Builder
     */
    public $builder;
    /**
     * @var ReviewService
     */
    public $reviewService;
    /**
     * @var ParserService
     */
    public $parserService;

    public function __construct(Builder $builder, ReviewService $reviewService, ParserService $parserService)
    {

        $this->builder       = $builder;
        $this->reviewService = $reviewService;
        $this->parserService = $parserService;
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
        if (request()->get('_method') == 'delete') {
            return $this->destroy($id);
        }
        return 'ok';
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

        if (request()->ajax()) {
            return view('admin.reviews.edit-form', [
                'review' => $review,
            ]);
        }
        return view('admin.reviews.edit', [
            'review' => $review,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function update(Request $request, $id)
    {

        $review       = Review::withTrashed()->where('id', $id)->first();
        $review->good = $request->has('good');
        $review->update($request->all());
        $request->session()->flash('success', 'Отзыв изменен');
        if ($request->ajax()) {
            return response()->json(
                view('admin.reviews.edit-form', ['review' => $review])->render()
            );
        }
        return redirect()->back();
    }

    public function like(Review $review)
    {
        $review->like();
        return response()->json('ok');
    }

    public function dislike(Review $review)
    {
        $review->dislike();
        return response()->json('ok');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $review = Review::withTrashed()->find($id);
        $review->trash();
        if (request()->ajax()) {
            return response()->json('ok');
        }
        return redirect()->back();
    }

    public function data(Request $request)
    {

        $reviews = Review
            ::select('reviews.*')
            ->groupBy('reviews.id')
            ->join('donors', 'donors.id', '=', 'reviews.donor_id')
            ->with('donor');

        if ($request->has('orderBy')) {
            $reviews->orderBy(
                $request->get('orderBy'),
                $request->get('dir', 'asc')
            );
        }

        $currentTab = $request->get('tab');
        if ($currentTab === 'new') {
            $reviews->unrated(true);
        }
        if ($currentTab === 'archive') {
            $reviews->unrated(false);
        }
        $reviews = $reviews->paginate(20);
        return response()->json($reviews);
    }


    function new()
    {
        $reviews = Review::filter(request()->all())->where('good', null)->paginate(15);
        if (\request()->ajax()) {
            return response()->json([
                'currentPage' => $reviews->currentPage(),
                'html'        => '' . view('admin.reviews.partials._list', ['reviews' => $reviews]),
            ]);
        }
        return view('admin.reviews.new',
            $this->parserService->getStatistics(),
            [
                'logs'    => ParserLog::paginate(),
                'donors'  => Donor::all(),
                'reviews' => $reviews,
            ]
        );
    }

    function archive(ReviewsDataTable $dataTable)
    {

        if (request()->ajax()) {
            return $dataTable->ajax();
        }

        return view('admin.reviews.archive',
            $this->parserService->getStatistics(),
            [
                'html'   => $dataTable->html(),
                'logs'   => ParserLog::paginate(),
                'donors' => Donor::all(),
            ]
        );
    }

    function updateMany()
    {
        if (request()->get('action') == 'group') {
            $ids               = \request()->get('reviews');
            $review_with_group = Review::where('group_id', '!=', null)->first();
            if ($review_with_group) {
                $group_id = $review_with_group->group_id;
                $num      = Review::whereIn('id', $ids)->update(['group_id' => $group_id]);
                return redirect()->back()->with('success', $num . ' отзыва(ов) сгруппированы');
            }
            $group = Group::create();
            $num   = Review::whereIn('id', $ids)->update(['group_id' => $group->id]);
            return redirect()->back()->with('success', $num . ' отзыва(ов) сгруппированы');
        }
    }
}
