<table class="table logs__table">
    <thead>
        <tr>
            <th>
                Url
            </th>
            <th>
                Сообщение
            </th>
            <th>
                Статус
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($http_logs as $http_log)
            <tr>
                <td style="width:50%">
                    <a href="{{$http_log->url}}" target="_blank">{{$http_log->url}}</a>
                </td>
                <td style="width:50%">
                    <a href="{{$http_log->message}}" target="_blank">{{$http_log->message}}</a>
                </td>
                <td>
                    {{$http_log->status}}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>