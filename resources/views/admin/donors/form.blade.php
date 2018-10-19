<?php
/** @var \App\Models\Donor $donor */
$donor = $donor ?? null;
?>

@extends('admin.layout')

@section('title',$donor->link ?? 'Создать донора')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{$donor->link ?? 'Создать донора'}}</h1>

        {{BootForm::vertical(['model' => $donor, 'store' => 'donors.store','update' => 'donors.update'])}}
        {{BootForm::text('link')}}
        {{BootForm::text('title')}}
        {{BootForm::checkbox('mass_parsing','Парсить все компании')}}
        {{BootForm::text('loop_item')}}
        {{BootForm::text('loop_title')}}
        {{BootForm::text('loop_address')}}
        {{BootForm::text('loop_link')}}
        {{BootForm::text('single_site')}}
        {{BootForm::text('single_address')}}
        {{BootForm::text('single_tel')}}
        {{BootForm::text('single_title')}}
        {{BootForm::text('single_city')}}
        {{BootForm::text('replace_search')}}
        {{BootForm::text('replace_to')}}
        {{BootForm::text('reviews_all')}}
        {{BootForm::text('reviews_title')}}
        {{BootForm::text('reviews_text')}}
        {{BootForm::text('reviews_rating')}}
        {{BootForm::text('reviews_name')}}
        {{BootForm::text('reviews_id')}}
        {{BootForm::text('reviews_pagination')}}
        {{BootForm::text('reviews_ignore_text')}}
        {{BootForm::text('archive_pagination')}}

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Send</button>
            <i class="fa fa-spinner fa-spin spinner"></i>
        </div>

        {{BootForm::close()}}
    </div>
@stop
