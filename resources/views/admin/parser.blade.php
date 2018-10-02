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
        <h1 class="page-header">Start</h1>
        <form action="{{route('pars.parse')}}" method="post" class="mb-2">
            @csrf
            <label>
                Сколько спарсить у каждого донора
                <input type="text" name="how_many">
            </label>
            <button type="submit" class="btn btn-danger">Send</button>
        </form>
        <div class="logs" data-parser_ids="{{$logs->pluck('parser_id','parser_id')->implode(',')}}">
            @include('admin.partials.logs',[
                'logs' => $logs,
            ])
        </div>
        {{$logs->render()}}
    </div>
@stop