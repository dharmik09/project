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

Route::get('/', 'Home\HomeController@index');
Route::get('/home', 'Home\HomeController@index');
Route::get('/faq', 'Home\HomeController@faq');

Route::group(['prefix' => 'admin'], function () {
	Route::get('/', 'Admin\LoginController@login');
	Route::get('/login', 'Admin\LoginController@login')->name('admin.login');
	Route::post('/loginCheck', 'Admin\LoginController@loginCheck')->name('loginCheck');
	Route::get('/loginCheck', 'Admin\LoginController@login');
	Route::post('/logout', 'Admin\LoginController@logout')->name('logout');
  	Route::get('/register', 'Admin\RegisterController@showRegistrationForm')->name('register');
  	Route::post('/register', 'Admin\RegisterController@register');
});

Route::group(['prefix' => 'teenager'], function () {
	Route::get('/', 'Teenager\HomeController@index');
	Route::get('/home', 'Teenager\HomeController@index');
	Route::get('/login', 'Teenager\LoginController@login')->name('login');
	Route::post('/login-check', 'Teenager\LoginController@loginCheck')->name('loginCheck');
	Route::post('/logout', 'Teenager\LoginController@logout')->name('logout');
	Route::get('/register', 'Teenager\RegisterController@showRegistrationForm')->name('register');
	Route::post('/register', 'Teenager\RegisterController@register');
});

Route::group(['prefix' => 'developer'], function () {
	Route::get('/', 'Developer\LoginController@login');
	Route::get('/login', 'Developer\LoginController@login')->name('developer.login');
	Route::post('/loginCheck', 'Developer\LoginController@loginCheck')->name('loginCheck');
	Route::post('/logout', 'Developer\LoginController@logout')->name('logout');
	
});

Route::get('/get-state/{id}', 'StateCityController@getState');
Route::get('/get-city/{id}', 'StateCityController@getCity');

Route::group(['prefix' => 'sponsor'], function () {
	Route::get('/', 'Sponsor\LoginController@login');
	Route::get('/login', 'Sponsor\LoginController@login')->name('sponsor.login');
	Route::post('/login-check', 'Sponsor\LoginController@loginCheck')->name('sponsor.loginCheck');
	Route::post('/logout', 'Sponsor\LoginController@logout')->name('sponsor.logout');
	Route::get('/signup', 'Sponsor\SignupController@signup')->name('sponsor.signup');
	Route::post('/do-signup', 'Sponsor\SignupController@doSignup')->name('sponsor.doSignup');
	Route::get('/do-signup', 'Sponsor\SignupController@signup');
	Route::get('/enterprise-request', 'Sponsor\SignupController@preLoginPackagePurchase')->name('sponsor.enterprise-request');
});