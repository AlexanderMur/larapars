<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[] $reviews */
/** @var \Yajra\DataTables\Html\Builder $html */

?>

@extends('admin.reviews.layout')


@section('content2')
    {{$html->table()}}
@stop


@push('scripts')
    {{$html->scripts()}}
@endpush