<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')


@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Company edit</h1>

        @include('admin.partials.messages')

        {{Form::model($company,['route' => ['companies.update', $company->id],'class'=>'form-horizontal'])}}
        @method('put')
        <div class="form-group">
            {{ Form::label('title', 'title:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('title', $value = null, ['class' => 'form-control', 'placeholder' => 'title']) }}
            </div>
        </div>

        <div class="form-group">
            {{ Form::label('site', 'site:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('site', $value = null,['class' => 'form-control', 'placeholder' => 'title']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('single_page_link', 'single_page_link:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('single_page_link', $value = null,['class' => 'form-control', 'placeholder' => 'title']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('address', 'address:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('address', $value = null,['class' => 'form-control', 'placeholder' => 'title']) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
                <a href="{{route('companies.show',$company)}}" class="btn btn-default">Cancel</a>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop