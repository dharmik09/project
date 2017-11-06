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
Route::get('/export-l4-data/{id}', 'Admin\TeenagerManagementController@exportl4data');

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
Route::get('/coins', 'Admin\CoinsManagementController@index')->name('coins');
Route::post('/coins', 'Admin\CoinsManagementController@index')->name('coins');
Route::get('/addCoins', 'Admin\CoinsManagementController@add')->name('addCoins');
Route::post('/saveCoins', 'Admin\CoinsManagementController@save')->name('saveCoins');
Route::get('/editCoins/{id}', 'Admin\CoinsManagementController@edit')->name('editCoins');
Route::get('/deleteCoins/{id}', 'Admin\CoinsManagementController@delete')->name('deleteCoins');

//Level 1 Section

//Activities
Route::get('/level1Activity', 'Admin\Level1ActivityManagementController@index')->name('level1Activity');
Route::post('/level1Activity', 'Admin\Level1ActivityManagementController@index')->name('level1Activity');
Route::get('/deleteLevel1Activity/{id}', 'Admin\Level1ActivityManagementController@delete')->name('deleteLevel1Activity');
Route::get('/addLevel1Activity', 'Admin\Level1ActivityManagementController@add')->name('addLevel1Activity');
Route::get('/editLevel1Activity/{id}', 'Admin\Level1ActivityManagementController@edit')->name('editLevel1Activity');
Route::post('/saveLevel1Activity', 'Admin\Level1ActivityManagementController@save')->name('saveLevel1Activity');

//Cartoon Icons
Route::get('/cartoons', 'Admin\Level1CartoonIconManagementController@index')->name('cartoons');
Route::post('/cartoons', 'Admin\Level1CartoonIconManagementController@index')->name('cartoons');
Route::get('/deleteCartoon/{id}', 'Admin\Level1CartoonIconManagementController@delete')->name('deleteCartoon');
Route::get('/deleteIcon/{id}', 'Admin\Level1CartoonIconManagementController@deleteusericon')->name('deleteIcon');
Route::get('/addCartoon', 'Admin\Level1CartoonIconManagementController@add')->name('addCartoon');
Route::get('/editCartoon/{id}', 'Admin\Level1CartoonIconManagementController@edit')->name('editCartoon');
Route::post('/saveCartoon', 'Admin\Level1CartoonIconManagementController@save')->name('saveCartoon');
Route::get('/viewUserImage', 'Admin\Level1CartoonIconManagementController@displayimage')->name('viewUserImage');
Route::post('/deleteSelectedIcon', 'Admin\Level1CartoonIconManagementController@deleteSelectedIcon')->name('deleteSelectedIcon');
Route::get('/uploadCartoons', 'Admin\Level1CartoonIconManagementController@uploadView')->name('uploadCartoons');
Route::post('/saveCartoons', 'Admin\Level1CartoonIconManagementController@uploadCartoons')->name('saveCartoons');

//Cartoon Icons Category
Route::get('/cartoonIconsCategory', 'Admin\Level1CartoonIconCategoryManagementController@index')->name('cartoonIconsCategory');
Route::post('/cartoonIconsCategory', 'Admin\Level1CartoonIconCategoryManagementController@index')->name('cartoonIconsCategory');
Route::get('/addCartoonIconsCategory', 'Admin\Level1CartoonIconCategoryManagementController@add')->name('addCartoonIconsCategory');
Route::get('/editCartoonIconCategory/{id}', 'Admin\Level1CartoonIconCategoryManagementController@edit')->name('editCartoonIconCategory');
Route::get('/deleteCartoonIconCategory/{id}', 'Admin\Level1CartoonIconCategoryManagementController@delete')->name('deleteCartoonIconCategory');
Route::post('/saveCartoonIconCategory', 'Admin\Level1CartoonIconCategoryManagementController@save')->name('saveCartoonIconCategory');

//Human Icons Category
Route::get('/humanIconsCategory', 'Admin\Level1HumanIconCategoryManagementController@index')->name('humanIconsCategory');
Route::post('/humanIconsCategory', 'Admin\Level1HumanIconCategoryManagementController@index')->name('humanIconsCategory');
Route::get('/addHumanIconsCategory', 'Admin\Level1HumanIconCategoryManagementController@add')->name('addHumanIconsCategory');
Route::get('/editHumanIconCategory/{id}', 'Admin\Level1HumanIconCategoryManagementController@edit')->name('editHumanIconCategory');
Route::get('/deleteHumanIconCategory/{id}', 'Admin\Level1HumanIconCategoryManagementController@delete')->name('deleteHumanIconCategory');
Route::post('/saveHumanIconCategory', 'Admin\Level1HumanIconCategoryManagementController@save')->name('saveHumanIconCategory');


