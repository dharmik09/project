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
	Route::post('/apiVersion', 'Webservice\RestLessController@apiVersion');
	Route::post('/login', 'Webservice\LoginController@login');
	Route::post('/getCountryList', 'Webservice\RestLessController@getCountryList');
	Route::post('/getSponsors', 'Webservice\RestLessController@getSponsors');
	Route::post('/verifyOTP', 'Webservice\PasswordController@verifyOTP');
	Route::post('/resetPassword', 'Webservice\PasswordController@resetPassword');
	Route::post('/forgotPassword', 'Webservice\PasswordController@forgotPassword');
	//Dought
	Route::post('/userLogout', 'Webservice\LoginController@userLogout');
	
	Route::post('/saveUpdatedDeviceToken', 'Webservice\LoginController@saveUpdatedDeviceToken');
	Route::post('/updateTeenagerLoginToken', 'Webservice\LoginController@updateTeenagerLoginToken');
	//Dought for login token required in response or not. If yes then have to pass required information
	Route::post('/signup', 'Webservice\SignupController@signup');
});

Route::group([ 'middleware' => ['api-support'] ], function () {
	Route::post('/setPassword', 'Webservice\PasswordController@setPassword');
	Route::post('/changePassword', 'Webservice\PasswordController@changePassword');
	Route::post('/updateProfile', 'Webservice\DashboardController@updateProfile');
	Route::post('/getTeenagerProfileData', 'Webservice\ProfileController@getTeenagerProfileData');
	Route::post('/deleteTeenagerData', 'Webservice\ProfileController@deleteTeenagerData');
	Route::post('/getActiveTeenages', 'Webservice\TeenagerController@getActiveTeenages');
	Route::post('/getActiveTeenagesBySearch', 'Webservice\TeenagerController@getActiveTeenagesBySearch');
	//Parent&Mentor invitation section
	Route::post('/getParentMentorList', 'Webservice\ParentController@getParentMentorList');
	Route::post('/parentTeenPair', 'Webservice\ParentController@parentTeenPair');
	//First Level Part 1 Question/Answer Route
	Route::post('/getLevel1Questions', 'Webservice\Level1ActivityController@getFirstLevelActivity');
	Route::post('/submitLevel1Answers', 'Webservice\Level1ActivityController@saveFirstLevelActivity');
	//Education & Achievement Route
	Route::post('/getTeenagerAcademicInfo', 'Webservice\DashboardController@getTeenagerAcademicInfo');
	Route::post('/saveTeenagerAcademicInfo', 'Webservice\DashboardController@saveTeenagerAcademicInfo');
	Route::post('/getTeenagerAchievementInfo', 'Webservice\DashboardController@getTeenagerAchievementInfo');
	Route::post('/saveTeenagerAchievementInfo', 'Webservice\DashboardController@saveTeenagerAchievementInfo');
	//Help
	Route::post('/help', 'Webservice\HomeController@help');
	Route::post('/helpSearch', 'Webservice\HomeController@helpSearch');
	//Dashboard
	Route::post('/getDashboardDetail', 'Webservice\DashboardController@getDashboardDetail');
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
