<?php
/** @var \App\Models\Review $review */
?>
{{BootForm::vertical(['model' => $review, 'update' => 'reviews.update','class'=>'ajax-form'])}}

{{BootForm::text('title')}}
{{BootForm::text('name')}}
{{BootForm::textarea('text')}}
{{BootForm::checkbox('good')}}
{{BootForm::date('date')}}
{{BootForm::submit('send')}}

{{BootForm::close()}}