<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')

@section('title','Просмотр компанию')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Просмотр компании</h1>
        @include('admin.partials.messages')
        <h2>
            <a href="{{route('companies.edit',$company)}}" type="button" class="btn btn-sm btn-success">
                <i class="fa fa-edit"></i>
            </a>
            {{$company->title}}
        </h2>
        <div>
            <div class="row mb-1">

                <div class="col-lg-6">
                    <form action="">
                        @foreach ($company->parsed_companies as $parsed_company)
                            <input type="hidden" name="pages[]" value="{{$parsed_company->donor_page}}">
                        @endforeach
                        <button class="btn btn-primary start-parsing">Парсить</button>
                    </form>
                </div>
            </div>
            <div class="mb-5">
                <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo">Показать логи</button>
            </div>
            <div id="demo" class="logs collapse" data-parser_ids="{{$logs->pluck('parser_id','parser_id')->implode(',')}}">
                @include('admin.partials.logs',[
                    'logs' => $logs,
                ])
            </div>
        </div>
        <p>
            Телефон: {{$company->phone}}
        </p>
        <p>
            Сайт: <a href="{{$company->site}}" target="_blank">{{$company->site}}</a>
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