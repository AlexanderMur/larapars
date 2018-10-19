<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 20.10.2018
 * Time: 1:23
 */

namespace App\Http\Requests;


class DonorRequest extends Request
{

    public function rules()
    {
        return [
            'title' => 'required',
            'link' => 'required',
        ];
    }
}