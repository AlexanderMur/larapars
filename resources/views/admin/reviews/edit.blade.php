<?php
/** @var \App\Models\Review $review */
?>

@extends('admin.layout')


@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Review edit</h1>
        @include('admin.reviews.edit-form',[
            'reviews' => $review
        ])
    </div>
@stop