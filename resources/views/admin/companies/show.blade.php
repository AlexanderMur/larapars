<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Company show</h1>
        <h2>
            <a href="{{route('companies.edit',$company)}}" type="button" class="btn btn-sm btn-success">
                <i class="fa fa-edit"></i>
            </a>
            {{$company->title}}
        </h2>
        <div class="row mt-5">
            <div class="col-md-6">
                @include('admin.partials.company-tabs',[
                    'reviews' => $company->reviews,
                    'parsed_companies' => $company->parsed_companies,
                ])
            </div>
        </div>


    </div>
@stop