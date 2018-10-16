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
    Route::resource('companies', 'CompanyController');

    //Parsed companies routes
    Route::put('parsed_companies', 'ParsedCompanyController@bulk')->name('parsed_companies.bulk');
    Route::get('parsed_companies/{parsed_company}/detach', 'ParsedCompanyController@detach')->name('parsed_companies.detach');
    Route::get('parsed_companies/{parsed_company}/getReviews', 'ParsedCompanyController@getReviews')->name('parsed_companies.getReviews');
    Route::resource('parsed_companies', 'ParsedCompanyController');

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
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('view:clear');
    return $exitCode;
    // return what you want
});

Route::get('test', function () {

    if(isset($_GET['stop'])){
        unlink('check_file');
        return 'deleting!!!!';
    }
    if(!file_exists('check_file')){
        fopen('check_file','w');
    }
    for ($i = 0; $i < 10; $i++) {
        if(file_exists('check_file')){ //Есть другие варианты?
            //времязатратная задача
            info('working...');
            sleep(1);
        } else {
            break;
        }
    }
    info('job stopped. was working: ' . $i . ' seconds');

    return 123;
});
Route::get('test', function () {
    $can_work = true;
    pcntl_signal(SIGTERM, function() use(&$can_work){
        $can_work = false;
    });
    for ($i = 0; $i < 100; $i++) {
        if($can_work){ //Есть другие варианты?
            //времязатратная задача
            info('working...');
            sleep(1);
        } else {
            break;
        }
    }
    info('job stopped. was working: ' . $i . ' seconds');

    return 123;
});
Route::get('stop', function () {
    session_start();
    $_SESSION['stop'] = 1;
    return 123;
});

Route::get('/home', 'HomeController@index')->name('home');
