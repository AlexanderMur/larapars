<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[] $reviews */
?>

@extends('admin.reviews.layout')


@section('content2')

    @foreach ($reviews as $review)
        @include('admin.reviews.review',[
            'review' => $review,
        ])
    @endforeach
@stop
