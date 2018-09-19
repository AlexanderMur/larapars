<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('companies', 'CompanyController');

    Route::get('pars-test', 'ParserController@start')->name('pars.test');
    Route::post('pars-test', 'ParserController@parse')->name('pars.parse');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');