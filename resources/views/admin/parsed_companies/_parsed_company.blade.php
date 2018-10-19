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

                <p>
                    Страница донора: <a
                        href="{{$parsed_company->donor_page}}"
                        target="_blank"
                    >{{str_limit($parsed_company->donor_page,60)}}</a>
                    <a
                        href="{{route('parsed_companies.detach',$parsed_company->id)}}"
                        onclick="return confirm('Отвязать донора?')"
                        class="text-danger"
                    >Отвязать донора</a>
                </p>
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
                            Донор: <a href="{{$parsed_company->donor->link}}" target="_blank">{{$parsed_company->donor->link}}</a>
                        </p>

                    </div>
                </div>


                @include('admin.reviews.partials._tabs',[
                    'company' => $parsed_company,
                    'reviews' => [],
                ])

            </div>
            <div class="tab-pane fade" id="history-{{$parsed_company->id}}">
                <div class="mt-5">
                    @include('admin.parsed_companies.partials._history')
                </div>
            </div>
        </div>
    </div>
</div>