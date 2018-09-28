<?php
/**
 * @var App\Models\Company $company
 */
?>
<div class="btn-group" role="group" aria-label="...">
    <a href="{{route('companies.show',$company)}}" type="button" class="btn btn-sm btn-info">
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{route('companies.edit',$company)}}" type="button" class="btn btn-sm btn-success model-edit">
        <i class="fa fa-edit"></i>
    </a>
</div>