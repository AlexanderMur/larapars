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
                            <div class="form-group">
                                <label for="tries">Количество попыток</label>
                                <input
                                    class="form-control"
                                    id="tries"
                                    name="tries"
                                    type="number"
                                    value="{{setting()->tries}}"
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
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Смена пароля
                    </div>
                    <div class="panel-body">
                            <form method="POST" action="{{route('admin.settings.changePassword')}}">
                            @csrf
                            <div class="form-group">
                                <label for="old_password">Старый пароль</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="old_password"
                                    name="old_password"
                                    value="{{old('old_password')}}"
                                >
                            </div>
                            <div class="form-group">
                                <label for="new_password">Новый пароль</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password"
                                    name="new_password"
                                    value="{{old('new_password')}}"
                                >
                            </div>
                            <div class="form-group">
                                <label for="new_password_confirmation">Подтвердите пароль</label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password_confirmation"
                                    name="new_password_confirmation"
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
    </div>

@stop
