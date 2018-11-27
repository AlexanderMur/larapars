<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.10.2018
 * Time: 12:14
 */

namespace App\Http\Controllers;


use App\CompanyHistory;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateParserRequest;
use App\Models\Company;
use App\Models\Group;
use App\Models\HttpLog;
use App\Models\ParsedCompany;
use App\Models\ParserTask;
use App\Models\Review;
use App\ParserLog;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings');
    }
    public function restoreDefaults(){

        \DB::table('jobs')->truncate();
        ParsedCompany::truncate();
        Company::truncate();
        ParserLog::truncate();
        HttpLog::truncate();
        Review::truncate();
        ParserTask::truncate();
        CompanyHistory::truncate();
        Group::truncate();

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
    public function changePassword(ChangePasswordRequest $request){
        if (!password_verify($request->old_password, \Auth::user()->password)) {
            \Toastr::error('Не совпадает старый пароль');
            return redirect()->back();
        }
        $user = \Auth::user();

        $user->password = \Hash::make($request->new_password);
        $user->save();
        \Toastr::success('Пароль сохранен');
        return redirect()->back();
    }
}