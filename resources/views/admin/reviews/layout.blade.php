<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[] $reviews */
?>

@extends('admin.layout')


@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Review edit</h1>
        @include('admin.partials.messages')
        @include('admin.partials.parser.controls')
        <ul class="nav nav-pills nav-justified">
            <li role="presentation" class="{{Route::currentRouteNamed('reviews.new') ? 'active' : ''}}">
                <a href="{{route('reviews.new')}}">Новые ({{$reviews_count}})</a>
            </li>
            <li role="presentation" class="{{Route::currentRouteNamed('reviews.archive') ? 'active' : ''}}">
                <a href="{{route('reviews.archive')}}">Архив ({{$rated_reviews_count}})</a>
            </li>
        </ul>

        @yield('content2')
    </div>
@stop
