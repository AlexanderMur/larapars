@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Start</h1>
        {{$html->table()}}
        @push('scripts')
            {{$html->scripts()}}
        @endpush
    </div>
@stop