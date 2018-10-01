<?php
/** @var \App\Models\Company $company */
?>

@include('admin.partials.messages')
{{BootForm::vertical(['model' => $company, 'update' => 'companies.update','class'=>'ajax-form'])}}

{{BootForm::text('title')}}
{{BootForm::text('site')}}
{{BootForm::text('address')}}

<div class="form-group">
    <button type="submit" class="btn btn-primary">Send</button>
    <i class="fa fa-spinner fa-spin spinner"></i>
</div>

{{BootForm::close()}}