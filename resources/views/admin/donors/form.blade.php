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
        <h2>Archive page selector</h2>
        {{BootForm::text('loop_item')}}
        {{BootForm::text('loop_title')}}
        {{BootForm::text('loop_address')}}
        {{BootForm::text('loop_link')}}
        <h2>Single page selectors</h2>
        {{BootForm::text('single_site')}}
        {{BootForm::text('single_address')}}
        {{BootForm::text('single_tel')}}
        {{BootForm::text('single_title')}}
        {{BootForm::text('single_city')}}
        <h2>Replace unclosed tags</h2>
        {{BootForm::text('replace_search')}}
        {{BootForm::text('replace_to')}}
        <h2>Reviews selectors</h2>
        {{BootForm::text('reviews_all')}}
        {{BootForm::text('reviews_title')}}
        {{BootForm::text('reviews_text')}}
        {{BootForm::text('reviews_rating')}}
        {{BootForm::text('reviews_name')}}
        {{BootForm::text('reviews_id')}}
        {{BootForm::text('reviews_pagination')}}
        {{BootForm::text('reviews_ignore_text',null,null,['helpBlock'=>('Убрать "Читать полностью..." у отзыва')])}}
        <p class="help-block">Убрать "Читать полностью..." у отзыва</p>
        {{BootForm::text('archive_pagination',null,null,['helpBlock'=>('Пагинация или любые ссылки куда ведут на архив')])}}
        <p class="help-block">Пагинация или любые ссылки куда ведут на архив</p>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Send</button>
            <i class="fa fa-spinner fa-spin spinner"></i>
        </div>

        {{BootForm::close()}}
    </div>
@stop
