<?php
/**
 * @var \App\Models\Donor[] $donors
 */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Тест</h1>
        <div class="col-lg-8">
            <form method="post">
                @csrf
                <p>Введите ссылки на компании. Разделять через новую строку. Например:<br>
                    https://avtosalon-otzyv.ru/avtosalon-aleaavto/<br>
                    https://otziv-avto.ru/avtosalon-armada-spb-otzyvy/
                </p>
                <p>
                    Доноры ищутся автоматически на основании хоста
                </p>
                <p>Доступные доноры:</p>
                <ul>
                    @foreach ($donors as $donor)
                        <li>{{$donor->link}} {{$donor->title}}</li>
                    @endforeach
                </ul>
                <div class="form-group">
                    <label for="page">Ссылки</label>
                    <textarea id="page" name="page" class="form-control">{{old('page')}}</textarea>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
        </div>
    </div>
@stop