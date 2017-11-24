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
Route::post('/play-level4-activity', 'Parent\Level4ActivityController@saveLevel4Ans')->name('play-level4-activity');
Route::get('/level4-play-more/{professionId}/{teenId}', 'Parent\Level4ActivityController@level4PlayMore')->name('level4-play-more');
Route::post('/get-available-coins-for-template/', 'Parent\CoinsManagement@getAvailableCoinsForTemplate')->name('get-available-coins-for-template');
Route::post('/get-coins-for-template/', 'Parent\CoinsManagement@getCoinsForTemplate')->name('get-coins-for-template');
Route::any('/my-coins/', 'Parent\CoinsManagement@display')->name('my-coins');
Route::get('/get-transaction/', 'Parent\CoinsManagement@getTransaction')->name('get-transaction');
Route::get('/get-consumption/', 'Parent\CoinsManagement@getConsumption')->name('get-consumption');
Route::get('/get-gift-coins/', 'Parent\CoinsManagement@getGiftCoins')->name('get-gift-coins');
Route::post('/user-search-for-show-gift-coins/', 'Parent\CoinsManagement@userSearchForShowGiftCoins')->name('user-search-for-show-gift-coins');
Route::post('/user-search-for-coins/', 'Parent\CoinsManagement@userSearchForCoins')->name('user-search-for-coins');
Route::get('/save-coin-purchased-data/{id}', 'Parent\CoinsManagement@saveCoinPurchasedData')->name('save-coin-purchased-data');
Route::post('/get-concept-data','Parent\CoinsManagement@saveConceptCoinsDetail')->name('get-concept-data');
Route::get('/level4-intermediate-activity/{professionId}/{templateId}/{teenId}', 'Parent\Level4ActivityController@level4IntermediateActivity');
Route::post('/level4-intermediate-activity/{professionId}/{templateId}/{teenId}', 'Parent\Level4ActivityController@level4IntermediateActivity')->name('level4-intermediate-activity');
Route::post('/play-level4-intermediate-activity', 'Parent\Level4ActivityController@saveLevel4IntermediateAns')->name('play-level4-intermediate-activity');

