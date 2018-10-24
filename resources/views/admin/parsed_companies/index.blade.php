<?php
/**
 * @var \Yajra\DataTables\Html\Builder $html
 */
?>
@extends('admin.layout')

@section('title','Модерация')
@section('content')

    <div id="page-wrapper">
        <h1 class="page-header">Модерация</h1>
        @include('admin.partials.messages')
        @include('admin.partials.parser.controls')
        <form action="{{route('parsed_companies.bulk')}}" method="POST" class="table__form">
            @method('PUT')
            @csrf


            <div class="mb-2">
                <button class="btn btn-primary" name="action2" value="export">Экспортировать все в Excel</button>
            </div>
            <div class="form-inline table__controls">
                <div class="form-group">
                    <label for="title">Название</label>
                    <input type="text" class="form-control" name="title" id="title" value="{{request('title')}}">
                </div>
                <div class="form-group">
                    <label for="site">Сайт</label>
                    <input type="text" class="form-control" name="site" id="site" value="{{request('site')}}">
                </div>
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="text" class="form-control" name="phone" id="phone" value="{{request('phone')}}">
                </div>
            </div>
            {{$html->table(['class' => 'table table-bordered'])}}

            <div class="form-group">
                <select name="action" class="form-control select-medium bulk-select" title="Выберете действие">
                    <option value="">Действия</option>
                    <option value="new_company">Создать новую компанию</option>
                    <option value="group">Привязать к существующей</option>
                    <option value="export">Экспортировать</option>
                </select>
                <select
                    class="select-medium company-select show-if-group"
                    title="Выберите компанию"
                    name="company_id"
                ></select>
                <button class="btn btn-primary" type="submit">Применить</button>
            </div>
        </form>
    </div>
@stop


@push('scripts')
    {{$html->scripts()}}
@endpush