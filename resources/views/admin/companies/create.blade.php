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

        <div class="col-lg-12">
            @include('admin.partials.messages')
            {{BootForm::vertical(['model' => null, 'store' => 'companies.store'])}}

            @foreach ($parsed_companies as $parsed_company)
                <input type="hidden" value="{{$parsed_company->id}}" name="parsed_companies_ids[]">
            @endforeach

            <div class="row final_data_choice_arrow">

                <div class="data_choice--col">
                    {{
                        BootForm::select(
                            'company_titles',
                            'Название',
                            $parsed_companies->pluck('title','title'),
                            null,
                            ['class'=>'parsed_data']
                        )
                    }}
                </div>
                <div class="data_choice--arrow">
                    <button class="btn btn-default border-0 text-primary  data_choice--arrow__click">
                        <i class="fa fa-arrow-right"></i></button>
                </div>
                <div class="data_choice--col">
                    {{BootForm::text('company_title','Название',null,['class'=>'final_data'])}}
                </div>
            </div>


            <div class="row final_data_choice_arrow">

                <div class="data_choice--col">
                    {{
                        BootForm::select(
                            'company_sites',
                            'Сайт',
                            $parsed_companies->pluck('site','site'),
                            null,
                            ['class'=>'parsed_data']
                        )
                    }}
                </div>
                <div class="data_choice--arrow">
                    <button class="btn btn-default border-0 text-primary data_choice--arrow__click"><i class="fa fa-arrow-right"></i>
                    </button>
                </div>
                <div class="data_choice--col">
                    {{BootForm::text('company_site','Сайт',null,['class'=>'final_data'])}}
                </div>
            </div>


            <div class="row final_data_choice_arrow">

                <div class="data_choice--col">
                    {{
                        BootForm::select(
                            'company_phones',
                            'Телефон',
                            $parsed_companies->pluck('phone','phone'),
                            null,
                            ['class'=>'parsed_data']
                        )
                    }}
                </div>
                <div class="data_choice--arrow">
                    <button class="btn btn-default border-0 text-primary  data_choice--arrow__click">
                        <i class="fa fa-arrow-right"></i></button>
                </div>
                <div class="data_choice--col">
                    {{BootForm::text('company_phone','Телефон',null,['class'=>'final_data'])}}
                </div>
            </div>


            <div class="row final_data_choice_arrow">

                <div class="data_choice--col">
                    {{
                        BootForm::select(
                            'company_addresses',
                            'Адрес',
                            $parsed_companies->pluck('address','address'),
                            null,
                            ['class'=>'parsed_data']
                        )
                    }}
                </div>
                <div class="data_choice--arrow">
                    <button class="btn btn-default border-0 text-primary  data_choice--arrow__click">
                        <i class="fa fa-arrow-right"></i></button>
                </div>
                <div class="data_choice--col">
                    {{BootForm::text('company_address','Адрес',null,['class'=>'final_data'])}}
                </div>
            </div>


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