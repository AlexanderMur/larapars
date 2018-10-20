<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[]|\Illuminate\Pagination\LengthAwarePaginator $reviews */
?>

@extends('admin.reviews.layout')

@section('title','Новые отзывы')
@section('content2')
    <form action="" oninput="this.submit()" class="mb-2">
        <select name="favouriteCompany" class="select-medium form-control">
            <option value="">Все</option>
            <option value="1" {{request('favouriteCompany') == 1 ? 'selected' : ''}}>Только избранные компании</option>
        </select>
    </form>
    <div class="reviews__infinity mb-4">
        @include('admin.reviews.partials._list',['dont_show_likes'=>true])
        <button class="btn btn-primary reviews__load-more" data-page="{{$reviews->currentPage()}}">Загрузить еще</button>
    </div>
@stop

