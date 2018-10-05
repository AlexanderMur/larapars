<?php
/**
 * @var \App\Models\ParsedCompany $parsed_company
 */
?>
<div class="panel panel-default">
    <div class="panel-body">
        <h2>{{$parsed_company->title}}</h2>

        <p>
            Название донора: <b>{{$parsed_company->donor->title}}</b>
        </p>
        <p>
            <a href="{{$parsed_company->donor_page}}">Посетить страницу донора</a>
        </p>
    </div>
</div>