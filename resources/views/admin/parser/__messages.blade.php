<table class="table logs__table">
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
                    @if ($log->type == 'bold')
                        <b>{{$log->message}}</b>
                    @elseif ($log->type == 'error')
                        <span class="text-danger"><b>{{$log->message}}</b></span>
                    @else
                        {{$log->message}}
                    @endif

                    <a href="{{$log->url}}" target="_blank">{{str_limit($log->url,55)}}</a>

                    @if ($log->type == 'bold')
                        <a href="{{route('logs.details',$log->parser_task_id)}}" class="ajax-load">Показать детали <span class="caret"></span></a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>