<?php

Route::get('/home', 'Admin\DashboardController@index')->name('home');
Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');

//Video Module Routes 
Route::get('/video', 'Admin\VideoManagementController@index')->name('video');
Route::post('/video', 'Admin\VideoManagementController@index')->name('video');
Route::get('/addVideo', 'Admin\VideoManagementController@add')->name('addVideo');
Route::post('saveVideo', 'Admin\VideoManagementController@save')->name('saveVideo');
Route::get('/editVideo/{id}', 'Admin\VideoManagementController@edit')->name('editVideo');
Route::get('/deleteVideo/{id}', 'Admin\VideoManagementController@delete')->name('deleteVideo');

//All Users section
Route::get('/teenagers', 'Admin\TeenagerManagementController@index');
Route::post('/teenagers', 'Admin\TeenagerManagementController@index');

//Faq Section
Route::get('/faq', 'Admin\FAQManagementController@index');
Route::post('/faq', 'Admin\FAQManagementController@index');
Route::get('/addFaq', 'Admin\FAQManagementController@add');
Route::post('/saveFaq', 'Admin\FAQManagementController@save');
Route::get('/editFaq/{id}', 'Admin\FAQManagementController@edit');
Route::get('/deleteFaq/{id}', 'Admin\FAQManagementController@delete');

