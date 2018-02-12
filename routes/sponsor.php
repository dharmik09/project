<?php

Route::get('/home', 'Sponsor\DashboardManagementController@index')->name('home');
Route::get('/inactive/{id}', 'Sponsor\DashboardManagementController@inactive');
Route::get('/purchase-credit', 'Sponsor\DashboardManagementController@purchaseCredit')->name('purchase-credit');
Route::get('/export-pdf', 'Sponsor\DashboardManagementController@exportPDF')->name('export-pdf');
Route::post('/get-available-coins/', 'Sponsor\CoinManagementController@getAvailableCoins')->name('get-available-coins');
Route::post('/get-available-coins-for-sponsor/', 'Sponsor\CoinManagementController@getAvailableCoinsForSponsor')->name('get-available-coins-for-sponsor');
Route::post('/get-coins-for-sponsor/', 'Sponsor\CoinManagementController@getCoinsForSponsor')->name('get-coins-for-sponsor');
Route::post('/get-remainig-days-for-sponsor/', 'Sponsor\CoinManagementController@getremainigdaysForSponsor')->name('get-remainig-days-for-sponsor');
Route::post('/purchased-coins-to-view-report', 'Sponsor\DashboardManagementController@purchasedCoinsToViewReport')->name('purchased-coins-to-view-report');
Route::get('/get-teenager-whose-applied-for-scholarship', 'Sponsor\DashboardManagementController@getTeenagerWhoseAppliedForScholarship');

//Advertisements
Route::get('/data-add/', 'Sponsor\DashboardManagementController@addForm')->name('data-add');
Route::post('/save', 'Sponsor\DashboardManagementController@save')->name('save');
Route::post('/get-credit', 'Sponsor\DashboardManagementController@getCreditKey')->name('get-credit');
Route::get('/edit/{id}', 'Sponsor\DashboardManagementController@edit')->name('edit');

//Gift ProCoins
Route::post('/gift-coins/', 'Sponsor\CoinManagementController@giftcoinstoSchool')->name('gift-coins');
Route::post('/save-coins-data-for-school','Sponsor\CoinManagementController@saveGiftedCoinsDetail')->name('save-coins-data-for-school');
Route::get('/save-coin-package-purchased-data/{p_id}', 'Sponsor\SignupController@saveCoinPurchasedData')->name('save-coin-package');

//Coupon
Route::get('/add-coupon', 'Sponsor\DashboardManagementController@addCoupon')->name('add-coupon');
Route::post('/coupon-bulk-save', 'Sponsor\DashboardManagementController@couponBulkSave')->name('coupon-bulk-save');
Route::get('/edit-coupon/{id}', 'Sponsor\DashboardManagementController@editCoupon')->name('edit-coupon');
Route::post('/save-coupon', 'Sponsor\DashboardManagementController@saveCoupon')->name('save-coupon');
Route::get('/get-coupon-competing/', 'Sponsor\DashboardManagementController@getCouponCompeting');

//My Profile
Route::get('/update-profile', 'Sponsor\ProfileManagementController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Sponsor\ProfileManagementController@saveProfile')->name('save-profile');

//My Coins
Route::any('/my-coins/', 'Sponsor\CoinManagementController@display')->name('my-coins');
Route::get('/get-transaction/', 'Sponsor\CoinManagementController@getTransaction')->name('get-transaction');
Route::get('/get-consumption/', 'Sponsor\CoinManagementController@getConsumption')->name('get-consumption');
Route::get('/save-coin-purchased-data/{id}', 'Sponsor\CoinManagementController@saveCoinPurchasedData')->name('save-coin-purchased-data');
Route::get('/get-gift-coins/', 'Sponsor\CoinManagementController@getGiftCoins')->name('get-gift-coins');

//Change Password
Route::get('/change-password', 'Sponsor\PasswordManagementController@changePassword')->name('change-password');
Route::post('/update-password', 'Sponsor\PasswordManagementController@updatePassword')->name('update-password');

