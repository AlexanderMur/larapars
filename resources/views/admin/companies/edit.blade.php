<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')


@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Company edit</h1>


        @include('admin.companies.edit-form',[
            'company' => $company,
        ])
    </div>
@stop