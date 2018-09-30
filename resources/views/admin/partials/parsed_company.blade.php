<?php
/**
 * @var \App\Models\ParsedCompany $parsed_company
 */
?>
<div class="panel panel-default">
    <div class="panel-body">
        <h2>{{$parsed_company->donor->title}}</h2>

        <div>
            <b>{{$parsed_company->title}}</b>
        </div>
        <div>
            <a href="{{$parsed_company->donor_page}}">{{$parsed_company->donor_page}}</a>
        </div>
    </div>
</div>