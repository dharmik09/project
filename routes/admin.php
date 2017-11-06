<?php

Route::get('/home', 'Admin\DashboardController@index')->name('home');
Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');

//Video Module Routes 
Route::get('/video', 'Admin\VideoManagementController@index')->name('video');
Route::post('/video', 'Admin\VideoManagementController@index')->name('video');
Route::get('/addVideo', 'Admin\VideoManagementController@add')->name('addVideo');
Route::post('saveVideo', 'Admin\VideoManagementController@save')->name('saveVideo');
Route::get('/editVideo/{id}', 'Admin\VideoManagementController@edit')->name('editVideo');
Route::get('/deleteVideo/{id}', 'Admin\VideoManagementController@delete')->name('deleteVideo');

//All Users section
Route::get('/teenagers', 'Admin\TeenagerManagementController@index');
Route::post('/get-teenager', 'Admin\TeenagerManagementController@getIndex');
Route::get('/add-teenager', 'Admin\TeenagerManagementController@add');
Route::get('/view-teenager/{id}', 'Admin\TeenagerManagementController@viewDetail');
Route::post('/get-uniqueid', 'Admin\AjaxController@generateTeenagerUniqueId');
Route::get('/edit-teenager/{id}/{sid}', 'Admin\TeenagerManagementController@edit');
Route::get('/delete-teenager/{id}', 'Admin\TeenagerManagementController@delete');
Route::post('/save-teenager', 'Admin\TeenagerManagementController@save');
Route::get('/add-teenagerbulk', 'Admin\TeenagerManagementController@addbulk');
Route::post('/save-teenagerbulk', 'Admin\TeenagerManagementController@savebulkdata');
Route::post('/map-teenager', 'Admin\TeenagerManagementController@mapteenager');
Route::post('/insert-map-teenager', 'Admin\TeenagerManagementController@insertMapTeenager');
Route::get('/download-excel', 'Admin\TeenagerManagementController@downloadExcel');
Route::get('/export-teenager', 'Admin\TeenagerManagementController@exportData');
Route::get('/clear-cache-teenager','Admin\TeenagerManagementController@clearCache');

//Faq Section
Route::get('/faq', 'Admin\FAQManagementController@index');
Route::post('/faq', 'Admin\FAQManagementController@index');
Route::get('/addFaq', 'Admin\FAQManagementController@add');
Route::post('/saveFaq', 'Admin\FAQManagementController@save');
Route::get('/editFaq/{id}', 'Admin\FAQManagementController@edit');
Route::get('/deleteFaq/{id}', 'Admin\FAQManagementController@delete');

