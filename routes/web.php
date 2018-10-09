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

    //Companies routes
    Route::get('companies/search', 'CompanyController@search')->name('companies.search');
    Route::resource('companies', 'CompanyController');

    //Parsed companies routes
    Route::resource('parsed_companies', 'ParsedCompanyController');
    Route::put('parsed_companies', 'ParsedCompanyController@bulk')->name('parsed_companies.bulk');

    //Reviews routes
    Route::resource('reviews', 'ReviewController');
    Route::put('reviews', 'ReviewController@updateMany')->name('reviews.updateMany');
    Route::get('reviews2', 'ReviewController@main')->name('reviews.main');
    Route::get('reviews2/{review}/like', 'ReviewController@like')->name('reviews.like');
    Route::get('reviews2/{review}/dislike', 'ReviewController@dislike')->name('reviews.dislike');
    Route::get('reviews2/data', 'ReviewController@data')->name('reviews.data');
    Route::get('reviews2/new', 'ReviewController@new')->name('reviews.new');
    Route::get('reviews2/archive', 'ReviewController@archive')->name('reviews.archive');

    //Donors routes
    Route::resource('donors', 'DonorController');

    //Parsers routes
    Route::get('parsers/logs', 'ParserController@logs')->name('parsers.logs');
    Route::resource('parsers', 'ParserController');

    Route::get('pars-test', 'ParserController@start')->name('pars.test');
    Route::post('pars-test', 'ParserController@parse')->name('pars.parse');

    Route::get('manual-parser', 'ParserController@manualParser')->name('pars.manual');
    Route::post('manual-parser', 'ParserController@manualParser')->name('pars.manual');

    Route::get('all-data', 'AdminController@allData')->name('admin.alldata');
    Route::get('export', 'AdminController@export')->name('admin.export');

    Route::get('logs', 'AdminController@logs')->name('logs.index');
    Route::get('/clear-cache', function () {
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('view:clear');
        return $exitCode;
        // return what you want
    });
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');