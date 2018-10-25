<?php
/** @var \App\Models\Donor[] $donors */

?>

@extends('admin.layout')

@section('title',$donor->link ?? 'Доноры')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{'Доноры'}}</h1>

        {{$html->table()}}
    </div>
@stop

@push('scripts')
    {{$html->scripts()}}
@endpush