<?php
/**
 * @var \App\Models\Company|\App\Models\ParsedCompany $company
 * @var \Illuminate\Support\Collection|\App\Models\Review[] $reviews
 * @var \Illuminate\Support\Collection|\App\Models\ParsedCompany[] $parsed_companies
 */
?>

<div>
    <div id="tab" class="btn-group btn-group-justified" data-toggle="buttons">
        <a href="#reviews" class="btn btn-primary active" data-toggle="tab">
            <input type="radio"/>Отзывы ({{$reviews->count()}})
        </a>
        <a href="#parsed_companies" class="btn btn-primary" data-toggle="tab">
            <input type="radio"/>Доноры ({{$parsed_companies->count()}})
        </a>
    </div>

    <div class="tab-content mt-3">

        <div class="tab-pane fade active in" id="reviews">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#all_reviews">Все ({{$reviews->where('deleted_at','===',null)->count()}})</a>
                </li>
                <li>
                    <a href="#good_reviews">Положительные ({{$reviews->where('deleted_at','===',null)->where('good','===',true)->count()}})</a>
                </li>
                <li>
                    <a href="#bad_reviews">Отрицательные ({{$reviews->where('deleted_at','===',null)->where('good','===', false)->count() }})</a>
                </li>
                <li>
                    <a href="#unrated_reviews">Не оцененные ({{$reviews->where('deleted_at','===',null)->where('good','===',null)->count()}})</a>
                </li>
                <li>
                    <a href="#deleted_reviews">Удалённые ({{$reviews->where('deleted_at','!==',null)->where('trashed_at','===',null)->count()}})</a>
                </li>
                <li>
                    <a href="#trashed_reviews">В корзине ({{$reviews->where('deleted_at','!==',null)->where('trashed_at','!==',null)->count()}})</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="all_reviews">
                    @foreach ($reviews->where('deleted_at','===',null) as $review)

                        @include('admin.reviews._review')
                    @endforeach
                </div>
                <div class="tab-pane fade" id="good_reviews">

                    @foreach ($reviews->where('deleted_at','===',null)->where('good','===',true) as $review)
                        @include('admin.reviews._review')
                    @endforeach
                </div>
                <div class="tab-pane fade" id="bad_reviews">

                    @foreach ($reviews->where('deleted_at','===',null)->where('good','===',false) as $review)
                        @include('admin.reviews._review')
                    @endforeach
                </div>
                <div class="tab-pane fade" id="unrated_reviews">

                    @foreach ($reviews->where('deleted_at','===',null)->where('good','===',null) as $review)
                        @include('admin.reviews._review')
                    @endforeach
                </div>
                <div class="tab-pane fade" id="deleted_reviews">

                    @foreach ($reviews->where('deleted_at','!==',null)->where('trashed_at','===',null) as $review)
                        @include('admin.reviews._review')
                    @endforeach
                </div>
                <div class="tab-pane fade" id="trashed_reviews">
                    @foreach ($reviews->where('trashed_at','!==',null) as $review)
                        @include('admin.reviews._review')
                    @endforeach
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="parsed_companies">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#sub21">Доноры</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="sub21">

                    @foreach ($parsed_companies as $parsed_company)
                        @include('admin.parsed_companies._parsed_company')
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>