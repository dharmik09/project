<?php

Route::get('/home', 'Teenager\DashboardController@dashboard')->name('home');
Route::get('/dashboard', 'Teenager\DashboardController@dashboard');
Route::get('/edit-profile', 'Teenager\DashboardController@profile');
Route::get('/my-profile', 'Teenager\DashboardController@profile');
Route::get('/set-profile', 'Teenager\DashboardController@setProfile')->name('set-profile');
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

//Profile Level 1 Questions related route
Route::post('/play-first-level-activity', 'Teenager\level1ActivityController@playLevel1Activity');
Route::post('/save-first-level-activity', 'Teenager\level1ActivityController@saveFirstLevelActivity');

//Career
Route::get('/my-careers', function() {
	return view('teenager.myCareers');
});
Route::get('/career-detail/{id}', function() {
	return view('teenager.careerDetail');
});
Route::get('/list-career', function() {
	return view('teenager.careersListing');
});
Route::get('/career-grid', function() {
	return view('teenager.careerGrid');
});
Route::get('/career-tag', function() {
	return view('teenager.careerTag');
});

//Community
Route::get('/community', 'Teenager\CommunityManagementController@index');
Route::post('/search-community', 'Teenager\CommunityManagementController@index')->name('search-community');
Route::post('/load-more-new-connections', 'Teenager\CommunityManagementController@loadMoreNewConnections')->name('load-more-new-connections');
Route::post('/load-more-my-connections', 'Teenager\CommunityManagementController@loadMoreMyConnections')->name('load-more-my-connections');
Route::get('/send-request-to-teenager/{uniqueId}', 'Teenager\CommunityManagementController@sendRequest')->name('send-request-to-teenager');
//Route::get('/accept-connection-request', 'Teenager\CommunityManagementController@acceptRequest')->name('accept-connection-request');
//Route::get('/reject-connection-request', 'Teenager\CommunityManagementController@rejectRequest')->name('reject-connection-request');

Route::get('/learning-guidance', function() {
	return view('teenager.learningGuidance');
});

//Network
Route::get('/my-network', function() {
	return view('teenager.network');
});
Route::get('/network-member/{uniqueId}', 'Teenager\CommunityManagementController@getMemberDetails');
