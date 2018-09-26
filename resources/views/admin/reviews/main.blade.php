<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[] $reviews */
?>

@extends('admin.layout')


@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Review edit</h1>
        <reviews :reviews="{{json_encode($reviews)}}"></reviews>
    </div>
@stop
