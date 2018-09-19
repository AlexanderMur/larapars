<?php
/** @var \App\Models\Company[] $companies */
/** @var $tables */
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

        @isset($tables)
            @foreach ($tables as $key => $table)
                @if ($key == 0)
                    <h2>Сайт-Донор https://avtosalon-otzyv.ru/</h2>
                @endif
                @if ($key == 1)
                    <h2>Сайт-Донор https://otziv-avto.ru/msk/</h2>
                @endif
                @include('admin.partials.table',['table'=>$table])
            @endforeach
        @endisset
    </div>
@stop