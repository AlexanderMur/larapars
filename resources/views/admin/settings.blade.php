<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')

@section('title','Настройки ')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Настройки</h1>
        @include('admin.partials.messages')
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Настройки парсера
                    </div>
                    <div class="panel-body">

                        <form method="POST" action="{{route('admin.settings.updateParser')}}">
                            @csrf
                            <div class="form-group">
                                <label for="time">Периодичность парсинга</label>
                                <input
                                    class="form-control"
                                    data-placement="left"
                                    data-toggle="tooltip"
                                    name="time"
                                    title="Сколько часов"
                                    type="number"
                                    value="{{setting()->time}}"
                                >
                            </div>
                            <div class="form-group">
                                <label for="proxies">Прокси</label>
                                <textarea
                                    class="form-control"
                                    id="proxies"
                                    name="proxies"
                                    rows="10"
                                >{{setting()->proxies}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="concurrency">Количество потоков</label>
                                <input
                                    class="form-control"
                                    id="concurrency"
                                    name="concurrency"
                                    type="number"
                                    value="{{setting()->concurrency}}"
                                >
                            </div>
                            <button class="btn btn-primary">
                                Submit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Очистить данные
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="{{route('admin.settings.restoreDefaults')}}">
                            @csrf
                            <input type="hidden" name="action" value="restoreDefaults">
                            <button class="btn btn-danger" onclick="return confirm('Уверены?')">Очистить данные
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
