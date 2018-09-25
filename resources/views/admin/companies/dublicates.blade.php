<?php
/** @var \App\Models\Group $group */
$count = $group->reviews->count();
$nice_title = $count >= 5 ? $count . ' дублей' : $count . ' дубля';
?>
<a href="javascript:" data-toggle="modal" data-target="#myModal{{$group->id}}" class="text-dark">
    <i class="fa fa-copy fa-fw fa-2x"
       data-toggle="tooltip"
       title="{{$nice_title}}"
    ></i></a>

<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="myModal{{$group->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Дубликаты</h4>
            </div>
            <div class="modal-body">
                @foreach ($group->reviews as $review)
                    <div>
                        <a href="{{$review->donor_link}}">{{$review->donor_link}}</a>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>