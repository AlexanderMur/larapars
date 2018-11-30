<?php
/** @var \App\Models\Donor[] $donors */

?>

@extends('admin.layout')

@section('title',$donor->link ?? 'Доноры')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{'Доноры'}}</h1>
        <div class="mb-3">
            <a href="{{route('donors.export')}}" class="btn btn-info">Экспорт</a>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exportModal">
                Импорт
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exportModalLabel">Импорт</h4>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('donors.import')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <input type="file" id="file" name="file">
                                </div>
                                <input type="submit" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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