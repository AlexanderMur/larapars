<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Company show</h1>
        {{$company->title}}

        <a href="{{route('companies.edit',$company)}}" type="button" class="btn btn-sm btn-success">
            <i class="fa fa-edit"></i>
        </a>
        <div class="row mt-5">
            <div class="col-md-5">
                <div id="tab" class="btn-group btn-group-justified" data-toggle="buttons">
                    <a href="#reviews" class="btn btn-primary active" data-toggle="tab">
                        <input type="radio"/>Отзывы
                    </a>
                    <a href="#donors" class="btn btn-primary" data-toggle="tab">
                        <input type="radio"/>Доноры
                    </a>
                </div>

                <div class="tab-content mt-3">

                    <div class="tab-pane fade active in" id="reviews">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#all_reviews">Все ({{$company->reviews()->count()}})</a>
                            </li>
                            <li>
                                <a href="#good_reviews">Положительные (0)</a>
                            </li>
                            <li>
                                <a href="#bad_reviews">Отрицательные (0)</a>
                            </li>
                            <li>
                                <a href="#deleted_reviews">Удалённые (0)</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="all_reviews">
                                @foreach ($company->reviews as $review)
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h4>{{$review->title}}</h4>
                                            <div>
                                                <i class="fa fa-user fa-fw"></i>
                                                {{$review->name}}
                                            </div>
                                            <div>
                                                <i class="fa fa-clock-o fa-fw"></i>
                                                {{$review->created_at}}
                                            </div>
                                            <div>
                                                {{$review->text}}
                                            </div>
                                            <div>
                                                <a href="{{$company->single_page_link}}" target="_blank">
                                                    Источник <i class="fa fa-external-link fa-fw"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="good_reviews">
                                <p>Tab 1.2</p>
                            </div>
                            <div class="tab-pane fade" id="bad_reviews">
                                <p>Tab 1.3</p>
                            </div>
                            <div class="tab-pane fade" id="deleted_reviews">
                                <p>Tab 1.4</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="donors">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#sub21">Tab 2.1</a>
                            </li>
                            <li><a href="#sub22">Tab 2.2</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="sub21">
                                <p>Tab 2.1</p>
                            </div>
                            <div class="tab-pane fade" id="sub22">
                                <p>Tab 2.2</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
@stop