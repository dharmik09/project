<?php

Route::get('/home', 'Parent\ParentDashboardController@index')->name('home');
Route::get('/update-profile', 'Parent\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Parent\UpdateProfileController@saveProfile')->name('save-profile');

