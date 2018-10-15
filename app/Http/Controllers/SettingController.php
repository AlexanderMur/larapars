<?php
/**
 * Created by PhpStorm.
 * User: jople
 * Date: 15.10.2018
 * Time: 12:14
 */

namespace App\Http\Controllers;


class SettingController extends Controller
{
    public function index(){
        if(request()->isMethod('post')){
            \Artisan::call('migrate:fresh',['--seed'=>true]);
            return redirect()->back()->with('success','Компании очищены');
        }
        return view('admin.settings.index');
    }
}