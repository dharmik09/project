<?php

Route::get('/home', 'Parent\DashboardManagementController@index')->name('home');

//My Profile
Route::get('/update-profile', 'Parent\ProfileManagementController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Parent\ProfileManagementController@saveProfile')->name('save-profile');

//My Challengers
Route::get('/my-challengers/', 'Parent\Level4ActivityManagementController@myChallengers')->name('my-challengers');
Route::get('/my-challengers-research/{professionId}/{teenId}', 'Parent\Level4ActivityManagementController@myChallengersResearch')->name('my-challengers-research');
Route::get('/my-challengers-accept/{professionId}/{teenId}', 'Parent\Level4ActivityManagementController@myChallengersAccept')->name('my-challengers-accept');
Route::get('/level4-activity/{professionId}/{teenId}', 'Parent\Level4ActivityManagementController@professionQuestion')->name('level4-activity');
Route::post('/play-level4-activity', 'Parent\Level4ActivityManagementController@saveLevel4Ans')->name('play-level4-activity');
Route::get('/level4-play-more/{professionId}/{teenId}', 'Parent\Level4ActivityManagementController@level4PlayMore')->name('level4-play-more');
Route::post('/get-available-coins-for-template/', 'Parent\CoinManagementController@getAvailableCoinsForTemplate')->name('get-available-coins-for-template');
//Route::post('/get-coins-for-template/', 'Parent\CoinManagementController@getCoinsForTemplate')->name('get-coins-for-template');
Route::any('/my-coins/', 'Parent\CoinManagementController@display')->name('my-coins');
Route::get('/get-transaction/', 'Parent\CoinManagementController@getTransaction')->name('get-transaction');
Route::get('/get-consumption/', 'Parent\CoinManagementController@getConsumption')->name('get-consumption');
Route::get('/get-gift-coins/', 'Parent\CoinManagementController@getGiftCoins')->name('get-gift-coins');
Route::post('/user-search-for-show-gift-coins/', 'Parent\CoinManagementController@userSearchForShowGiftCoins')->name('user-search-for-show-gift-coins');
Route::post('/user-search-for-coins/', 'Parent\CoinManagementController@userSearchForCoins')->name('user-search-for-coins');
Route::get('/save-coin-purchased-data/{id}', 'Parent\CoinManagementController@saveCoinPurchasedData')->name('save-coin-purchased-data');
Route::post('/get-concept-data','Parent\CoinManagementController@saveConceptCoinsDetail')->name('get-concept-data');
Route::get('/level4-intermediate-activity/{professionId}/{templateId}/{teenId}', 'Parent\Level4ActivityManagementController@level4IntermediateActivity');
Route::post('/level4-intermediate-activity/{professionId}/{templateId}/{teenId}', 'Parent\Level4ActivityManagementController@level4IntermediateActivity')->name('level4-intermediate-activity');
Route::post('/play-level4-intermediate-activity', 'Parent\Level4ActivityManagementController@saveLevel4IntermediateAns')->name('play-level4-intermediate-activity');

//Route::post('/get-question-data-advance-level', 'Parent\Level4AdvanceActivityManagementController@getQuestionDataAdvanceLevel')->name('get-question-data-advance-level');
Route::get('/level4-advance-step2/{professionId}/{typeid}/{teenId}', 'Parent\Level4AdvanceActivityManagementController@level4AdvanceStep2')->name('level4-advance-step2');
Route::post('/submit-level4-advance-activity', 'Parent\Level4AdvanceActivityManagementController@submitLevel4AdvanceActivity')->name('submit-level4-advance-activity');
Route::post('/delete-user-advance-task/', 'Parent\Level4AdvanceActivityManagementController@deleteUserAdvanceTask')->name('delete-user-advance-task');
Route::post('/submit-level4-advance-activity-for-review', 'Parent\Level4AdvanceActivityManagementController@submitLevel4AdvanceActivityForReview')->name('submit-level4-advance-activity-for-review');

//Careers details page
Route::get('/career-detail/{slug}/{teenId}', 'Parent\ProfessionController@careerDetails');
Route::post('/get-teenagers-challenged-to-parent', 'Parent\ProfessionController@getTeenagersChallengedToParent');
Route::post('/show-competitor-data/', 'Parent\ProfessionController@showCompetitorData')->name('show-competitor-data');
Route::post('/load-more-leaderboard', 'Parent\ProfessionController@getLeaderBoardDetails');

//Level4 Basic Activity
Route::post('/play-basic-level-activity', 'Parent\Level4ActivityManagementController@getL4BasicQuestions');
Route::post('/save-basic-level-activity', 'Parent\Level4ActivityManagementController@saveBasicLevelActivity');

//Level4 Advance Activity
Route::post('/get-question-data-advance-level', 'Parent\Level4AdvanceActivityManagementController@getQuestionDataAdvanceLevel');
Route::post('/get-media-upload-section', 'Parent\Level4AdvanceActivityManagementController@getMediaUploadSection');
Route::post('/get-level4-advance-step2-details', 'Parent\Level4AdvanceActivityManagementController@getLevel4AdvanceStep2Details');
Route::post('/submit-level4-advance-activity', 'Parent\Level4AdvanceActivityManagementController@submitLevel4AdvanceActivity');
Route::post('/submit-level4-advance-activity-for-review', 'Parent\Level4AdvanceActivityManagementController@submitLevel4AdvanceActivityForReview');
Route::post('/delete-user-advance-task', 'Parent\Level4AdvanceActivityManagementController@deleteUserAdvanceTask');

//Level 4 Intermediate Activity
Route::post('/get-coins-for-template', 'Parent\CoinManagementController@getCoinsForTemplate');
Route::post('/save-coins-for-template-data', 'Parent\CoinManagementController@saveConceptCoinsDetail');
Route::post('/play-intermediate-level-activity', 'Parent\Level4ActivityManagementController@professionIntermediateQuestions')->name('play-intermediate-level-activity');
Route::post('/save-intermediate-level-activity', 'Parent\Level4ActivityManagementController@saveIntermediateLevelActivity')->name('play-intermediate-level-activity');
Route::post('/get-competitor-data/', 'Parent\DashboardManagementController@getCompetitorData')->name('get-competitor-data');

//Learning Guidance
Route::get('/learning-guidance/{teenUniqueId}', 'Parent\Level4ActivityManagementController@learningGuidance');
Route::post('/save-consumed-coins-details', 'Parent\Level4ActivityManagementController@saveConsumedCoinsDetails');

//My Teen
Route::get('/pair-with-teen', 'Parent\DashboardManagementController@pairWithTeen')->name('pair-with-teen');
Route::post('/save-pair', 'Parent\DashboardManagementController@savePair')->name('save-pair');
Route::post('/gift-coins/', 'Parent\CoinManagementController@giftcoinstoTeenager')->name('gift-coins');
Route::post('/save-coins-data-for-teen','Parent\CoinManagementController@saveGiftedCoinsDetail')->name('save-coins-data-for-teen');

//Progress
Route::get('/progress/{id}', 'Parent\DashboardManagementController@progress')->name('progress');
Route::post('/get-profession-badges-and-rank/', 'Parent\DashboardManagementController@getProfessionBadgesAndRank')->name('get-profession-badges-and-rank');
Route::post('/save-teen-promise-rate/', 'Parent\DashboardManagementController@saveTeenPromiseRate')->name('save-teen-promise-rate');
Route::post('/get-teen-promise-rate-count/', 'Parent\DashboardManagementController@getTeenPromiseRateCount')->name('get-teen-promise-rate-count');
Route::get('/progress/', 'Parent\DashboardManagementController@progress');
Route::post('/get-available-coins/', 'Parent\CoinManagementController@getAvailableCoins')->name('get-available-coins');
Route::post('/get-coins-for-parent/', 'Parent\CoinManagementController@getCoinsForParent')->name('get-coins-for-parent');
Route::post('/purchased-coins-to-view-report', 'Parent\DashboardManagementController@purchasedCoinsToViewReport')->name('purchased-coins-to-view-report');
Route::get('/export-pdf/{id}', 'Parent\DashboardManagementController@exportPDF')->name('export-pdf');
Route::post('/get-profession-badges-and-rank-on-click/', 'Parent\DashboardManagementController@getProfessionBadgesAndRankOnClick')->name('get-profession-badges-and-rank-on-click');
Route::post('/get-available-coins-for-parent/', 'Parent\CoinManagementController@getAvailableCoinsForParent')->name('get-available-coins-for-parent');
Route::post('/get-learning-style', 'Parent\DashboardManagementController@getLearningStyle')->name('get-learning-style');
Route::post('/get-remaining-days/', 'Parent\CoinManagementController@getRemainigDays')->name('get-remaining-days');
Route::post('/get-remaining-days-for-report/', 'Parent\CoinManagementController@getremainigdaysForReport')->name('get-remaining-days-for-report');
Route::post('/get-profession-education-path/', 'Parent\DashboardManagementController@getProfessionEducationPath')->name('get-profession-education-path');
Route::post('/accept-teen-request','Parent\CoinManagementController@acceptTeenRequest')->name('accept-teen-request');
Route::post('/get-interest-detail', 'Parent\DashboardManagementController@getTeenagerInterestDetails')->name('get-interest-detail');
Route::post('/get-strength-detail', 'Parent\DashboardManagementController@getTeenagerStrengthDetails')->name('get-strength-detail');

//Password
Route::get('/change-password', 'Parent\PasswordManagementController@changePassword')->name('change-password');
Route::post('/update-password', 'Parent\PasswordManagementController@updatePassword')->name('update-password');


