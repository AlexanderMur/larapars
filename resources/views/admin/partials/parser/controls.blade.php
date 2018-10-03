<div>
    <div class="row">
        <div class="col-lg-6 statistics">

        </div>
        <div class="col-lg-6">
            <select class="form-control select-medium" name="select_parser" id="">
                <option value="1">Все</option>
            </select>
            <button class="btn btn-primary start-parsing">Парсить</button>
        </div>
    </div>
    <div class="mb-5">
        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo">Показать логи</button>
    </div>
    <div id="demo" class="logs collapse" data-parser_ids="{{$logs->pluck('parser_id','parser_id')->implode(',')}}">
        @include('admin.partials.logs',[
            'logs' => $logs,
        ])
    </div>
</div>