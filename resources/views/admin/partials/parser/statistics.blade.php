<div>
    <p>Последний раз парсер был запущен {{$statistics['last_parse_date']}}</p>
</div>
<div class="row">
    <div class="col-lg-6">
        <p>Всего новых компаний {{$statistics['parsed_companies_count']}}</p>
        <p>Всего новых отзывов {{$statistics['reviews_count']}}</p>
    </div>
    <div class="col-lg-6">
        <p>Найдено новых компаний {{$statistics['last_parse_counts']->new_parsed_companies_count ?? 0 }}</p>
        <p>Найдено новых отзывов {{$statistics['last_parse_counts']->new_reviews_count ?? 0 }}</p>
    </div>
</div>