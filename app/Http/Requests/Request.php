<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function __get($key)
    {
        return parent::get($key);
    }
}
