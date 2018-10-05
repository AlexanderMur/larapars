<?php
/**
 * @var \App\Models\Review $review
 */
?>
<div class="row review">
    <div class="col-lg-8">
        @include('admin.reviews._review')
    </div>
    <div class="col-lg-4">
        <a href="javascript:" class="text-danger"><i class="fa fa-fw fa-2x fa-thumbs-down dislike" data-review-id="{{$review->id}}"></i></a>
        <a href="javascript:" class="text-success"><i class="fa fa-fw fa-2x fa-thumbs-up like" data-review-id="{{$review->id}}"></i></a>
    </div>
</div>