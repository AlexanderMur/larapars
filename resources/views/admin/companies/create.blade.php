<?php
/**
 * @var \Illuminate\Support\Collection|\App\Models\ParsedCompany[] $parsed_companies
 *
 */

?>

@extends('admin.layout')


@section('content')
    <div id="page-wrapper">
        <h1 class="page-header">Создание новой компании</h1>

        <div class="col-lg-7">
            @include('admin.partials.messages')
            {{BootForm::vertical(['model' => null, 'store' => 'companies.store'])}}

            @foreach ($parsed_companies as $parsed_company)
                <input type="hidden" value="{{$parsed_company->id}}" name="parsed_companies_ids[]">
            @endforeach
            @isset($parsed_companies)
                {{
                    BootForm::select(
                        'title',
                        'название компании',
                        $parsed_companies->pluck('title','title')
                    )
                }}
            @endisset
            @isset($parsed_companies)
                {{
                    BootForm::select(
                        'site',
                        'Сайт',
                        $parsed_companies->pluck('site','site')
                    )
                }}
            @endisset
            @isset($parsed_companies)
                {{
                    BootForm::select(
                        'phone',
                        'Номер',
                        $parsed_companies->pluck('phone','phone')
                    )
                }}
            @endisset
            @isset($parsed_companies)
                {{
                    BootForm::select(
                        'address',
                        'Адрес',
                        $parsed_companies->pluck('address','address')
                    )
                }}
            @endisset
            @isset($parsed_companies)

            @endisset

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Send</button>
                <i class="fa fa-spinner fa-spin spinner"></i>
            </div>

            {{BootForm::close()}}

            @include('admin.partials.company-tabs',[
                'reviews' => $parsed_companies->pluck('reviews')->flatten(),
                'parsed_companies' => $parsed_companies,
            ])
        </div>
    </div>
@stop