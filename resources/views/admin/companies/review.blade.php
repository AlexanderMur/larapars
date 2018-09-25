<?php
/**
 * @var \App\Models\Review $review
 * @var \App\Models\Company $company
 */
?>
<div class="panel panel-default">
    <div class="panel-body">

        <div class="row mb-3">
            <div class="col-lg-6">

                <h3 class="my-0 d-inline">
                    {{$review->title}}

                </h3>
                @if ($review->good)
                    <i class="fa fa-thumbs-up text-success fa-fw fa-2x"></i>
                @endif
                @if (!$review->good)
                    <i class="fa fa-thumbs-down text-success fa-fw fa-2x"></i>
                @endif
                @isset($review->group->reviews)
                    @if ($review->group->reviews->count() >= 2)
                        @include('admin.companies.dublicates',[
                            'group' => $review->group,
                        ])
                    @endif
                @endisset
            </div>
            <div class="col-lg-6 text-right">
                <i class="fa fa-user fa-fw"></i>
                {{$review->name}}
                /
                <i class="fa fa-clock-o fa-fw"></i>
                <time>{{$review->created_at}}</time>
            </div>
        </div>

        {!! nl2p($review->text) !!}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Донор (название)</th>
                    <th>Донор (ссылка)</th>
                    <th>Донор (ссылка на страницу)</th>
                    <th>Дата парсинга</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$review->donor->title}}</td>
                    <td><a href="{{$review->donor_link}}">{{$review->donor_link}}</a></td>
                    <td><a href="{{$review->donor->site}}">{{$review->donor->site}}</a></td>
                    <td>{{$review->donor->created_at}}</td>
                </tr>
            </tbody>

        </table>


        <div>
            <a class="btn btn-primary" href="{{route('reviews.edit',$review)}}"><i class="fa fa-edit"></i> Edit</a>
        </div>

    </div>
</div>