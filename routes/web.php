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


use Yajra\DataTables\Html\Builder;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('companies/search', 'CompanyController@search')->name('companies.search');
    Route::resource('companies', 'CompanyController');
    Route::resource('parsed_companies', 'ParsedCompanyController');
    Route::put('parsed_companies', 'ParsedCompanyController@bulk')->name('parsed_companies.bulk');
    Route::resource('reviews', 'ReviewController');
    Route::put('reviews', 'ReviewController@updateMany')->name('reviews.updateMany');
    Route::get('reviews2', 'ReviewController@main')->name('reviews.main');
    Route::get('reviews2/data', 'ReviewController@data')->name('reviews.data');
    Route::get('reviews2/new', 'ReviewController@new')->name('reviews.new');

    Route::get('reviews2/archive', 'ReviewController@archive')->name('reviews.archive');
    Route::resource('donors', 'DonorController');

    Route::get('parsers/logs', 'ParserController@logs')->name('parsers.logs');
    Route::resource('parsers', 'ParserController');

    Route::get('pars-test', 'ParserController@start')->name('pars.test');
    Route::get('all-data', 'AdminController@allData')->name('admin.alldata');
    Route::get('export', 'AdminController@export')->name('admin.export');
    Route::post('pars-test', 'ParserController@parse')->name('pars.parse');
    Route::get('/clear-cache', function () {
        $exitCode = Artisan::call('cache:clear');
        $exitCode = Artisan::call('config:clear');
        $exitCode = Artisan::call('view:clear');
        return $exitCode;
        // return what you want
    });
    Route::get('users', function (Builder $builder) {
        if (request()->ajax()) {
            return \DataTables::of(\App\User::query())->toJson();
        }

        $html = $builder->columns([
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'],
        ]);

        return view('users.index', compact('html'));
    });
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');