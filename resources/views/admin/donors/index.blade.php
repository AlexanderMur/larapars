<?php
/** @var \App\Models\Donor[] $donors */

?>

@extends('admin.layout')

@section('title',$donor->link ?? 'Доноры')
@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{'Доноры'}}</h1>

        @foreach ($donors as $donor)
            <h2><a href="{{route('donors.show',$donor->id)}}">{{$donor->link}}</a></h2>
        @endforeach
    </div>
@stop