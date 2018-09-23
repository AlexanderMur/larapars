<?php
/**
 * @var \App\Models\Review $review
 * @var \App\Models\Company $company
 */
?>
<div class="panel panel-default">
    <div class="panel-body">

        @if ($review->good)
            <h4>{{$review->title}} <i class="fa fa-thumbs-up"></i></h4>
        @endif
        @if (!$review->good)
            <h4>{{$review->title}} <i class="fa fa-thumbs-down"></i></h4>

        @endif
        <div>
            <i class="fa fa-user fa-fw"></i>
            {{$review->name}}
        </div>
        <div>
            <i class="fa fa-clock-o fa-fw"></i>
            {{$review->created_at}}
        </div>
        <div>
            {{$review->text}}
        </div>

        <div>
            <a href="{{$company->single_page_link}}" target="_blank">
                Страница у донора <i class="fa fa-external-link fa-fw"></i>
            </a>
        </div>

        <div>
            <a href="{{route('reviews.edit',$review)}}"><i class="fa fa-edit"></i> Edit</a>
        </div>
    </div>
</div>