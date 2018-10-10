<?php
/**
 * @var \App\Models\Review $review
 */

if ($review->good === true) {
    $class = 'review-good';
} else if ($review->good === false){
    $class = 'review-bad';
} else {
    $class = '';
}
?>
<div class="panel panel-default has-actions _review {{$class}}">
    <div class="panel-body">

        <div class="row mb-3">
            <div class="col-lg-6">

                <h3 class="my-0 d-inline">
                    {{$review->title}}

                </h3>

                <a href="{{route('reviews.like',$review)}}" class="like-review"><i class="fa fa-2x fa-thumbs-up"></i></a>
                <a href="{{route('reviews.dislike',$review)}}" class="dislike-review"><i class="fa fa-2x fa-thumbs-down"></i></a>
                @isset($review->group->reviews)
                    @if ($review->group->reviews->count() >= 2)
                        @include('admin.companies.dublicates',[
                            'group' => $review->group,
                        ])
                    @endif
                @endisset
            </div>
            <div class="col-lg-6 text-right">
                <i class="fa fa-user fa-fw"></i>
                {{$review->name}}
                /
                <i class="fa fa-clock-o fa-fw"></i>
                <time>{{$review->created_at}}</time>
            </div>
        </div>

        {!! filter_text($review->text) !!}
        <p>
            <b>{{$review->donor->title}}</b>
        </p>
        <p>
            <a href="{{$review->donor_link}}" target="_blank">{{str_limit($review->donor_link,70)}}</a>
        </p>


        <div class="actions">
            <a class="btn btn-primary btn-sm" href="{{route('reviews.edit',$review)}}"><i class="fa fa-edit"></i>
                Редактировать</a>
            <a
                class="btn btn-danger btn-sm"
                href="{{route('reviews.destroy',[ $review,'_method=delete' ])}}"
            ><i class="fa fa-trash"></i> В корзину</a>

        </div>
    </div>
</div>