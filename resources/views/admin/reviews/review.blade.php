<?php
/**
 * @var \App\Models\Review $review
 */
?>
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="row mb-3">
                    <div class="col-lg-6">

                        <h3 class="my-0 d-inline">
                            {{$review->title}}

                        </h3>
                        @if ($review->good === true)
                            <i class="fa fa-thumbs-up text-success fa-fw fa-2x"></i>
                        @endif
                        @if ($review->good === false)
                            <i class="fa fa-thumbs-down text-danger fa-fw fa-2x"></i>
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

                {!! $review->text !!}

                <b><a href="{{route('companies.show',$review->company_id)}}">{{$review->donor->title}}</a></b>
                <br>
                <a href="{{$review->donor_link}}">Перейти к странице донора</a>
                <div>
                    <a class="btn btn-primary" href="{{route('reviews.edit',$review)}}"><i class="fa fa-edit"></i> Edit</a>
                </div>

            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <i class="fa fa-fw fa-2x fa-thumbs-down dislike" data-review-id="{{$review->id}}"></i>
        <i class="fa fa-fw fa-2x fa-thumbs-up like" data-review-id="{{$review->id}}"></i>
    </div>
</div>