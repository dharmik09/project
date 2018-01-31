<?php

Route::get('/home', 'Teenager\DashboardController@dashboard')->name('home');
Route::get('/dashboard', 'Teenager\DashboardController@dashboard');
Route::get('/edit-profile', 'Teenager\DashboardController@profile');
Route::get('/my-profile', 'Teenager\DashboardController@profile');
Route::get('/set-sound-value/{sound}', 'Teenager\ProfileController@setSoundOnOff');

Route::get('/set-profile', 'Teenager\DashboardController@setProfile')->name('set-profile');
Route::post('/save-profile', 'Teenager\DashboardController@saveProfile')->name('save-profile');
Route::post('/save-teenager-academic-info', 'Teenager\DashboardController@saveTeenagerAcademic');
Route::post('/save-teenager-achievement-info', 'Teenager\DashboardController@saveTeenagerAchievement');
Route::post('/load-more-my-careers', 'Teenager\DashboardController@loadMoreMyCareers')->name('load-more-my-careers');
Route::post('/get-phone-code-by-country-for-profile', 'Teenager\DashboardController@getPhoneCodeByCountry');
Route::post('/save-pair', 'Teenager\DashboardController@savePair')->name('save-pair');
Route::get('/gift-coupons', function() {
	return view('teenager.giftCoupons');
});
Route::get('/interest/{slug}', 'Teenager\InterestManagementController@index');
	//return view('teenager.interest');
Route::get('/multi-intelligence/{type}/{slug}', 'Teenager\MultipleIntelligenceManagementController@index');
Route::post('/see-more-related-careers', 'Teenager\MultipleIntelligenceManagementController@seeMoreRelatedCareers');

Route::get('/seo-mi', function() {
	return view('teenager.seoMi');
});
Route::get('/seo-teaser', function() {
	return view('teenager.seoTeaser');
});

Route::post('/get-interest-detail', 'Teenager\HomeController@getInterestDetail');
Route::post('/get-strength-detail', 'Teenager\HomeController@getStrengthDetail');
Route::post('/get-career-consideration', 'Teenager\ProfessionController@getCareerConsideration');


//Help
Route::get('help', 'Teenager\HomeController@help');
Route::post('/search-help', 'Teenager\HomeController@help')->name('search-help');

//ProCoins Gift
Route::get('/gift-coins/', 'Teenager\CoinManagementController@getGiftCoins');
Route::post('/user-search-to-gift-coins/', 'Teenager\CoinManagementController@userSearchToGiftCoins');
Route::post('/save-gifted-coins-data/', 'Teenager\CoinManagementController@saveGiftedCoinsData');
Route::post('/get-available-coins/', 'Teenager\CoinManagementController@getAvailableCoins');

//ProCoins History
Route::get('/get-pro-coins-history/', 'Teenager\CoinManagementController@getProCoinsHistory');
Route::post('/get-consumption-history-more-data', 'Teenager\CoinManagementController@getConsumptionHistoryMoreData');

//Buy ProCoins
Route::get('/buy-procoins', 'Teenager\CoinManagementController@displayProCoins');
Route::post('/request-parent', 'Teenager\CoinManagementController@requestParentForPurchasedCoins');
Route::get('/save-coin-purchased-data/{id}', 'Teenager\CoinManagementController@saveCoinPurchasedData');

//Coupons
Route::get('/coupons/', 'Teenager\CouponManagementController@coupons');
Route::post('/consume-coupon', 'Teenager\CouponManagementController@consumeCoupon');
Route::post('/get-users', 'Teenager\CouponManagementController@getUsers');

//Profile Level 1 Questions related route
Route::post('/play-first-level-activity', 'Teenager\Level1ActivityController@playLevel1Activity');
Route::post('/save-first-level-activity', 'Teenager\Level1ActivityController@saveFirstLevelActivity');
Route::post('/play-first-level-world-type', 'Teenager\Level1ActivityController@playLevel1WorldActivity');
Route::post('/get-icon-name-new', 'Teenager\Level1ActivityController@getIconNameNew');
Route::get('/get-icon-name-new', 'Teenager\DashboardController@profile');
Route::post('/add-icon-category', 'Teenager\Level1ActivityController@addIconCategory');
Route::post('/save-first-level-icon-category', 'Teenager\Level1ActivityController@saveFirstLevelIconCategory');
Route::post('/save-first-level-icon-quality', 'Teenager\Level1ActivityController@saveLevel1Part2Ans');

//Career
Route::get('/career-detail/{slug}', 'Teenager\ProfessionController@careerDetails');
Route::post('/add-star-to-career', 'Teenager\ProfessionController@addStarToCareer');
Route::get('/list-career', 'Teenager\ProfessionController@listIndex');
Route::post('/career-list', 'Teenager\ProfessionController@listGetIndex');
Route::post('/search-career-list', 'Teenager\ProfessionController@listGetSearch');
Route::get('/career-grid', 'Teenager\ProfessionController@gridIndex');
Route::post('/career-grid', 'Teenager\ProfessionController@gridGetIndex');
Route::post('/search-career-grid', 'Teenager\ProfessionController@gridGetSearch');
Route::post('/fetch-career-search-dropdown/', 'Teenager\ProfessionController@getSearchDropdown');
Route::post('/get-dropdown-search-result/', 'Teenager\ProfessionController@getDropdownSearchResult');
Route::get('/my-careers/', 'Teenager\ProfessionController@getTeenagerCareers');
Route::post('/get-my-careers-search/', 'Teenager\ProfessionController@getTeenagerCareersSearch');

//Tag
Route::get('/career-tag/{slug}', 'Teenager\ProfessionTagController@index');
Route::post('/tag-related-careers/', 'Teenager\ProfessionTagController@getIndex');

//Community
Route::get('/community', 'Teenager\CommunityManagementController@index');
Route::post('/search-community', 'Teenager\CommunityManagementController@index')->name('search-community');
Route::post('/load-more-new-connections', 'Teenager\CommunityManagementController@loadMoreNewConnections')->name('load-more-new-connections');
Route::post('/load-more-my-connections', 'Teenager\CommunityManagementController@loadMoreMyConnections')->name('load-more-my-connections');
Route::get('/send-request-to-teenager/{uniqueId}', 'Teenager\CommunityManagementController@sendRequest')->name('send-request-to-teenager');
Route::get('/accept-request/{id}', 'Teenager\CommunityManagementController@acceptRequest')->name('accept-request');
Route::get('/decline-request/{id}', 'Teenager\CommunityManagementController@declineRequest')->name('decline-request');
Route::post('/get-sub-filter', 'Teenager\CommunityManagementController@getSubFilter')->name('get-sub-filter');
Route::post('get-teenagers-by-filter', 'Teenager\CommunityManagementController@index')->name('get-teenagers-by-school');

//Route::get('/accept-connection-request', 'Teenager\CommunityManagementController@acceptRequest')->name('accept-connection-request');
//Route::get('/reject-connection-request', 'Teenager\CommunityManagementController@rejectRequest')->name('reject-connection-request');

//Learning Guidance
Route::get('/learning-guidance', 'Teenager\HomeController@learningGuidance');
	
//Network
Route::get('/my-network', 'Teenager\DashboardController@getMyNetworkDetails');
Route::post('/search-network', 'Teenager\DashboardController@getMyNetworkDetails');
Route::post('/get-network-members-by-filter', 'Teenager\DashboardController@getMyNetworkDetails');
Route::get('/network-member/{uniqueId}', 'Teenager\CommunityManagementController@getMemberDetails');

Route::post('get-level2-activity', 'Teenager\level2ActivityController@index')->name('getLevel2Activity');
Route::post('save-level2-activity', 'Teenager\level2ActivityController@saveLevel2Ans')->name('saveLevel2Activity');

Route::post('get-level1-trait', 'Teenager\Level1ActivityController@getLevel1Trait')->name('get-level1-trait');
Route::post('save-level1-trait', 'Teenager\Level1ActivityController@saveLevel1Trait')->name('save-level1-trait');

//Chat
Route::get('/chat', 'Teenager\ChatController@index');
Route::post('/getChatUsers', 'Teenager\ChatController@getChatUsers');
Route::post('/registerUserInAppLozic', 'Teenager\ChatController@registerUserInAppLozic');

//Notification
Route::post('/get-page-wise-notification', 'Teenager\ChatController@getPageWiseNotification');
Route::post('/delete-notification', 'Teenager\ChatController@deleteNotification');

//Help
Route::post('/get-help-text', 'Teenager\HelpController@getHelpTextBySlug');
Route::get('/payment', 'Teenager\CoinManagementController@payment');