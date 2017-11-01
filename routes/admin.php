<?php

Route::get('/home', 'Admin\DashboardController@index')->name('home');
Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');