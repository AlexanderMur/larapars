<?php
/** @var \App\Models\Company[] $companies */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Companies</h1>
        @include('admin.partials.table',['table'=>($companies->toArray())])
    </div>
@stop