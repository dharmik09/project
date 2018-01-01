<?php

Route::get('/home', 'Teenager\DashboardController@dashboard')->name('home');
Route::get('/dashboard', 'Teenager\DashboardController@dashboard');
Route::get('/edit-profile', 'Teenager\DashboardController@profile');
Route::get('/my-profile', 'Teenager\DashboardController@profile');
Route::post('/save-profile', 'Teenager\DashboardController@saveProfile')->name('save-profile');
Route::post('/save-teenager-academic-info', 'Teenager\DashboardController@saveTeenagerAcademic');
Route::post('/save-teenager-achievement-info', 'Teenager\DashboardController@saveTeenagerAchievement');
Route::get('/chat', 'Teenager\DashboardController@chat');
Route::post('/get-phone-code-by-country-for-profile', 'Teenager\DashboardController@getPhoneCodeByCountry');
Route::post('/save-pair', 'Teenager\DashboardController@savePair')->name('save-pair');
Route::get('/gift-coupons', function() {
	return view('teenager.giftCoupons');
});
Route::get('/interest/{slug}', 'Teenager\InterestManagementController@index');
	//return view('teenager.interest');
Route::get('/multi-intelligence/{type}/{slug}', 'Teenager\MultipleIntelligenceManagementController@index');

Route::get('/seo-mi', function() {
	return view('teenager.seoMi');
});
Route::get('/seo-teaser', function() {
	return view('teenager.seoTeaser');
});

//Help
Route::get('help', 'Teenager\HomeController@help');
Route::post('/search-help', 'Teenager\HomeController@help')->name('search-help');

//ProCoins Gift
Route::get('/get-gift-coins/', 'Teenager\CoinManagementController@getGiftCoins');
Route::post('/user-search-for-gifted-coins/', 'Teenager\CoinManagementController@userSearchForGiftCoins');

//ProCoins History
Route::get('/get-pro-coins-history/', 'Teenager\CoinManagementController@getProCoinsHistory');

//Buy ProCoins
Route::get('/buy-procoins', 'Teenager\CoinManagementController@displayProCoins');
Route::post('/request-parent', 'Teenager\CoinManagementController@requestParentForPurchasedCoins');
Route::get('/save-coin-purchased-data/{id}', 'Teenager\CoinManagementController@saveCoinPurchasedData');

//Coupons
Route::get('/coupons/', 'Teenager\CouponManagementController@coupons');
Route::post('/consume-coupon', 'Teenager\CouponManagementController@consumeCoupon');
Route::post('/get-users', 'Teenager\CouponManagementController@getUsers');

Route::get('/playLevel1Activity', 'Teenager\level1ActivityController@playLevel1Activity');