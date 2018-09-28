<?php
/** @var \App\Models\Review $review */
?>

@include('admin.partials.messages')
{{BootForm::vertical(['model' => $review, 'update' => 'reviews.update','class'=>'ajax-form'])}}

{{BootForm::text('title')}}
{{BootForm::text('name')}}
{{BootForm::textarea('text')}}
{{BootForm::checkbox('good')}}
{{BootForm::date('date')}}

<div class="form-group">
    <button type="submit" class="btn btn-primary">Send</button>
    <i class="fa fa-spinner fa-spin spinner"></i>
</div>

{{BootForm::close()}}