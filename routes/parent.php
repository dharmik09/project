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
Route::post('/show-competitor-data/', 'Parent\ParentDashboardController@showCompetitorData')->name('show-competitor-data');

Route::get('/level4-advance/{professionId}/{teenId}', 'Parent\Level4AdvanceActivityController@level4Advance')->name('level4-advance');
Route::post('/get-question-data-advance-level', 'Parent\Level4AdvanceActivityController@getQuestionDataAdvanceLevel')->name('get-question-data-advance-level');
Route::get('/level4-advance-step2/{professionId}/{typeid}/{teenId}', 'Parent\Level4AdvanceActivityController@level4AdvanceStep2')->name('level4-advance-step2');
Route::post('/submit-level4-advance-activity', 'Parent\Level4AdvanceActivityController@submitLevel4AdvanceActivity')->name('submit-level4-advance-activity');
Route::post('/delete-user-advance-task/', 'Parent\Level4AdvanceActivityController@deleteUserAdvanceTask')->name('delete-user-advance-task');
Route::post('/submit-level4-advance-activity-for-review', 'Parent\Level4AdvanceActivityController@submitLevel4AdvanceActivityForReview')->name('submit-level4-advance-activity-for-review');

//My Teen
Route::get('/pair-with-teen', 'Parent\ParentDashboardController@pairWithTeen')->name('pair-with-teen');
Route::post('/save-pair', 'Parent\ParentDashboardController@savePair')->name('save-pair');
Route::post('/gift-coins/', 'Parent\CoinsManagement@giftcoinstoTeenager')->name('gift-coins');
Route::post('/save-coins-data-for-teen','Parent\CoinsManagement@saveGiftedCoinsDetail')->name('save-coins-data-for-teen');

//Progress
Route::get('/progress/{id}', 'Parent\ParentDashboardController@progress')->name('progress');
Route::post('/get-profession-badges-and-rank/', 'Parent\ParentDashboardController@getProfessionBadgesAndRank')->name('get-profession-badges-and-rank');
Route::post('/save-teen-promise-rate/', 'Parent\ParentDashboardController@saveTeenPromiseRate')->name('save-teen-promise-rate');
Route::post('/get-teen-promise-rate-count/', 'Parent\ParentDashboardController@getTeenPromiseRateCount')->name('get-teen-promise-rate-count');
Route::get('/progress/', 'Parent\ParentDashboardController@progress');
Route::post('/get-available-coins/', 'Parent\CoinsManagement@getAvailableCoins')->name('get-available-coins');
Route::post('/get-coins-for-parent/', 'Parent\CoinsManagement@getCoinsForParent')->name('get-coins-for-parent');
Route::post('/purchased-coins-to-view-report', 'Parent\ParentDashboardController@purchasedCoinsToViewReport')->name('purchased-coins-to-view-report');
Route::get('/export-pdf/{id}', 'Parent\ParentDashboardController@exportPDF')->name('export-pdf');
Route::post('/get-profession-badges-and-rank-on-click/', 'Parent\ParentDashboardController@getProfessionBadgesAndRankOnClick')->name('get-profession-badges-and-rank-on-click');
Route::post('/get-available-coins-for-parent/', 'Parent\CoinsManagement@getAvailableCoinsForParent')->name('get-available-coins-for-parent');
Route::post('/get-learning-style', 'Parent\ParentDashboardController@getLearningStyle')->name('get-learning-style');
Route::post('/get-remaining-days/', 'Parent\CoinsManagement@getRemainigDays')->name('get-remaining-days');
Route::post('/get-remaining-days-for-report/', 'Parent\CoinsManagement@getremainigdaysForReport')->name('get-remaining-days-for-report');
Route::post('/get-profession-education-path/', 'Parent\ParentDashboardController@getProfessionEducationPath')->name('get-profession-education-path');

//Password
Route::get('/change-password', 'Parent\PasswordController@changePassword')->name('change-password');
Route::post('/update-password', 'Parent\PasswordController@updatePassword')->name('update-password');


