<?php
/**
 * @var \App\Models\ParsedCompany $parsed_company
 */
?>
<a href="#" class="parsed-company__update btn btn-info" data-id="{{$parsed_company->id}}">
    Обновить
</a>
@forelse ($parsed_company->history as $record)
    <p>
        <b>{{$record->created_at}}</b><br>
        Изменено поле "{{__("company.$record->field")}}" – было "{{$record->old_value}}" стало
        "{{$record->new_value}}"
    </p>
@empty
    <p>
        нет изменений
    </p>
@endforelse
<p>
    Дата парсинга: {{$parsed_company->created_at}}
</p>