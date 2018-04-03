<?php

Route::get('/home', 'School\DashboardController@index')->name('home');

//My Student
Route::get('/bulk-import', 'School\DashboardController@bulkImport')->name('bulk-import');
Route::post('/save-school-bulk-import', 'School\DashboardController@savebulkdata')->name('save-school-bulk-import');
Route::get('/inactive/{id}/{status}', 'School\DashboardController@inactive')->name('inactive');
Route::post('/gift-coins/', 'School\DashboardController@giftcoinstoTeenager')->name('gift-coins');
Route::post('/save-coins-data-for-teen','School\DashboardController@saveGiftedCoinsDetail')->name('save-coins-data-for-teen');
Route::post('/gift-coins-to-all-teen/', 'School\DashboardController@giftcoinstoAllTeenager')->name('gift-coins-to-all-teen');
Route::post('/save-coins-data-for-all-teenager','School\DashboardController@saveCoinsDataForAllTeenager')->name('save-coins-data-for-all-teenager');
Route::post('/user-search-for-school-data/', 'School\DashboardController@userSearchForSchoolData')->name('user-search-for-school-data');
Route::post('/edit-teen-roll-num', 'School\DashboardController@editTeenRollnum')->name('edit-teen-roll-num');

//My Profile
Route::get('/update-profile', 'School\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'School\UpdateProfileController@saveProfile')->name('save-profile');

//Progress
Route::get('/progress', 'School\UpdateProfileController@progress')->name('progress');
Route::post('/get-available-coins/', 'School\DashboardController@getAvailableCoins')->name('get-available-coins');
Route::post('/get-coins-for-school/', 'School\DashboardController@getCoinsForSchool')->name('get-coins-for-school');
Route::post('/purchased-coins-to-view-report', 'School\DashboardController@purchasedCoinsToViewReport')->name('purchased-coins-to-view-report');
Route::get('/export-pdf/{id}', 'School\DashboardController@exportPDF')->name('export-pdf');
Route::post('/get-remaining-days-for-school/', 'School\DashboardController@getremainigdaysForSchool')->name('get-remaining-days-for-school');
Route::get('/progress/{cid}', 'School\UpdateProfileController@progress');

//My ProCoins
Route::get('/get-gift-coins/', 'School\DashboardController@getGiftCoins')->name('get-gift-coins');
Route::get('/get-consumption/', 'School\DashboardController@getConsumption')->name('get-consumption');
Route::post('/user-search-for-show-gift-coins/', 'School\DashboardController@userSearchForShowGiftCoins')->name('user-search-for-show-gift-coins');

//Level2 Questions
Route::get('/questions', 'School\QuestionsController@index');
Route::get('/add-questions', 'School\QuestionsController@add');
Route::post('/save-level2-questions', 'School\QuestionsController@save');
Route::get('/edit-level2-questions/{activityId}', 'School\QuestionsController@edit');
Route::get('/delete-level2-questions/{activityId}', 'School\QuestionsController@delete');
