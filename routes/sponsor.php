<?php

Route::get('/home', 'Sponsor\DashboardController@index')->name('home');
Route::get('/inactive/{id}', 'Sponsor\DashboardController@inactive');
Route::get('/purchase-credit', 'Sponsor\DashboardController@purchaseCredit')->name('purchase-credit');
Route::get('/export-pdf', 'Sponsor\DashboardController@exportPDF')->name('export-pdf');
Route::post('/get-available-coins/', 'Sponsor\CoinsManagement@getAvailableCoins')->name('get-available-coins');
Route::post('/get-available-coins-for-sponsor/', 'Sponsor\CoinsManagement@getAvailableCoinsForSponsor')->name('get-available-coins-for-sponsor');
Route::post('/get-coins-for-sponsor/', 'Sponsor\CoinsManagement@getCoinsForSponsor')->name('get-coins-for-sponsor');
Route::post('/get-remainig-days-for-sponsor/', 'Sponsor\CoinsManagement@getremainigdaysForSponsor')->name('get-remainig-days-for-sponsor');
Route::post('/purchased-coins-to-view-report', 'Sponsor\DashboardController@purchasedCoinsToViewReport')->name('purchased-coins-to-view-report');


//Advertisements
Route::get('/data-add/', 'Sponsor\DashboardController@addForm')->name('data-add');
Route::post('/save', 'Sponsor\DashboardController@save')->name('save');
Route::post('/get-credit', 'Sponsor\DashboardController@getCreditKey')->name('get-credit');
Route::get('/edit/{id}', 'Sponsor\DashboardController@edit')->name('edit');

//Gift ProCoins
Route::post('/gift-coins/', 'Sponsor\CoinsManagement@giftcoinstoSchool')->name('gift-coins');
Route::post('/save-coins-data-for-school','Sponsor\CoinsManagement@saveGiftedCoinsDetail')->name('save-coins-data-for-school');
Route::get('/save-coin-package-purchased-data/{p_id}', 'Sponsor\SignupController@saveCoinPurchasedData')->name('save-coin-package');

//Coupon
Route::get('/add-coupon', 'Sponsor\DashboardController@addCoupon')->name('add-coupon');
Route::post('/coupon-bulk-save', 'Sponsor\DashboardController@couponBulkSave')->name('coupon-bulk-save');
Route::get('/edit-coupon/{id}', 'Sponsor\DashboardController@editCoupon')->name('edit-coupon');
Route::post('/save-coupon', 'Sponsor\DashboardController@saveCoupon')->name('save-coupon');
Route::get('/get-coupon-competing/', 'Sponsor\DashboardController@getCouponCompeting');

//My Profile
Route::get('/update-profile', 'Sponsor\UpdateProfileController@updateProfile')->name('update-profile');
Route::post('/save-profile', 'Sponsor\UpdateProfileController@saveProfile')->name('save-profile');

//My Coins
Route::any('/my-coins/', 'Sponsor\CoinsManagement@display')->name('my-coins');
Route::get('/get-transaction/', 'Sponsor\CoinsManagement@getTransaction')->name('get-transaction');
Route::get('/get-consumption/', 'Sponsor\CoinsManagement@getConsumption')->name('get-consumption');
Route::get('/save-coin-purchased-data/{id}', 'Sponsor\CoinsManagement@saveCoinPurchasedData')->name('save-coin-purchased-data');
Route::get('/get-gift-coins/', 'Sponsor\CoinsManagement@getGiftCoins')->name('get-gift-coins');

//Change Password
Route::get('/change-password', 'Sponsor\PasswordController@changePassword')->name('change-password');
Route::post('/update-password', 'Sponsor\PasswordController@updatePassword')->name('update-password');

