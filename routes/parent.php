<?php

Route::get('/home', 'Parent\ParentDashboardController@index')->name('home');

//My Profile
Route::get('/update-profile', 'Parent\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Parent\UpdateProfileController@saveProfile')->name('save-profile');

//My Challengers
Route::get('/my-challengers/', 'Parent\Level4ActivityController@myChallengers')->name('my-challengers');
Route::get('/my-challengers-research/{professionId}/{teenId}', 'Parent\Level4ActivityController@myChallengersResearch')->name('my-challengers-research');
Route::get('/my-challengers-accept/{professionId}/{teenId}', 'Parent\Level4ActivityController@myChallengersAccept')->name('my-challengers-accept');
Route::get('/level4-activity/{professionId}/{teenId}', 'Parent\Level4ActivityController@professionQuestion')->name('level4-activity');

