<?php
declare(ticks = 1);
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
    Route::get('companies/bulk', 'CompanyController@bulk')->name('companies.bulk');
    Route::get('companies/{company}/logs', 'CompanyController@logs')->name('companies.logs');
    Route::resource('companies', 'CompanyController');

    //Parsed companies routes
    Route::put('parsed_companies', 'ParsedCompanyController@bulk')->name('parsed_companies.bulk');
    Route::get('parsed_companies/{parsed_company}/detach', 'ParsedCompanyController@detach')->name('parsed_companies.detach');
    Route::get('parsed_companies/{parsed_company}/getReviews', 'ParsedCompanyController@getReviews')->name('parsed_companies.getReviews');
    Route::get('parsed_companies/{parsed_company}/getHistory', 'Ajax\AjaxController@getParsedCompanyHistory')->name('parsed_companies.getHistory');
    Route::resource('parsed_companies', 'ParsedCompanyController');

    //Reviews routes
    Route::resource('reviews', 'ReviewController', ['middleware' => 'dateformat']);
    Route::put('reviews', 'ReviewController@updateMany')->name('reviews.updateMany');
    Route::get('reviews2/{review}/like', 'ReviewController@like')->name('reviews.like');
    Route::get('reviews2/{review}/dislike', 'ReviewController@dislike')->name('reviews.dislike');
    Route::get('reviews2/data', 'ReviewController@data')->name('reviews.data');
    Route::get('reviews2/new', 'ReviewController@new')->name('reviews.new');
    Route::get('reviews2/archive', 'ReviewController@archive')->name('reviews.archive');

    //Donors routes
    Route::resource('donors', 'DonorController');

    //Parsers routes
    Route::get('parsers/logs', 'ParserController@logs')->name('parsers.logs');

    Route::get('logs12/{log}/details', 'LogController@details')->name('logs.details');
    Route::get('logs', 'LogController@index')->name('logs.index');

    Route::resource('parsers', 'ParserController');

    Route::get('pars-test', 'ParserController@start')->name('pars.test');
    Route::post('pars-test', 'ParserController@parse')->name('pars.parse');


    Route::get('manual-parser', 'ParserController@manualParser')->name('pars.manual');
    Route::post('manual-parser', 'ParserController@manualParser')->name('pars.manual');

    Route::get('all-data', 'AdminController@allData')->name('admin.alldata');
    Route::get('export', 'AdminController@export')->name('admin.export');


    Route::get('settings', 'SettingController@index')->name('admin.settings');
    Route::post('settings', 'SettingController@index')->name('admin.settings');


});
Auth::routes();
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return redirect()->back();
});

Route::get('/home', 'HomeController@index')->name('home');
