<?php

Route::get('/home', 'Sponsor\DashboardController@index')->name('home');

//My Profile
Route::get('/update-profile', 'Sponsor\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Sponsor\UpdateProfileController@saveProfile')->name('save-profile');

//My Coins
Route::any('/my-coins/', 'Sponsor\CoinsManagement@display')->name('my-coins');
Route::get('/get-transaction/', 'Sponsor\CoinsManagement@getTransaction')->name('get-transaction');
Route::get('/get-consumption/', 'Sponsor\CoinsManagement@getConsumption')->name('get-consumption');
Route::get('/save-coin-purchased-data/{id}', 'Sponsor\CoinsManagement@saveCoinPurchasedData')->name('save-coin-purchased-data');

