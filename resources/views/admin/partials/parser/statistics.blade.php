<?php
/**
 * @var \App\Models\ParserTask $task
 */
?>

<div>
    @isset($task->created_at)
        <p>Последний раз парсер был запущен {{$task->created_at}}</p>
    @else
        <p>Парсер не запускался</p>
    @endisset
</div>
<div class="row">
    <div class="col-lg-6">
        <p>Всего новых компаний {{$statistics['parsed_companies_count']}}</p>
        <p>Всего новых отзывов {{$statistics['reviews_count']}}</p>
    </div>
    <div class="col-lg-6">
        <p>Найдено новых компаний {{$task->new_companies_count ?? 0 }}</p>
        <p>Найдено новых отзывов {{$task->new_reviews_count ?? 0 }}</p>
    </div>
</div>
<div>
    @if (isset($task) && $task->state === null)
        <p>
            <b>Задача в очереди</b>
        </p>
    @endif
    <p>
        Ссылок в очереди {{$task->not_sent_links ?? 0}}
    </p>
    <p>
        Ссылок ожидает ответа {{$task->concurrent_links ?? 0}}
    </p>
    <p>
        Всего запросов {{$task->http_logs_count ?? 0}}
    </p>
    @if (isset($task))
        <p>
            Неудачных запросов {{$task->getHttpErrorsCount() ?? 0}}
        </p>
        <p>
            Количество ошибок {{$task->getErrorsCount() ?? 0}}
        </p>
        <p>
            Страниц архивов {{$task->getArchivePagesCount() ?? 0}}
        </p>
    @endif
    <p>
        Дубликатов {{$task->details['duplicated_companies'] ?? 0}}
    </p>
</div>