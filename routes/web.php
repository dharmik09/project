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

Route::group(['prefix' => 'admin'], function () {
	Route::get('/login', 'Admin\LoginController@login')->name('login');
	Route::post('/loginCheck', 'Admin\LoginController@loginCheck')->name('loginCheck');
  	Route::post('/logout', 'Admin\LoginController@logout')->name('logout');

  	Route::get('/register', 'Admin\RegisterController@showRegistrationForm')->name('register');
  	Route::post('/register', 'Admin\RegisterController@register');
});

Route::group(['prefix' => 'teenager'], function () {
	Route::get('/login', 'Teenager\LoginController@login')->name('login');
	Route::post('/loginCheck', 'Teenager\LoginController@loginCheck')->name('loginCheck');
	Route::post('/logout', 'Teenager\LoginController@logout')->name('logout');

	Route::get('/register', 'Teenager\RegisterController@showRegistrationForm')->name('register');
	Route::post('/register', 'Teenager\RegisterController@register');
});