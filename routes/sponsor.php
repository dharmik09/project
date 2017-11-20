<?php

Route::get('/home', 'Sponsor\DashboardController@index')->name('home');

//My Profile
Route::get('/update-profile', 'Sponsor\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Sponsor\UpdateProfileController@saveProfile')->name('save-profile');

