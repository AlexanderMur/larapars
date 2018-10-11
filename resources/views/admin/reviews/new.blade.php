<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[]|\Illuminate\Pagination\LengthAwarePaginator $reviews */
?>

@extends('admin.reviews.layout')

@section('title','Новые отзывы')
@section('content2')
    <div class="reviews__infinity mb-4">
        @include('admin.reviews.partials._list',['dont_show_likes'=>true])
        <button class="btn btn-primary reviews__load-more" data-page="{{$reviews->currentPage()}}">Загрузить еще</button>
    </div>
@stop
