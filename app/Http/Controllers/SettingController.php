<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.10.2018
 * Time: 12:14
 */

namespace App\Http\Controllers;


use App\Http\Requests\UpdateParserRequest;
use App\Services\SettingService;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }
    public function restoreDefaults(){

        \Artisan::call('migrate:fresh', ['--seed' => true]);

        return redirect()->back();
    }
    function updateParser(UpdateParserRequest $request)
    {
        SettingService::set('time',$request->time);
        SettingService::set('proxies',$request->proxies);
        \Toastr::success('Настройки обновлены');
        return redirect()->back();
    }
}