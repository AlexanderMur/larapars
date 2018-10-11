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
        {{$html->table(['class' => 'table table-bordered'])}}
    </div>
@stop

@push('scripts')
    {{$html->scripts()}}
@endpush