<?php
/** @var Illuminate\Support\Collection|\App\Models\Review[] $reviews */
/** @var \Yajra\DataTables\Html\Builder $html */

?>

@extends('admin.reviews.layout')


@section('content2')

    <form action="{{route('reviews.updateMany')}}" method="post">
        @method('PUT')
        @csrf
        {{$html->table()}}

        <div>
            <select name="action" class="form-control select-medium" title="TITLE">
                <option value="-1">Действия</option>
                <option value="group">Группировать</option>
            </select>
            <button class="btn btn-primary" type="submit">Применить</button>
        </div>
    </form>
@stop


@push('scripts')
    {{$html->scripts()}}
@endpush