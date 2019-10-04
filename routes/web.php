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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/redirect', 'SocialAuthFacebookController@redirect');
Route::get('/callback', 'SocialAuthFacebookController@callback');

Route::get('/posts/create-step1', 'PostController@createStep1');
Route::post('/posts/create-step1', 'PostController@postCreateStep1');
Route::get('/posts/create-step2', 'PostController@createStep2');
Route::post('/posts/create-step2', 'PostController@postCreateStep2');
Route::post('/posts/remove-image', 'PostController@removeImage');
Route::get('/posts/create-step3', 'PostController@createStep3');
Route::post('/getPageAccessToken', 'PostController@getPageAccessToken');
