<?php
/** @var \App\Models\Company $company */
?>

@extends('admin.layout')

@section('title','Настройки ')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Настройки</h1>
        @include('admin.partials.messages')
        <form action="" method="post">
            @csrf
            <button class="btn btn-danger" onclick="return confirm('Уверены?')">Очистить Компании</button>
        </form>
    </div>
@stop