<?php

Route::get('/home', 'Teenager\DashboardController@dashboard')->name('home');
Route::get('/dashboard', 'Teenager\DashboardController@dashboard');
Route::get('/edit-profile', 'Teenager\ProfileController@profile');
Route::get('/my-profile', 'Teenager\ProfileController@profile');
Route::get('/set-sound-value/{sound}', 'Teenager\ProfileController@setSoundOnOff');

Route::get('/set-profile', 'Teenager\ProfileController@setProfile')->name('set-profile');
Route::post('/save-profile', 'Teenager\ProfileController@saveProfile')->name('save-profile');
Route::post('/save-teenager-academic-info', 'Teenager\DashboardController@saveTeenagerAcademic');
Route::post('/save-teenager-achievement-info', 'Teenager\DashboardController@saveTeenagerAchievement');
Route::post('/load-more-my-careers', 'Teenager\DashboardController@loadMoreMyCareers')->name('load-more-my-careers');
Route::post('/get-phone-code-by-country-for-profile', 'Teenager\DashboardController@getPhoneCodeByCountry');
Route::post('/save-pair', 'Teenager\DashboardController@savePair')->name('save-pair');
Route::get('/gift-coupons', function() {
	return view('teenager.giftCoupons');
});
Route::get('/interest/{slug}', 'Teenager\InterestManagementController@index');
Route::post('/see-more-interest-related-careers', 'Teenager\InterestManagementController@seeMoreRelatedCareers');
Route::post('/see-more-inerest-page-gurus', 'Teenager\InterestManagementController@seeMoreGurus');
Route::get('/multi-intelligence/{type}/{slug}', 'Teenager\MultipleIntelligenceManagementController@index');
Route::post('/see-more-related-careers', 'Teenager\MultipleIntelligenceManagementController@seeMoreRelatedCareers');
Route::post('/see-more-gurus', 'Teenager\MultipleIntelligenceManagementController@seeMoreGurus');

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
Route::get('help', 'Teenager\FAQController@help');
Route::post('/search-help', 'Teenager\FAQController@help')->name('search-help');

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
Route::get('/get-icon-name-new', 'Teenager\ProfileController@profile');
Route::post('/add-icon-category', 'Teenager\Level1ActivityController@addIconCategory');
Route::post('/save-first-level-icon-category', 'Teenager\Level1ActivityController@saveFirstLevelIconCategory');
Route::post('/save-first-level-icon-quality', 'Teenager\Level1ActivityController@saveLevel1Part2Ans');

//Career
Route::get('/career-detail/{slug}', 'Teenager\ProfessionController@careerDetails');
Route::post('/add-star-to-career', 'Teenager\ProfessionController@addStarToCareer');
Route::get('/get-career-pdf/{slug}', 'Teenager\ProfessionController@getCareerPdf');
Route::get('/list-career', 'Teenager\ProfessionController@listIndex');
Route::post('/career-list', 'Teenager\ProfessionController@listGetIndex');
Route::post('/search-career-list', 'Teenager\ProfessionController@listGetSearch');
Route::get('/career-grid', 'Teenager\ProfessionController@gridIndex');
Route::post('/career-grid', 'Teenager\ProfessionController@gridGetIndex');
Route::post('/search-career-grid', 'Teenager\ProfessionController@gridGetSearch');
Route::post('/fetch-career-search-dropdown/', 'Teenager\ProfessionController@getSearchDropdown');
Route::post('/get-dropdown-search-result/', 'Teenager\ProfessionController@getDropdownSearchResult');
Route::post('/get-my-career-dropdown-search-result', 'Teenager\ProfessionController@getMyCareerDropdownSearchResult');
Route::get('/my-careers/', 'Teenager\ProfessionController@getTeenagerCareers');
Route::post('/get-my-careers-search/', 'Teenager\ProfessionController@getTeenagerCareersSearch');
Route::post('/get-teenagers-for-starrated/', 'Teenager\ProfessionController@getTeenagerWhoStarRatedCareer');
Route::post('/apply-for-scholarship-program', 'Teenager\ProfessionController@applyForScholarshipProgram');
Route::post('/challenge-to-parent-and-mentor', 'Teenager\ProfessionController@challengeToParentAndMentor');
Route::post('/get-challenged-parent-and-mentor-list', 'Teenager\ProfessionController@getChallengedParentAndMentorList');
Route::post('/get-challenge-score-details', 'Teenager\ProfessionController@getChallengeScoreDetails');

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
Route::post('/save-consumed-coins-details', 'Teenager\HomeController@saveConsumedCoinsDetails');
	
//Network
Route::get('/my-network', 'Teenager\DashboardController@getMyNetworkDetails');
Route::post('/search-network', 'Teenager\DashboardController@getMyNetworkDetails');
Route::post('/get-network-members-by-filter', 'Teenager\DashboardController@getMyNetworkDetails');
Route::get('/network-member/{uniqueId}', 'Teenager\CommunityManagementController@getMemberDetails');
Route::post('/load-more-member-connections', 'Teenager\CommunityManagementController@loadMoreMemberConnections');

Route::post('get-level2-activity', 'Teenager\level2ActivityController@index')->name('getLevel2Activity');
Route::post('save-level2-activity', 'Teenager\level2ActivityController@saveLevel2Ans')->name('saveLevel2Activity');

Route::post('get-level1-trait', 'Teenager\Level1ActivityController@getLevel1Trait')->name('get-level1-trait');
Route::post('save-level1-trait', 'Teenager\Level1ActivityController@saveLevel1Trait')->name('save-level1-trait');

//Chat
Route::get('/chat', 'Teenager\ChatController@index');
Route::get('/chat/{uniqueId}', 'Teenager\ChatController@index');
Route::post('/getChatUsers', 'Teenager\ChatController@getChatUsers');
Route::post('/registerUserInAppLozic', 'Teenager\ChatController@registerUserInAppLozic');

//Notification
Route::get('/get-notification-count', 'Teenager\ChatController@getUnreadNotificationCount');
Route::post('/get-page-wise-notification', 'Teenager\ChatController@getPageWiseNotification');
Route::post('/delete-notification', 'Teenager\ChatController@deleteNotification');
Route::post('/read-notification', 'Teenager\ChatController@changeNotificationStatus');

//Help
Route::post('/get-help-text', 'Teenager\HelpController@getHelpTextBySlug');
Route::get('/payment', 'Teenager\CoinManagementController@payment');

//L3
Route::post('/teen-l3-career-research', 'Teenager\Level3ActivityController@level3CareerResearch');

//Level 4 question answer related routes
Route::post('/play-basic-level-activity', 'Teenager\Level4ActivityController@professionBasicQuestion');
Route::post('/save-basic-level-activity', 'Teenager\Level4ActivityController@saveBasicLevelActivity');
Route::post('/play-intermediate-level-activity', 'Teenager\Level4ActivityController@professionIntermediateQuestion');
Route::post('/save-intermediate-level-activity', 'Teenager\Level4ActivityController@saveIntermediateLevelActivity');

//Level 4 Advance Activity
Route::post('/get-question-data-advance-level', 'Teenager\Level4AdvanceActivityController@getQuestionDataAdvanceLevel');
Route::post('/get-media-upload-section', 'Teenager\Level4AdvanceActivityController@getMediaUploadSection');
Route::post('/get-level4-advance-step2-details', 'Teenager\Level4AdvanceActivityController@getLevel4AdvanceStep2Details');
Route::post('/submit-level4-advance-activity', 'Teenager\Level4AdvanceActivityController@submitLevel4AdvanceActivity');
Route::post('/submit-level4-advance-activity-for-review', 'Teenager\Level4AdvanceActivityController@submitLevel4AdvanceActivityForReview');
Route::post('/delete-user-advance-task', 'Teenager\Level4AdvanceActivityController@deleteUserAdvanceTask');
Route::post('/get-teen-profession-promiseplus', 'Teenager\Level4ActivityController@getPromisePlusData');

//Forum Module
Route::get('/forum-questions', 'Teenager\ForumController@index');
Route::post('/fetch-page-wise-forum-questions', 'Teenager\ForumController@getIndex');
Route::get('/forum-question/{id}', 'Teenager\ForumController@getQuestionByQuestionId');
Route::post('/fetch-question-answer', 'Teenager\ForumController@getAnswerByQuestionId');
Route::post('/save-forum-answer', 'Teenager\ForumController@saveForumAnswer');
Route::post('/get-user-score-progress', 'Teenager\HomeController@getUserScoreProgress');

//Get User unread message count chat
Route::post('/get-user-unread-message-chat', 'Teenager\ProfileController@getUserUnreadMessageCountChat');