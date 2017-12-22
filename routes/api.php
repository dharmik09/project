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

Route::post('/login', 'Webservice\LoginController@login');

Route::get('/get-state/{id}', 'StateCityController@getState');
Route::get('/get-city/{id}', 'StateCityController@getCity');

Route::group([ 'middleware' => ['api-support'] ], function () {
	Route::get('articles', 'Webservice\ArticleController@index');
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
