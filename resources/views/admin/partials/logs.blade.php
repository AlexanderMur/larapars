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
                @if ($log->status == 'bold')
                    <td>
                        <b>{{$log->message}}</b>
                        <a href="{{$log->url}}">{{str_limit($log->url,55)}}</a>
                    </td>
                @else
                    <td>
                        {{$log->message}}
                        <a href="{{$log->url}}">{{str_limit($log->url,55)}}</a>
                        @if ($log->status == 'ok')
                            <span class="label label-info">OK</span>
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
