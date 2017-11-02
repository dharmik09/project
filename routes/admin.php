<?php

Route::get('/home', 'Admin\DashboardController@index')->name('home');
Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');

/* Video Module Routes */
Route::get('/video', 'Admin\VideoManagementController@index')->name('video');
Route::post('/video', 'Admin\VideoManagementController@index')->name('video');
Route::get('/addVideo', 'Admin\VideoManagementController@add')->name('addVideo');
Route::post('saveVideo', 'Admin\VideoManagementController@save')->name('saveVideo');
Route::get('/editVideo/{id}', 'Admin\VideoManagementController@edit')->name('editVideo');
Route::get('/deleteVideo/{id}', 'Admin\VideoManagementController@delete')->name('deleteVideo');