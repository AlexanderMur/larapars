<?php
/**
 * @var \App\Models\Company|\App\Models\ParsedCompany $company
 * @var \Illuminate\Support\Collection|\App\Models\Review[] $reviews
 * @var \Illuminate\Support\Collection|\App\Models\ParsedCompany[] $parsed_companies
 */
?>

<div>
    <div id="tab" class="btn-group btn-group-justified" data-toggle="buttons">
        <a href="#reviews" class="btn btn-primary active" data-toggle="tab">
            <input type="radio"/>Отзывы ({{$company->reviews_count ?? 0}})
        </a>
        <a href="#parsed_companies" class="btn btn-primary" data-toggle="tab">
            <input type="radio"/>Доноры ({{$parsed_companies->count()}})
        </a>
    </div>

    <div class="tab-content mt-3">

        <div class="tab-pane fade active in" id="reviews">
            @include('admin.reviews._tabs')
        </div>
        <div class="tab-pane fade" id="parsed_companies">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#sub21">Доноры</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="sub21">

                    @foreach ($parsed_companies as $parsed_company)
                        @include('admin.parsed_companies._parsed_company')
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>