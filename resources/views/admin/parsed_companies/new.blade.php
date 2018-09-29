<?php
/**
 * @var \Yajra\DataTables\Html\Builder $html
 */
?>
@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">{{$action}}</h1>
        <?php echo implode(', ',$ids) ?>
    </div>
@stop

