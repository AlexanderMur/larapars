<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 20.10.2018
 * Time: 1:23
 */

namespace App\Http\Requests;
/**
 * Class ChangePasswordRequest
 * @package App\Http\Requests
 * @property string $old_password
 * @property string $new_password
 */
class ChangePasswordRequest extends Request
{

    public function rules()
    {
        return [
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ];
    }

}