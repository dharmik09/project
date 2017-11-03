<?php

Route::get('/home', 'Admin\DashboardController@index')->name('home');
Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard');

//Video Module 
Route::get('/video', 'Admin\VideoManagementController@index')->name('video');
Route::post('/video', 'Admin\VideoManagementController@index')->name('video');
Route::get('/addVideo', 'Admin\VideoManagementController@add')->name('addVideo');
Route::post('saveVideo', 'Admin\VideoManagementController@save')->name('saveVideo');
Route::get('/editVideo/{id}', 'Admin\VideoManagementController@edit')->name('editVideo');
Route::get('/deleteVideo/{id}', 'Admin\VideoManagementController@delete')->name('deleteVideo');

//All Users section
Route::get('/teenagers', 'Admin\TeenagerManagementController@index');
Route::post('/teenagers', 'Admin\TeenagerManagementController@index');

//Faq Section
Route::get('/faq', 'Admin\FAQManagementController@index')->name('faq');
Route::post('/faq', 'Admin\FAQManagementController@index')->name('faq');
Route::get('/addFaq', 'Admin\FAQManagementController@add')->name('addFaq');
Route::post('/saveFaq', 'Admin\FAQManagementController@save')->name('saveFaq');
Route::get('/editFaq/{id}', 'Admin\FAQManagementController@edit')->name('editFaq');
Route::get('/deleteFaq/{id}', 'Admin\FAQManagementController@delete')->name('deleteFaq');

//Settings Section

//Hint Management
Route::get('/listHint', 'Admin\HintManagementController@listhint')->name('listHint');
Route::post('/listHint', 'Admin\HintManagementController@listhint')->name('listHint');
Route::get('/hintLogic','Admin\HintManagementController@hintLogic')->name('hintLogic');
Route::get('/editHintLogic/{id}', 'Admin\HintManagementController@edithintLogic')->name('editHintLogic');
Route::post('/saveHint', 'Admin\HintManagementController@savehint')->name('saveHint');
Route::get('/deleteHint/{id}', 'Admin\HintManagementController@deletehint')->name('deleteHint');

//Cms Management
Route::get('/cms', 'Admin\CMSManagementController@index')->name('cms');
Route::post('/cms', 'Admin\CMSManagementController@index')->name('cms');
Route::get('/deleteCms/{id}', 'Admin\CMSManagementController@delete')->name('deleteCms');
Route::get('/addCms', 'Admin\CMSManagementController@add')->name('addCms');
Route::post('/saveCms', 'Admin\CMSManagementController@save')->name('saveCms');
Route::get('/editCms/{id}', 'Admin\CMSManagementController@edit')->name('editCms');

//Email Template Management
Route::get('/templates', 'Admin\TemplateManagementController@index')->name('templates');
Route::post('/templates', 'Admin\TemplateManagementController@index')->name('templates');
Route::get('/deleteTemplate/{id}', 'Admin\TemplateManagementController@delete')->name('deleteTemplate');
Route::get('/addTemplate', 'Admin\TemplateManagementController@add')->name('addTemplate');
Route::post('/saveTemplate', 'Admin\TemplateManagementController@save')->name('saveTemplate');
Route::get('/editTemplate/{id}', 'Admin\TemplateManagementController@edit')->name('editTemplate');

//Configuration Management
Route::get('/configurations', 'Admin\ConfigurationManagementController@index')->name('configuration');
Route::post('/configurations', 'Admin\ConfigurationManagementController@index')->name('configuration');
Route::get('/addConfiguration', 'Admin\ConfigurationManagementController@add')->name('addConfiguration');
Route::post('/saveConfiguration', 'Admin\ConfigurationManagementController@save')->name('saveConfiguration');
Route::get('/editConfiguration/{id}', 'Admin\ConfigurationManagementController@edit')->name('editConfiguration');

//Generic Ads
Route::get('/genericAds', 'Admin\GenericAdsManagementController@index')->name('genericAds');
Route::post('/genericAds', 'Admin\GenericAdsManagementController@index')->name('genericAds');
Route::get('/addGeneric', 'Admin\GenericAdsManagementController@add')->name('addGeneric');
Route::get('/editGeneric/{id}', 'Admin\GenericAdsManagementController@edit')->name('editGeneric');
Route::post('/saveGeneric', 'Admin\GenericAdsManagementController@save')->name('saveGeneric');
Route::get('/deleteGeneric/{id}', 'Admin\GenericAdsManagementController@delete')->name('deleteGeneric');

//ProCoin Packages
Route::get('/coins', 'Admin\CoinsManagementController@index');
Route::post('/coins', 'Admin\CoinsManagementController@index');
Route::get('/addCoins', 'Admin\CoinsManagementController@add');
Route::post('/saveCoins', 'Admin\CoinsManagementController@save');
Route::get('/editCoins/{id}', 'Admin\CoinsManagementController@edit');
Route::get('/deleteCoins/{id}', 'Admin\CoinsManagementController@delete');


