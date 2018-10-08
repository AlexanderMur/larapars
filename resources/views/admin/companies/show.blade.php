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
        <a href="{{external_link('google.com')}}" target="_blank">google</a>
        <p>
            Телефон: {{$company->phone}}
        </p>
        <p>
            Сайт: {{$company->site}}
        </p>
        <p>
            Название: {{$company->title}}
        </p>
        <p>
            Город: {{$company->city}}
        </p>
        <p>
            Адрес: {{$company->address}}
        </p>
        <p>
            Дата создания в бд: {{$company->created_at}}
        </p>
        <p>
            Дата обновления в бд: {{$company->updated_at}}
        </p>
        <div class="row mt-5">
            <div class="col-md-12">
                @include('admin.partials.company-tabs',[
                    'reviews' => $company->reviews,
                    'parsed_companies' => $company->parsed_companies,

                ])
            </div>
        </div>


    </div>
@stop