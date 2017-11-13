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

//All Users Section
//Teenager
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
Route::post('/add-coins-data-for-teenager','Admin\TeenagerManagementController@addCoinsDataForTeenager');
Route::post('/save-coins-data-for-teenager','Admin\TeenagerManagementController@saveCoinsDataForTeen');

//Parents
Route::get('/parents/{type}', 'Admin\ParentManagementController@index');
Route::post('/parents/{type}', 'Admin\ParentManagementController@index');
Route::get('/delete-parent/{id}/{type}', 'Admin\ParentManagementController@delete');
Route::get('/add-parent', 'Admin\ParentManagementController@add');
Route::post('/save-parent', 'Admin\ParentManagementController@save');
Route::get('/edit-parent/{id}', 'Admin\ParentManagementController@edit');
Route::get('/counselors/{type}', 'Admin\ParentManagementController@index');
Route::post('/counselors/{type}', 'Admin\ParentManagementController@index');
Route::get('/view-parentteen/{id}', 'Admin\ParentManagementController@viewparentteen');
Route::get('/export-parent/{type}', 'Admin\ParentManagementController@exportparent');
Route::post('/add-coins-data-for-parent','Admin\ParentManagementController@addCoinsDataForParent');
Route::post('/save-coins-data-for-parent','Admin\ParentManagementController@saveCoinsDataForParent');

//Mentors
Route::get('admin/counselors/{type}', 'Admin\ParentManagementController@index');
Route::post('admin/counselors/{type}', 'Admin\ParentManagementController@index');

//Schools
Route::get('/schools', 'Admin\SchoolManagementController@index');
Route::post('/get-school', 'Admin\SchoolManagementController@getIndex');
Route::post('/schools', 'Admin\SchoolManagementController@index');
Route::get('/delete-school/{id}', 'Admin\SchoolManagementController@delete');
Route::get('/add-school', 'Admin\SchoolManagementController@add');
Route::post('/save-school', 'Admin\SchoolManagementController@save');
Route::get('/edit-school/{id}', 'Admin\SchoolManagementController@edit');
Route::get('/view-student-list/{id}', 'Admin\SchoolManagementController@getStudentDetail');
Route::post('/view-student-list/{id}', 'Admin\SchoolManagementController@getStudentDetail');
Route::get('/edit-school-approved/{id}', 'Admin\SchoolManagementController@editToApproved');
Route::post('/add-coins-data-for-school','Admin\SchoolManagementController@addCoinsDataForSchool');
Route::post('/save-coins-data-for-school','Admin\SchoolManagementController@saveCoinsDataForSchool');
Route::get('/export-school', 'Admin\SchoolManagementController@exportschool');

//Enterprise section
Route::get('/sponsors', 'Admin\SponsorManagementController@index');
Route::post('/sponsors', 'Admin\SponsorManagementController@index');
Route::get('/delete-sponsor/{id}', 'Admin\SponsorManagementController@delete');
Route::get('/add-sponsor', 'Admin\SponsorManagementController@add');
Route::post('/save-sponsor', 'Admin\SponsorManagementController@save');
Route::get('/edit-sponsor/{id}', 'Admin\SponsorManagementController@edit');
Route::get('/edit-approved/{id}', 'Admin\SponsorManagementController@editToApproved');
Route::get('/sponsor-activity/{id}', 'Admin\SponsorManagementController@viewSponsorActivity');
Route::post('/sponsor-activity/{id}', 'Admin\SponsorManagementController@viewSponsorActivity');
Route::get('/view-sponsor-activity/{id}', 'Admin\SponsorManagementController@sponsorActivity');
Route::get('/edit-sponsor-activity/{id}', 'Admin\SponsorManagementController@editSponsorActivity');
Route::post('/save-sponsor-activity', 'Admin\SponsorManagementController@saveSponsorActivity');
Route::post('/add-coins-data-for-sponsor','Admin\SponsorManagementController@addCoinsDataForSponsor');
Route::post('/save-coins-data-for-sponsor','Admin\SponsorManagementController@saveCoinsDataForSponsor');
Route::get('/export-sponsor', 'Admin\SponsorManagementController@exportsponsor');

//Enterprise Coupons //Coupons
Route::get('/coupons', 'Admin\CouponManagementController@index');
Route::post('/coupons', 'Admin\CouponManagementController@index');
Route::get('/delete-coupon/{id}', 'Admin\CouponManagementController@delete');
Route::get('/add-coupon', 'Admin\CouponManagementController@add');
Route::get('/edit-coupon/{id}', 'Admin\CouponManagementController@edit');
Route::post('/save-coupon', 'Admin\CouponManagementController@save');
Route::get('/add-coupon-bulk', 'Admin\CouponManagementController@addbulk');
Route::post('/save-coupon-bulk', 'Admin\CouponManagementController@savebulkdata');
Route::get('/coupon-usage/{id}', 'Admin\CouponManagementController@couponUsage');

//Notification Section
Route::get('/notification', 'Admin\NotificationController@index');
Route::post('/send-notification', 'Admin\NotificationController@sendNotification');
Route::post('/get-notification', 'Admin\NotificationController@getIndex');

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

//Human Icon
Route::get('/humanIcons', 'Admin\Level1HumanIconManagementController@index')->name('humanIcons');
Route::post('/humanIcons', 'Admin\Level1HumanIconManagementController@index')->name('humanIcons');
Route::get('/deleteHumanIcon/{id}', 'Admin\Level1HumanIconManagementController@delete')->name('deleteHumanIcon');
Route::get('/addHumanIcon', 'Admin\Level1HumanIconManagementController@add')->name('addHumanIcon');
Route::get('/editHumanIcon/{id}', 'Admin\Level1HumanIconManagementController@edit')->name('editHumanIcon');
Route::post('/saveHumanIcon', 'Admin\Level1HumanIconManagementController@save')->name('saveHumanIcon');
Route::get('/viewHumanUserImage', 'Admin\Level1HumanIconManagementController@displayimage')->name('viewHumanUserImage');
Route::get('/deleteUserHumanIcon/{id}', 'Admin\Level1HumanIconManagementController@deletehumaniconuploadedbyuser')->name('deleteUserHumanIcon');
Route::post('/deleteHumanIcon', 'Admin\Level1HumanIconManagementController@deleteHumanIcon')->name('deleteHumanIcon');
Route::get('/uploadHumanIcons', 'Admin\Level1HumanIconManagementController@uploadView');
Route::post('/saveHumanIcons', 'Admin\Level1HumanIconManagementController@uploadHumanIcons');

//Level 2 Section

//Activities
Route::get('/level2Activity', 'Admin\Level2ActivityManagementController@index')->name('level2Activity');
Route::post('/level2Activity', 'Admin\Level2ActivityManagementController@index')->name('level2Activity');
Route::get('/deleteLevel2Activity/{id}', 'Admin\Level2ActivityManagementController@delete')->name('deleteLevel2Activity');
Route::get('/addLevel2Activity', 'Admin\Level2ActivityManagementController@add')->name('addLevel2Activity');
Route::get('/editLevel2Activity/{id}', 'Admin\Level2ActivityManagementController@edit')->name('editLevel2Activity');
Route::post('/saveLevel2Activity', 'Admin\Level2ActivityManagementController@save')->name('saveLevel2Activity');

//Level 3 Section

//Baskets
Route::get('/baskets', 'Admin\BasketManagementController@index');
Route::post('/baskets', 'Admin\BasketManagementController@index');
Route::get('/deleteBasket/{id}', 'Admin\BasketManagementController@delete');
Route::get('/addBasket', 'Admin\BasketManagementController@add');
Route::get('/editBasket/{id}', 'Admin\BasketManagementController@edit');
Route::post('/saveBasket', 'Admin\BasketManagementController@save');

//Professions
Route::get('/professions', 'Admin\ProfessionManagementController@index');
Route::post('/professions', 'Admin\ProfessionManagementController@index');
Route::get('/deleteProfession/{id}', 'Admin\ProfessionManagementController@delete');
Route::get('/addProfession', 'Admin\ProfessionManagementController@add');
Route::post('/saveProfession', 'Admin\ProfessionManagementController@save');
Route::get('/editProfession/{id}', 'Admin\ProfessionManagementController@edit');
Route::get('/addProfessionBulk', 'Admin\ProfessionManagementController@addbulk');
Route::post('/saveProfessionBulk', 'Admin\ProfessionManagementController@saveprofessionbulk');
Route::get('/exportProfessoin', 'Admin\ProfessionManagementController@exportData');
Route::post('/getUserCompetitorsData','Admin\ProfessionManagementController@getUserCompetitorsData');
Route::get('/exportCompetitors/{id}', 'Admin\ProfessionManagementController@exportCompetotorsData');

//Profession Headers
Route::get('/headers', 'Admin\ProfessionHeadersManagementController@index')->name('headers');
Route::post('/headers', 'Admin\ProfessionHeadersManagementController@index')->name('headers');
//Route::get('/headers/{page}', 'Admin\ProfessionHeadersManagementController@index')->name('');
//Route::post('/headers/{page}', 'Admin\ProfessionHeadersManagementController@index')->name('');
Route::get('/addHeader', 'Admin\ProfessionHeadersManagementController@add')->name('addHeader');
Route::get('/editHeader/{id}', 'Admin\ProfessionHeadersManagementController@edit')->name('editHeader');
Route::get('/deleteHeader/{id}', 'Admin\ProfessionHeadersManagementController@delete')->name('deleteHeader');
Route::post('/saveHeader', 'Admin\ProfessionHeadersManagementController@save')->name('saveHeader');

//Career HML Mapping
Route::get('/careerMapping', 'Admin\CareerMappingManagementController@index')->name('careerMapping');
Route::post('/careerMapping', 'Admin\CareerMappingManagementController@index')->name('careerMapping');
Route::get('/addCareerMapping', 'Admin\CareerMappingManagementController@add')->name('addCareerMapping');
Route::post('/saveAddCareerMapping', 'Admin\CareerMappingManagementController@save')->name('saveAddCareerMapping');
Route::get('/editCareerMapping/{id}','Admin\CareerMappingManagementController@edit')->name('editCareerMapping');
Route::get('/importExcel','Admin\CareerMappingManagementController@importExcel');
Route::post('/addImportExcel', 'Admin\CareerMappingManagementController@addimportExcel');

//Paid Components Section

//Paid Components
Route::get('/paidComponents', 'Admin\PaidComponentsManagementController@index')->name('paidComponents');
Route::post('/paidComponents', 'Admin\PaidComponentsManagementController@index')->name('paidComponents');
Route::get('/addPaidComponenets', 'Admin\PaidComponentsManagementController@add')->name('addPainComponenets');
Route::post('/savePaidComponents', 'Admin\PaidComponentsManagementController@save')->name('savePaidComponents');
Route::get('/editPaidComponents/{id}', 'Admin\PaidComponentsManagementController@edit')->name('editPaidComponents');
Route::get('/deletePaidComponents/{id}', 'Admin\PaidComponentsManagementController@delete')->name('deletePaidComponents');

//Invoice
Route::get('/invoice', 'Admin\InvoiceManagementController@index');
Route::post('/invoice', 'Admin\InvoiceManagementController@index');
Route::get('/viewInvoice/{transId}', 'Admin\CoinsManagementController@viewInvoiceData');
Route::get('/sendEmailForInvoice/{transId}', 'Admin\CoinsManagementController@sendEmailForInvoice');
Route::post('/printInvoice', 'Admin\CoinsManagementController@printInvoice');



