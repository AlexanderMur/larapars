<?php
/**
 * @var \App\Models\Company $company
 * @var \App\Models\ParserTask[]|\Illuminate\Support\Collection $tasks
 *
 */
?>

@extends('admin.layout')

@section('title',$company->title)
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{$company->title}}</h1>
        @include('admin.partials.messages')
        <div>
            <div class="row mb-1">
                <div class="col-lg-6">
                    <form action="">
                        <a href="{{route('companies.edit',$company)}}" type="button" class="btn btn-sm btn-success">
                            <i class="fa fa-edit"></i>
                            редактировать
                        </a>
                        @foreach ($company->parsed_companies as $parsed_company)
                            <input type="hidden" name="pages[]" value="{{$parsed_company->donor_page}}">
                        @endforeach
                        <button class="btn btn-primary parser__start">Парсить</button>
                        <button class="btn btn-primary parser__stop">Остановить</button>
                    </form>
                    <div class="progress">
                        <div class="parser__progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="200" style="width: 0;">

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-group" role="tablist">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="parser__logs__heading">
                        <h4 class="panel-title">
                            <a
                                href="#parser__logs__collapse"
                                class="collapsed"
                                role="button"
                                data-toggle="collapse"
                            >Показать логи</a>
                        </h4>
                    </div>
                    <div
                        class="panel-collapse collapse parser__logs__collapse"
                        role="tabpanel"
                        id="parser__logs__collapse"
                    >
                        <div class="panel-body parser__logs__inner" data-company_id="{{$company->id}}">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#messages"  data-toggle="tab">Сообщения</a>
                                </li>
                                <li>
                                    <a href="#http"  data-toggle="tab">HTTP история</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade active in" id="messages">

                                </div>
                                <div class="tab-pane fade" id="http">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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