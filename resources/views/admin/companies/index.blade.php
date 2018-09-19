<?php
/** @var \App\Models\Company[] $companies */
?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Companies</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td>Телефон</td>
                    <td>Сайт</td>
                    <td>Название</td>
                    <td>Адрес</td>
                    <td>Донор</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($companies as $company) {

                ?>
                <tr>
                    <td>{{$company->phone}}</td>
                    <td>{{$company->site}}</td>
                    <td>{{$company->title}}</td>
                    <td>{{$company->address}}</td>
                    <td>{{$company->donor_id}}</td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
@stop