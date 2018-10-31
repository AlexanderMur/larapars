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
                <select class="form-control select-medium" name="donor_id" id="">
                    <option value="all">Все</option>
                    @foreach ($donors as $donor)
                        <option value="{{$donor->id}}">{{$donor->link}}</option>
                    @endforeach
                </select>
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
                    <p>
                        Ссылок на компании в очереди <span class="logs__c-pages-count"></span>
                    </p>
                    <p>
                        Ссылок на архив в очереди <span class="logs__a-pages-count"></span>
                    </p>
                    <p>
                        Ссылок ожидает овета <span class="logs__s-pages-count"></span>
                    </p>
                    <div class="parser__logs__inner">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>