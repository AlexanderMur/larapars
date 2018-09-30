<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Review;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Builder;

class ReviewController extends Controller
{

    /**
     * @var Builder
     */
    public $builder;

    public function __construct(Builder $builder)
    {

        $this->builder = $builder;
    }

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
                    Review::with(['company'])->select('reviews.*')
                )
                ->toJson();
        }

        $html = $builder
            ->columns([
                'id',
                'title',
                'text',
                'good',
                'created_at',
                'updated_at',
                'name',
                'company.title',
                'company.site',
                'company.single_page_link',

            ])
            ->addCheckbox([], true);

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
        if(request()->get('_method') == 'delete'){
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
     */
    public function update(Request $request, $id)
    {

        $review = Review::withTrashed()->where('id', $id)->first();
        $review->good = $request->has('good');
        $review->update($request->all());
        $request->session()->flash('success', 'Отзыв изменен');
        if ($request->ajax()) {
            return view('admin.reviews.edit-form', ['review' => $review]);
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

    function main()
    {
        $reviews = Review::with('donor')->take(10)->get();

        return view('admin.reviews.new', [
            'reviews' => $reviews,
        ]);
    }

    function new()
    {

        $reviews = Review::where('good', null)
            ->take(20)
            ->get();

        return view('admin.reviews.new', [
            'reviews' => $reviews,
        ]);
    }

    function archive()
    {

        if (request()->ajax()) {
            return \DataTables
                ::eloquent(
                    Review::with(['company'])
                        ->select('reviews.*')
                        ->where('good', '!=', null)
                )
                ->editColumn('id',function (Review $review){
                    return new HtmlString("<input type='checkbox' value='$review->id' name='reviews[]'/>");
                })
                ->editColumn('company.title', function (Review $review) {
                    if($review->company_id){
                        ob_start();
                        ?>
                        <b><a href="<?php echo route('companies.show', $review->company_id) ?>"><?php echo $review->company->title ?></a></b>
                        <br>
                        <a href="<?php echo $review->donor_link ?>">Перейти к странице донора</a>
                        <?php
                        return new HtmlString(ob_get_clean());
                    }
                })
                ->editColumn('text', function (Review $review) {
                    ob_start();
                    echo $review->text
                    ?>
                    <div class="actions">
                        <span class="model-edit"><a href="<?php echo route('reviews.edit', $review) ?>">Редактировать</a></span>
                        |
                        <span class="model-trash"><a
                                class="text-danger"
                                href="<?php echo route('reviews.destroy', $review) ?>"
                            >В корзину</a></span>
                    </div>
                    <?php
                    return new HtmlString(ob_get_clean());
                })
                ->editColumn('good', function (Review $review) {
                    ob_start();
                    if ($review->good === true) {
                        ?>
                        <i class="fa fa-fw fa-2x fa-thumbs-up text-success"></i>
                        <?php
                    }
                    if ($review->good === false) {
                        ?>
                        <i class="fa fa-fw fa-2x fa-thumbs-down text-danger"></i>
                        <?php
                    }

                    return new HtmlString(ob_get_clean());
                })
                ->toJson();
        }

        $html = $this->builder
            ->columns([
                'id' => ['orderable' => false,'title'=>''],
                'name',
                'title',
                'text',
                'good' => ['width' => '1%'],
                'rated_at',
                'company.title',
            ])
            ->parameters([
                'order' => [[4, "desc"]],

            ]);
        return view('admin.reviews.archive', [
            'html' => $html,
        ]);
    }
    function updateMany(){
        if(request()->get('action') == 'group'){
            $ids = \request()->get('reviews');
            $review_with_group = Review::where('group_id','!=',null)->first();
            if($review_with_group){
                $group_id = $review_with_group->group_id;
                $num = Review::whereIn('id',$ids)->update(['group_id'=>$group_id]);
                return redirect()->back()->with('success',$num . ' отзыва(ов) сгруппированы');
            }
            $group = Group::create();
            $num = Review::whereIn('id',$ids)->update(['group_id'=>$group->id]);
            return redirect()->back()->with('success',$num . ' отзыва(ов) сгруппированы');
        }
    }
}
