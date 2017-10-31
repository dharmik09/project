<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('developer')->user();

    //dd($users);

    return view('developer.home');
})->name('home');

/* System Level Module Routes */
Route::get('/systemLevel', 'Developer\SystemLevelManagementController@index')->name('systemLevel');
Route::post('/systemLevel', 'Developer\SystemLevelManagementController@index')->name('systemLevel');
Route::get('/addSystemLevel', 'Developer\SystemLevelManagementController@add')->name('addSystemLevel');
Route::get('/editSystemLevel/{id}', 'Developer\SystemLevelManagementController@edit')->name('editSystemLevel');
Route::get('/deleteSystemLevel/{id}', 'Developer\SystemLevelManagementController@delete')->name('deleteSystemLevel');
Route::post('/saveSystemLevel', 'Developer\SystemLevelManagementController@save')->name('saveSystemLevel');

/* Apptitude Type Module Routes */
Route::get('/apptitudeType', 'Developer\ApptitudeTypeManagementController@index');
Route::post('/apptitudeType', 'Developer\ApptitudeTypeManagementController@index');
Route::get('/addApptitudeType', 'Developer\ApptitudeTypeManagementController@add');
Route::get('/editApptitudeType/{id}', 'Developer\ApptitudeTypeManagementController@edit');
Route::get('/deleteApptitudeType/{id}', 'Developer\ApptitudeTypeManagementController@delete');
Route::post('/saveApptitudeType', 'Developer\ApptitudeTypeManagementController@save');

/* Personality Type Module Routes */
Route::get('/personalityType', 'Developer\PersonalityTypeManagementController@index')->name('personalityType');
Route::post('/personalityType', 'Developer\PersonalityTypeManagementController@index')->name('personalityType');
Route::get('/addPersonalityType', 'Developer\PersonalityTypeManagementController@add')->name('addPersonalityType');
Route::get('/editPersonalityType/{id}', 'Developer\PersonalityTypeManagementController@edit')->name('editPersonalityType');
Route::post('/savePersonalityType', 'Developer\PersonalityTypeManagementController@save')->name('savePersonalityType');
Route::get('/deletePersonalityType/{id}', 'Developer\PersonalityTypeManagementController@delete')->name('deletePersonalityType');
