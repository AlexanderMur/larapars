<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[]|\Illuminate\Pagination\LengthAwarePaginator $reviews */
?>

@extends('admin.reviews.layout')


@section('content2')
    <div class="reviews__infinity">
        @include('admin.reviews.partials._list')
        <button class="btn btn-primary reviews__load-more" data-page="{{$reviews->currentPage()}}">Загрузить еще</button>
    </div>
@stop
