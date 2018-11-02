<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.10.2018
 * Time: 12:14
 */

namespace App\Http\Controllers;


use App\Http\Requests\UpdateParserRequest;

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
        setting()->setSetting('time',$request->time);
        setting()->setSetting('proxies',$request->proxies);
        setting()->setSetting('concurrency',$request->concurrency);
        setting()->setSetting('tries',$request->tries);
        \Toastr::success('Настройки обновлены');
        return redirect()->back();
    }
}