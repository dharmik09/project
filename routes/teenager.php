<?php

Route::get('/home', 'Teenager\DashboardController@dashboard')->name('home');
Route::get('/dashboard', 'Teenager\DashboardController@dashboard');
Route::get('/edit-profile', 'Teenager\DashboardController@profile');
Route::get('/my-profile', 'Teenager\DashboardController@profile');
Route::post('/save-profile', 'Teenager\DashboardController@saveProfile')->name('save-profile');
Route::get('/chat', 'Teenager\DashboardController@chat');
//Route::post('/save-pair', 'Teenager\DashboardController@savePair')->name('save-pair');
Route::get('/gift-coupons', function() {
	return view('teenager.giftCoupons');
});
Route::get('/interest/{slug}', 'Teenager\InterestManagementController@index');
	//return view('teenager.interest');
Route::get('/multi-intelligence/{type}/{slug}', 'Teenager\MultipleIntelligenceManagementController@index');
Route::get('/gift-procoins', function() {
	return view('teenager.proCoinsGift');
});
Route::get('/buy-procoins', function() {
	return view('teenager.proCoinsBuy');
});
Route::get('/procoins-history', function() {
	return view('teenager.proCoinsHistory');
});
Route::get('/seo-mi', function() {
	return view('teenager.seoMi');
});
Route::get('/seo-teaser', function() {
	return view('teenager.seoTeaser');
});

//Help
Route::get('help', 'Teenager\HomeController@help');

//ProCoins
Route::get('/get-gift-coins/', 'Teenager\CoinManagementController@getGiftCoins');
Route::post('/user-search-for-gifted-coins/', 'Teenager\CoinManagementController@userSearchForGiftCoins');
