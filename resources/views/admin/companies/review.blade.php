<?php
/**
 * @var \App\Models\Review $review
 * @var \App\Models\Company $company
 */
?>
<div class="panel panel-default">
    <div class="panel-body">

        <div class="row mb-3">
            @if ($review->good)
                <div class="col-lg-6">
                    <h3 class="my-0">{{$review->title}} <i class="fa fa-thumbs-up text-success "></i></h3>
                </div>
            @endif
            @if (!$review->good)
                <div class="col-lg-6">
                    <h3 class="my-0">{{$review->title}} <i class="fa fa-thumbs-down text-danger "></i></h3>
                </div>

            @endif
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
                @foreach ($review->donors as $donor)
                    <tr>
                        <td>{{$donor->title}}</td>
                        <td><a href="{{$donor->link}}">{{$donor->link}}</a></td>
                        <td><a href="{{$donor->pivot->site}}">{{$donor->pivot->site}}</a></td>
                        <td>{{$donor->pivot->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <div>
            <a class="btn btn-primary" href="{{route('reviews.edit',$review)}}"><i class="fa fa-edit"></i> Edit</a>
        </div>

    </div>
</div>