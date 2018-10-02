<?php
/** @var \Illuminate\Support\ViewErrorBag $errors */
?>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('success'))
    <div class="alert alert-success">
        <p>{{session('success')}}</p>
    </div>
@endif

@if (session('companies_grouped'))
    <div class="alert alert-success">
        <p>Компании привязаны</p>
        <p>
            <a href="{{route('companies.show',session('companies_grouped'))}}" target="_blank">
                <i class="fa fa-long-arrow-left fa-fw"></i>
                Перейти в компанию
            </a>
        </p>
    </div>
@endif