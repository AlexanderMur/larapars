<?php
/**
 * @var \App\Models\ParserTask $task
 */
?>



<table class="table">
    <thead>
        <tr>
            <th>URL Донора</th>
            <th>Найдено новых компаний</th>
            <th>Обновлено компаний</th>
            <th>Новых отзывов</th>
            <th>Удалено отзывов</th>
            <th>Возвращено отзывов</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($task->donors2 as $donor)
            <tr>
                <td><a href="{{$donor->link}}" target="_blank">{{$donor->link}}</a></td>
                <td>{{$donor->new_companies_count}}</td>
                <td>{{$donor->updated_companies_count}}</td>
                <td>{{$donor->new_reviews_count ?? 0}}</td>
                <td>{{$donor->deleted_reviews_count}}</td>
                <td>{{$donor->restored_reviews_count}}</td>
            </tr>
        @endforeach
    </tbody>
</table>


