<?php
/**
 * @var \Yajra\DataTables\Html\Builder $html
 */
?>
@extends('admin.layout')

@section('title','База компаний')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">База компаний</h1>
        <form action="{{route('companies.bulk')}}">
            <div class="mb-2">
                <button class="btn btn-primary" name="action2" value="export">Экспортировать все в Excel</button>
            </div>
            {{$html->table(['class' => 'table table-bordered'])}}
            <div class="form-group">
                <select name="action" class="form-control select-medium bulk-select" title="Выберете действие">
                    <option value="">Действия</option>
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