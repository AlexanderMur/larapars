<?php
/**
 * @var \App\Models\ParsedCompany $parsed_company
 */
?>

<div class="panel panel-default">
    <div class="panel-body">
        <ul class="nav nav-tabs mb-3">
            <li class="active">
                <a href="#info-{{$parsed_company->id}}">Инфо</a>
            </li>
            <li>
                <a href="#history-{{$parsed_company->id}}">История изменений</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="info-{{$parsed_company->id}}">
                <h2>{{$parsed_company->title}}</h2>

                <a
                    role="button"
                    data-toggle="collapse"
                    href="#donor__info-{{$parsed_company->id}}"
                    aria-expanded="false"
                    aria-controls="donor__info-{{$parsed_company->id}}"
                >
                    Информация
                    <span class="caret"></span>
                </a>
                <div class="collapse" id="donor__info-{{$parsed_company->id}}">
                    <div class="well">
                        <p>
                            Телефон: {{$parsed_company->phone}}
                        </p>
                        <p>
                            Сайт: <a href="{{$parsed_company->site}}" target="_blank">{{$parsed_company->site}}</a>
                        </p>
                        <p>
                            Название: {{$parsed_company->title}}
                        </p>
                        <p>
                            Город: {{$parsed_company->city}}
                        </p>
                        <p>
                            Адрес: {{$parsed_company->address}}
                        </p>
                        <p>
                            Дата создания в бд: {{$parsed_company->created_at}}
                        </p>
                        <p>
                            Дата обновления в бд: {{$parsed_company->updated_at}}
                        </p>
                        <p>
                            Название донора: <b>{{$parsed_company->donor->title}}</b>
                        </p>
                        <p>
                            Страница донора: <a
                                href="{{$parsed_company->donor_page}}"
                                target="_blank"
                            >{{str_limit($parsed_company->donor_page,60)}}</a>
                        </p>
                    </div>
                </div>

                <p>
                    <a
                        role="button"
                        data-toggle="collapse"
                        href="#donor__reviews-{{$parsed_company->id}}"
                        aria-expanded="false"
                        aria-controls="donor__reviews-{{$parsed_company->id}}"
                    >
                        Показать отзывы
                        <span class="caret"></span>
                    </a>
                </p>
                <div class="collapse" id="donor__reviews-{{$parsed_company->id}}">
                    @include('admin.reviews._tabs',[
                        'reviews' => $parsed_company->reviews,
                        'collapse' => true,
                    ])
                </div>

            </div>
            <div class="tab-pane fade" id="history-{{$parsed_company->id}}">
                <div class="mt-5">
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
                </div>
            </div>
        </div>
    </div>
</div>