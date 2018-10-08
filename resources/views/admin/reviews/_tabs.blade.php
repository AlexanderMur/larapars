<?php
/**
 * @var \App\Models\Review[]|\Illuminate\Support\Collection $reviews
 */
$id = uniqid();
?>
<ul class="nav nav-tabs">
    <li class="active">
        <a href="#all_reviews-{{$id}}">Все ({{$reviews->where('deleted_at','===',null)->count()}})</a>
    </li>
    <li>
        <a href="#good_reviews-{{$id}}">Положительные
            ({{$reviews->where('deleted_at','===',null)->where('good','===',true)->count()}})</a>
    </li>
    <li>
        <a href="#bad_reviews-{{$id}}">Отрицательные
            ({{$reviews->where('deleted_at','===',null)->where('good','===', false)->count() }})</a>
    </li>
    <li>
        <a href="#unrated_reviews-{{$id}}">Не оцененные
            ({{$reviews->where('deleted_at','===',null)->where('good','===',null)->count()}})</a>
    </li>
    <li>
        <a href="#deleted_reviews-{{$id}}">Удалённые
            ({{$reviews->where('deleted_at','!==',null)->where('trashed_at','===',null)->count()}})</a>
    </li>
    <li>
        <a href="#trashed_reviews-{{$id}}">В корзине
            ({{$reviews->where('deleted_at','!==',null)->where('trashed_at','!==',null)->count()}})</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade active in" id="all_reviews-{{$id}}">
        @foreach ($reviews->where('deleted_at','===',null) as $review)

            @include('admin.reviews._review')
        @endforeach
    </div>
    <div class="tab-pane fade" id="good_reviews-{{$id}}">

        @foreach ($reviews->where('deleted_at','===',null)->where('good','===',true) as $review)
            @include('admin.reviews._review')
        @endforeach
    </div>
    <div class="tab-pane fade" id="bad_reviews-{{$id}}">

        @foreach ($reviews->where('deleted_at','===',null)->where('good','===',false) as $review)
            @include('admin.reviews._review')
        @endforeach
    </div>
    <div class="tab-pane fade" id="unrated_reviews-{{$id}}">

        @foreach ($reviews->where('deleted_at','===',null)->where('good','===',null) as $review)
            @include('admin.reviews._review')
        @endforeach
    </div>
    <div class="tab-pane fade" id="deleted_reviews-{{$id}}">

        @foreach ($reviews->where('deleted_at','!==',null)->where('trashed_at','===',null) as $review)
            @include('admin.reviews._review')
        @endforeach
    </div>
    <div class="tab-pane fade" id="trashed_reviews-{{$id}}">
        @foreach ($reviews->where('trashed_at','!==',null) as $review)
            @include('admin.reviews._review')
        @endforeach
    </div>
</div>