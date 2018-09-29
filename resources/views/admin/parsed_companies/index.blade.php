<?php
/**
 * @var \Yajra\DataTables\Html\Builder $html
 */
?>
@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Start</h1>
        <form action="{{route('parsed_companies.bulk')}}" method="POST">
            @method('PUT')
            @csrf
            {{$html->table(['class' => 'table table-bordered'])}}
            <div>
                <select name="action" class="form-control bulk-select" title="TITLE">
                    <option value="-1">Действия</option>
                    <option value="new_company">Создать новую компанию</option>
                    <option value="group">Привязать к существующей</option>
                </select>
                <button class="btn btn-primary" type="submit">Применить</button>
            </div>
        </form>
    </div>
@stop


@push('scripts')
    {{$html->scripts()}}
@endpush