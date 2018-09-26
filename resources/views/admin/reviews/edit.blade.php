<?php
/** @var \App\Models\Review $review */
?>

@extends('admin.layout')


@section('content')

    <div
            id="page-wrapper"
    >
        <h1 class="page-header">Review edit</h1>

        @include('admin.partials.messages')
        {{Form::model($review,['route' => ['reviews.update', $review->id],'class'=>'form-horizontal'])}}
        @method('put')
        <div class="form-group">
            {{ Form::label('title', 'title:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('title',null, ['class' => 'form-control', 'placeholder' => 'title']) }}
            </div>
        </div>

        <div class="form-group">
            {{ Form::label('name', 'name:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('name',null,['class' => 'form-control', 'placeholder' => 'name']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('text', 'text:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::text('text',null,['class' => 'form-control', 'placeholder' => 'text']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('good', 'good:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::checkbox('good', 1,null) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('deleted_at', 'deleted-at:', ['class' => 'col-lg-2 control-label']) }}
            <div class="col-lg-10">
                {{ Form::date('deleted_at', $review->deleted_at,['class' => 'form-control', 'placeholder' => 'deleted_at']) }}
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-10 col-lg-offset-2">
                {{Form::submit('Submit',['class'=>'btn btn-primary'])}}
                <a href="{{route('companies.show',$review->company->id)}}" class="btn btn-default">Back</a>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop