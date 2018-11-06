<?php
/** @var \App\Models\Donor[] $donors */

?>

@extends('admin.layout')

@section('title',$donor->link ?? 'Доноры')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{'Доноры'}}</h1>

        <form method="post" action="{{route('donors.bulk')}}">
            @csrf
            {{$html->table()}}

            <div class="form-group">
                <select name="action" class="form-control select-medium bulk-select" title="Выберете действие">
                    <option value="">Действия</option>
                    <option value="parse">Парсить компании</option>
                </select>
                <button class="btn btn-primary" type="submit">Применить</button>
            </div>
        </form>

    </div>
@stop

@push('scripts')
    {{$html->scripts()}}
@endpush