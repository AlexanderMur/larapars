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
                <div class="form-group">
                    <label for="page">Ссылка</label>
                    <textarea id="page" name="page" class="form-control"></textarea>
                    <p class="help-block">Введите ссылку на компанию. Например: https://avtosalon-otzyv.ru/avtosalon-aleaavto/</p>
                </div>
                <div class="form-group">
                    <label for="donor_id">Донор</label>
                    <select id="donor_id" name="donor_id" class="form-control">
                        @foreach ($donors as $donor)
                            <option value="{{$donor->id}}">{{$donor->link}} {{$donor->title}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
        </div>
    </div>
@stop