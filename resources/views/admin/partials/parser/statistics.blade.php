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