<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|App\ParserLog[] $logs
 */
?>


<table class="table">
    <thead>
        <tr>
            <th>
                время
            </th>
            <th>
                сообщение
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $log)
            <tr>
                <td>
                    {{$log->created_at}}
                </td>
                <td>
                    {{$log->message}}
                    <a href="{{$log->url}}">{{$log->url}}</a>
                    <span class="label label-info">OK</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
