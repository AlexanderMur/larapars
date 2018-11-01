<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|App\ParserLog[] $logs
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|App\Models\HttpLog[] $http_logs
 */
?>
<?php

/**
 * @var \App\Models\Review[]|\Illuminate\Support\Collection $reviews
 */
$id = uniqid();
?>
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

