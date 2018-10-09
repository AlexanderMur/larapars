<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 09.10.2018
 * Time: 21:04
 */

namespace App\Services;


use App\Models\Setting;
use Carbon\Carbon;

class SettingService
{
    public static function get($field, $default = null){
        $setting = Setting::where('field',$field)->first();
        if($setting) {
            return json_decode($setting->value);
        }
        return $default;
    }
    public static function set($field, $value){
        if($value instanceof Carbon){
            $value = $value->__toString();
        }
        return Setting::updateOrCreate(['field'=>$field],['value'=>json_encode($value)]);
    }
}