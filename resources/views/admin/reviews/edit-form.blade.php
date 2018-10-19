<?php
/** @var \App\Models\Review $review */
?>

@include('admin.partials.messages')
{{BootForm::vertical(['model' => $review, 'update' => 'reviews.update','class'=>'ajax-form'])}}

{{BootForm::text('title','навзание')}}
{{BootForm::text('name','имя')}}
{{BootForm::textarea('text','текст')}}
{{BootForm::select('good','Оценка',[''=>'не оценен','1'=>'Положительный','0'=>'отрицательный'])}}
<div class="form-group ">
    <label for="deleted_at" class="control-label">Удалён с донора</label>
    <div>
        <input id="deleted_at" name="deleted_at" type="datetime-local" value="{{$review->deleted_at ? $review->deleted_at->format('Y-m-d\TH:i') : ''}}" class="form-control">
    </div>
</div>
<div class="form-group ">
    <label for="trashed_at" class="control-label">В корзине</label>
    <div>
        <input id="trashed_at" name="trashed_at" type="dateTime-local" value="{{$review->trashed_at ? $review->trashed_at->format('Y-m-d\TH:i') : ''}}" class="form-control">
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary">Send</button>
    <i class="fa fa-spinner fa-spin spinner"></i>
</div>

{{BootForm::close()}}