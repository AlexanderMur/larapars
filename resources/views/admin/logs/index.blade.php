<?php
/**
 * @var \App\Models\Company[] $companies
 * @var $tables
 * @var \Illuminate\Pagination\LengthAwarePaginator $logs
 */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Логи</h1>
        <div class="" data-parser_ids="{{$logs->pluck('parser_id','parser_id')->implode(',')}}">
            @include('admin.partials.logs',[
                'logs' => $logs,
            ])
        </div>
        {{$logs->render()}}
    </div>
@stop