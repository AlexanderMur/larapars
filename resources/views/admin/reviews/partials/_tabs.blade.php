<?php
/**
 * @var \App\Models\Company|\App\Models\ParsedCompany $company
 */
$curScope = request('scope') ?: 'all';
?>

<div data-id="{{$company->id}}" class="reviews__tabs">
    <ul class="nav nav-tabs">
        <li role="presentation" class="{{$curScope == 'all' ? 'active' : ''}}"><a class="reviews__nav-link" data-scope="all">Все({{$company->reviews_count ?? 0}})</a></li>
        <li role="presentation" class="{{$curScope == 'good' ? 'active' : ''}}"><a class="reviews__nav-link" data-scope="good">Положительные({{$company->good_reviews_count ?? 0}})</a></li>
        <li role="presentation" class="{{$curScope == 'bad' ? 'active' : ''}}"><a class="reviews__nav-link" data-scope="bad">Отрицательные({{$company->bad_reviews_count ?? 0}})</a></li>
        <li role="presentation" class="{{$curScope == 'unrated' ? 'active' : ''}}"><a class="reviews__nav-link" data-scope="unrated">Не оцененные({{$company->unrated_reviews_count ?? 0}})</a></li>
        <li role="presentation" class="{{$curScope == 'deleted' ? 'active' : ''}}"><a class="reviews__nav-link" data-scope="deleted">Удалённые({{$company->deleted_reviews_count ?? 0}})</a></li>
        <li role="presentation" class="{{$curScope == 'trashed' ? 'active' : ''}}"><a class="reviews__nav-link" data-scope="trashed">В корзине({{$company->trashed_reviews_count ?? 0}})</a></li>
    </ul>

    <div>
        @if(isset($reviews) && method_exists($reviews,'render'))
            @foreach ($reviews as $review)
                @include('admin.reviews._review')
            @endforeach
            {{$reviews->render()}}
        @endif
    </div>


</div>














