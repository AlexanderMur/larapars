<?php
/**
 * @var \App\Models\Company|\App\Models\ParsedCompany $company
 */
$curScope = request('scope') ?: '';
?>

<div data-id="{{$company->id}}" class="reviews__tabs">
    <ul class="nav nav-tabs">
        <li class="{{$curScope == 'all' ? 'active' : ''}}"><a href="#" class="reviews__nav-link" data-scope="all">Все({{$company->reviews_count ?? 0}})</a></li>
        <li class="{{$curScope == 'good' ? 'active' : ''}}"><a href="#" class="reviews__nav-link" data-scope="good">Положительные({{$company->good_reviews_count ?? 0}})</a></li>
        <li class="{{$curScope == 'bad' ? 'active' : ''}}"><a href="#" class="reviews__nav-link" data-scope="bad">Отрицательные({{$company->bad_reviews_count ?? 0}})</a></li>
        <li class="{{$curScope == 'unrated' ? 'active' : ''}}"><a href="#" class="reviews__nav-link" data-scope="unrated">Не оцененные({{$company->unrated_reviews_count ?? 0}})</a></li>
        <li class="{{$curScope == 'deleted' ? 'active' : ''}}"><a href="#" class="reviews__nav-link" data-scope="deleted">Удалённые({{$company->deleted_reviews_count ?? 0}})</a></li>
        <li class="{{$curScope == 'trashed' ? 'active' : ''}}"><a href="#" class="reviews__nav-link" data-scope="trashed">В корзине({{$company->trashed_reviews_count ?? 0}})</a></li>
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














