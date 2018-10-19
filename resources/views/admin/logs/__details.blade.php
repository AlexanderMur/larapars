<?php
/**
 * @var \App\ParserLog $log
 */
?>



<table class="table">
    <thead>
        <tr>
            <th>Название компании</th>
            <th>Найдено новых компаний</th>
            <th>Обновлено компаний</th>
            <th>Новых отзывов</th>
            <th>Удалено отзывов</th>
            <th>Возвращено отзывов</th>
        </tr>
    </thead>
    <tbody>
        @foreach (json_decode($log->details)->donor_stats as $stat)
            <tr>
                <td>{{$stat->title}}</td>
                <td>{{$stat->new_parsed_companies_count}}</td>
                <td>{{$stat->updated_companies_count}}</td>
                <td>{{$stat->new_reviews_count}}</td>
                <td>{{$stat->deleted_reviews_count}}</td>
                <td>{{$stat->restored_reviews_count}}</td>
            </tr>
        @endforeach
    </tbody>
</table>


