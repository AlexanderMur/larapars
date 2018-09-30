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
                <div id="tab" class="btn-group btn-group-justified" data-toggle="buttons">
                    <a href="#reviews" class="btn btn-primary active" data-toggle="tab">
                        <input type="radio"/>Отзывы ({{$company->reviews()->count()}})
                    </a>
                    <a href="#parsed_companies" class="btn btn-primary" data-toggle="tab">
                        <input type="radio"/>Доноры ({{$company->parsed_companies()->count()}})
                    </a>
                </div>

                <div class="tab-content mt-3">

                    <div class="tab-pane fade active in" id="reviews">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#all_reviews">Все ({{$company->reviews->where('deleted_at','===',null)->count()}})</a>
                            </li>
                            <li>
                                <a href="#good_reviews">Положительные ({{$company->reviews->where('deleted_at','===',null)->where('good','===',1)->count()}})</a>
                            </li>
                            <li>
                                <a href="#bad_reviews">Отрицательные ({{$company->reviews->where('deleted_at','===',null)->where('good','===', 0)->count() }})</a>
                            </li>
                            <li>
                                <a href="#unrated_reviews">Не оцененные ({{$company->reviews->where('deleted_at','===',null)->where('good','===',null)->count()}})</a>
                            </li>
                            <li>
                                <a href="#deleted_reviews">Удалённые ({{$company->reviews->where('deleted_at','!==',null)->where('trashed_at','===',null)->count()}})</a>
                            </li>
                            <li>
                                <a href="#trashed_reviews">В корзине ({{$company->reviews->where('deleted_at','!==',null)->where('trashed_at','!==',null)->count()}})</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="all_reviews">
                                @foreach ($company->reviews->where('deleted_at','===',null) as $review)

                                    @include('admin.companies.review',[
                                        'review' => $review,
                                        'company' => $company,
                                    ])
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="good_reviews">

                                @foreach ($company->reviews->where('deleted_at','===',null)->where('good','===',1) as $review)
                                    @include('admin.companies.review',[
                                        'review' => $review,
                                        'company' => $company,
                                    ])
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="bad_reviews">

                                @foreach ($company->reviews->where('deleted_at','===',null)->where('good','===',0) as $review)
                                    @include('admin.companies.review',[
                                        'review' => $review,
                                        'company' => $company,
                                    ])
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="unrated_reviews">

                                @foreach ($company->reviews->where('deleted_at','===',null)->where('good','===',null) as $review)
                                    @include('admin.companies.review',[
                                        'review' => $review,
                                        'company' => $company,
                                    ])
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="deleted_reviews">

                                @foreach ($company->reviews->where('deleted_at','!==',null)->where('trashed_at','===',null) as $review)
                                    @include('admin.companies.review',[
                                        'review' => $review,
                                        'company' => $company,
                                    ])
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="trashed_reviews">
                                @foreach ($company->reviews->where('trashed_at','!==',null) as $review)
                                    @include('admin.companies.review',[
                                        'review' => $review,
                                        'company' => $company,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="parsed_companies">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#sub21">Доноры</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="sub21">

                                @foreach ($company->parsed_companies as $parsed_company)
                                    @include('admin.partials.parsed_company',[
                                        'parsed_company' => $parsed_company,
                                    ])
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
@stop