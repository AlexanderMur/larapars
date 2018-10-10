<?php
/**
 * @var \App\Models\Company[] $companies
 * @var $tables
 * @var \Illuminate\Pagination\LengthAwarePaginator $logs
 * @var \Yajra\DataTables\Html\Builder $html
 */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Логи</h1>
        {{$html->table()}}
    </div>
@stop

@push('scripts')
    {{$html->scripts()}}
@endpush