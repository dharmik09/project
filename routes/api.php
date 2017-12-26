<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([ 'middleware' => ['api-outside'] ], function () {
	Route::post('/login', 'Webservice\LoginController@login');
	Route::post('/getCountryList', 'Webservice\RestLessController@getCountryList');
	Route::post('/getSponsors', 'Webservice\RestLessController@getSponsors');
});

Route::group([ 'middleware' => ['api-support'] ], function () {
	Route::post('/setPassword', 'Webservice\PasswordController@setPassword');
	Route::post('/changePassword', 'Webservice\PasswordController@changePassword');
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
