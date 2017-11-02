<?php

Route::get('/home', 'Admin\DashboardController@index')->name('home');
Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');

//All Users section
Route::get('/teenagers', 'Admin\TeenagerManagementController@index');
Route::post('/teenagers', 'Admin\TeenagerManagementController@index');