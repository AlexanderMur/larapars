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
        $this->request->set('mass_parsing',$this->request->has('mass_parsing'));
        return [
            'title' => 'required',
            'link' => 'required',
        ];
    }
}