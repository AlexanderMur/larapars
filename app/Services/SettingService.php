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

/**
 * Class SettingService
 * @package App\Services
 * @property string proxies
 * @property string time
 * @property string concurrency
 * @property string tries
 */
class SettingService
{
    public function getSetting($field, $default = null){
        $setting = Setting::where('field',$field)->first();
        if($setting) {
            return json_decode($setting->value);
        }
        return $default;
    }
    public function setSetting($field, $value){
        if($value instanceof Carbon){
            $value = $value->__toString();
        }
        return Setting::updateOrCreate(['field'=>$field],['value'=>json_encode($value)]);
    }
    public function getProxies(){
        return preg_split('/\r\n/',$this->getSetting('proxies',''));
    }
    public function __get($key){
        return $this->getSetting($key);
    }
}