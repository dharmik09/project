<?php

Route::get('/home', 'Sponsor\DashboardController@index')->name('home');
Route::get('/data-add/', 'Sponsor\DashboardController@addForm')->name('data-add');
Route::post('/save', 'Sponsor\DashboardController@save')->name('save');
Route::post('/get-credit', 'Sponsor\DashboardController@getCreditKey')->name('get-credit');
Route::get('/edit/{id}', 'Sponsor\DashboardController@edit')->name('edit');
Route::post('/gift-coins/', 'Sponsor\CoinsManagement@giftcoinstoSchool')->name('gift-coins');
Route::post('/save-coins-data-for-school','Sponsor\CoinsManagement@saveGiftedCoinsDetail')->name('save-coins-data-for-school');

//My Profile
Route::get('/update-profile', 'Sponsor\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Sponsor\UpdateProfileController@saveProfile')->name('save-profile');

//My Coins
Route::any('/my-coins/', 'Sponsor\CoinsManagement@display')->name('my-coins');
Route::get('/get-transaction/', 'Sponsor\CoinsManagement@getTransaction')->name('get-transaction');
Route::get('/get-consumption/', 'Sponsor\CoinsManagement@getConsumption')->name('get-consumption');
Route::get('/save-coin-purchased-data/{id}', 'Sponsor\CoinsManagement@saveCoinPurchasedData')->name('save-coin-purchased-data');

