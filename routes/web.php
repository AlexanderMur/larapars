<?php
declare(ticks=1);

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
    return redirect()->route('parsed_companies.index');
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
    Route::get('parsed_companies/{parsed_company}/duplicates', 'ParsedCompanyController@duplicates')->name('parsed_companies.getHistory');
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
    Route::post('donors/bulk', 'DonorController@bulk')->name('donors.bulk');
    Route::get('donors/export', 'DonorController@export')->name('donors.export');
    Route::post('donors/import', 'DonorController@import')->name('donors.import');
    Route::resource('donors', 'DonorController');

    //Parsers routes
    Route::get('parsers/logs', 'ParserController@logs')->name('parsers.logs');
    Route::get('parser', 'ParserController@parser')->name('parser');

    Route::get('logs12/{task}/details', 'LogController@details')->name('logs.details');
    Route::get('logs', 'LogController@index')->name('logs.index');

    Route::resource('parsers', 'ParserController');

    Route::get('pars-test', 'ParserController@start')->name('pars.test');
    Route::post('pars-test', 'ParserController@parse')->name('pars.parse');


    Route::get('manual-parser', 'ParserController@manualParser')->name('pars.manual');
    Route::post('manual-parser', 'ParserController@manualParser')->name('pars.manual');

    Route::get('all-data', 'AdminController@allData')->name('admin.alldata');
    Route::get('export', 'AdminController@export')->name('admin.export');

    //Settings route
    Route::get('settings', 'SettingController@index')->name('admin.settings');
    Route::post('settings/restoreDefaults', 'SettingController@restoreDefaults')->name('admin.settings.restoreDefaults');
    Route::post('settings/updateParser', 'SettingController@updateParser')->name('admin.settings.updateParser');
    Route::post('settings/changePassword', 'SettingController@changePassword')->name('admin.settings.changePassword');


    //Tasks route
    Route::get('tasks/{task}/pause', 'TaskController@pause')->name('tasks.pause');
    Route::get('tasks/{task}/resume', 'TaskController@resume')->name('tasks.resume');
    Route::post('tasks/create', 'TaskController@create')->name('tasks.create');

    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('view:cache');
        return redirect()->back();
    });

    Route::get('sleep',function(){
        echo 'start';
        register_shutdown_function(function(){
            echo 'exiting';
        });
        $i=0;
        while(true){
            file_put_contents('file',$i++."\r\n");
            sleep(1);
        }
        echo 'wtf';
    });
});


Route::get('/schedule', function () {
    $code = Artisan::call('schedule:run');
    return response()->json($code);
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
