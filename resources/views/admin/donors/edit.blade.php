<?php
/** @var \App\Models\Donor $donor */
?>

@extends('admin.layout')


@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Company edit</h1>

        @include('admin.partials.messages')

        {{Form::model($donor,['route' => ['donors.update', $donor],'class'=>'form-horizontal'])}}
        @method('put')
        <div class="form-group">
            {{ Form::label('title', 'title:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('title', $value = null, ['class' => 'form-control', 'placeholder' => 'title']) }}
            </div>
        </div>

        <div class="form-group">
            {{ Form::label('link', 'link:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('link', $value = null,['class' => 'form-control', 'placeholder' => 'link']) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
                {{--<a href="{{route('companies.show',$donor)}}" class="btn btn-default">Back</a>--}}
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop