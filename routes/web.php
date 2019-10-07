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

Route::get('/', '\App\Http\Controllers\Auth\LoginController@showLoginForm');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/callback', '\App\Http\Controllers\Auth\LoginController@fbCallback')->name('fb.callback');
Route::get('/bitly/callback', 'HomeController@bitlyCallback')->name('bitly.callback');

Route::match(['get', 'post'], 'post/step1', 'PostController@step1')->name('step1');
Route::match(['get', 'post'], 'post/step2', 'PostController@step2')->name('step2');
Route::match(['get', 'post'], 'post/step3', 'PostController@step3')->name('step3');
Route::delete('/folder/upload', 'PostController@clearUploadFolder')->name('folder.upload.clear');

Route::get('/report/download/{id}', 'FbReportController@download')->name('report.download');
Route::delete('/report/remove/{id}', 'FbReportController@remove')->name('report.remove');
