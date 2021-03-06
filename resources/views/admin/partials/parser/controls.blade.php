<?php
/**
 * @var \App\Models\Donor $donor
 */
?>

<div>
    <div class="row">
        <div class="col-lg-6 statistics">

        </div>
        <div class="col-lg-6">
            <form action="">
                <select class="form-control select-medium js-basic-multiple" name="donor_ids[]" id="" multiple="multiple">
                    @foreach ($donors as $donor)
                        <option value="{{$donor->id}}">{{$donor->title}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="job_id" value="">
                <button class="btn btn-primary parser__start">Парсить</button>
                <button class="btn btn-primary parser__stop">Остановить</button>
                <button class="btn btn-primary parser__resume">Возобновить</button>
                <button class="btn btn-primary parser__stopping" disabled="true">Остановка...</button>
            </form>
            <br>
            <div class="progress">
                <div class="parser__progress progress-bar progress-bar-success" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="200" style="width: 0;">

                </div>
            </div>
        </div>
    </div>


    <div class="panel-group" role="tablist">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="parser__logs__heading">
                <h4 class="panel-title">
                    <a
                        href="#parser__logs__collapse"
                        class="collapsed"
                        role="button"
                        data-toggle="collapse"
                    >Показать логи</a>
                </h4>
            </div>
            <div class="panel-collapse collapse parser__logs__collapse" role="tabpanel" id="parser__logs__collapse">
                <div class="panel-body ">
                    <div class="parser__logs__inner">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#messages"  data-toggle="tab">Сообщения</a>
                            </li>
                            <li>
                                <a href="#http"  data-toggle="tab">HTTP история</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="messages">

                            </div>
                            <div class="tab-pane fade" id="http">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>