@foreach ($reviews as $review)
    @include('admin.reviews.review',[
        'review' => $review,
    ])
@endforeach