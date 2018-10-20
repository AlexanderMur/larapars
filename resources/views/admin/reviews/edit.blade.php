<?php
/** @var \App\Models\Review $review */
?>

@extends('admin.layout')

@section('title','Редактировать отзывов')
@section('content')

    <div id="page-wrapper">
        <h1 class="page-header ">Редактировать отзыв</h1>
        @include('admin.reviews.edit-form',[
            'reviews' => $review
        ])
    </div>
@stop

