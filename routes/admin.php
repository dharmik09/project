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
Route::get('/view-teenager/{id}/{type}', 'Admin\TeenagerManagementController@viewDetail');
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
Route::post('/sendNotification', 'Admin\NotificationController@sendNotificationToTeen');
Route::post('/getNotification', 'Admin\NotificationController@getIndex');

//Level 4 Section
Route::get('/level4Activity', 'Admin\Level4ActivityManagementController@index');
Route::post('/level4Activity', 'Admin\Level4ActivityManagementController@index');
Route::post('/get-level4-activity', 'Admin\Level4ActivityManagementController@getIndex');
Route::get('/addLevel4Activity', 'Admin\Level4ActivityManagementController@add');
Route::get('/deleteLevel4Activity/{id}', 'Admin\Level4ActivityManagementController@delete');
Route::get('/editLeve4Activity/{id}', 'Admin\Level4ActivityManagementController@edit');
Route::post('/saveLevel4Activity', 'Admin\Level4ActivityManagementController@save');
Route::get('/addLevel4QuestionBulk', 'Admin\Level4ActivityManagementController@addbulk');
Route::post('/saveLevel4QuestionBulk', 'Admin\Level4ActivityManagementController@saveLevel4QuestionBulk');
Route::get('/saveLevel4QuestionBulk', 'Admin\Level4ActivityManagementController@addbulk');

Route::post('/saveGamificationTemplate/', 'Admin\Level4TemplateManagementController@save');
Route::get('/listGamificationTemplate/', 'Admin\Level4TemplateManagementController@index');
Route::post('/listGamificationTemplate/', 'Admin\Level4TemplateManagementController@index');
Route::post('/getGamificationTemplateList/', 'Admin\Level4TemplateManagementController@getIndex');
Route::get('/editGamificationTemplate/{id}', 'Admin\Level4TemplateManagementController@edit');
Route::get('/deleteGamificationTemplate/{id}', 'Admin\Level4TemplateManagementController@delete');
Route::post('/getGamificationTemplateAnswerBox/', 'Admin\Level4TemplateManagementController@getGamificationTemplateAnswerBox');
Route::post('/addCoinsDataForTemplate','Admin\Level4TemplateManagementController@addCoinsDataForTemplate');
Route::post('/saveCoinsDataForTemplate','Admin\Level4TemplateManagementController@saveCoinsDataForTemplate');
Route::get('/addLevel4Template', 'Admin\Level4TemplateManagementController@add');

Route::get('/listLevel4IntermediateActivity/', 'Admin\Level4IntermediateActivityManagementController@index');
Route::post('/getListLevel4IntermediateActivity/', 'Admin\Level4IntermediateActivityManagementController@getIndex');
Route::post('/listLevel4IntermediateActivity/', 'Admin\Level4IntermediateActivityManagementController@index');
Route::get('/addIntermediateActivity/', 'Admin\Level4IntermediateActivityManagementController@add');
Route::get('/editlevel4IntermediateActivity/{id}', 'Admin\Level4IntermediateActivityManagementController@edit');
Route::post('/savelevel4Intermediateactivity/', 'Admin\Level4IntermediateActivityManagementController@save');
Route::get('/deleteAudioPopupImage/{id}/{filename}/{type}', 'Admin\Level4IntermediateActivityManagementController@deleteAudioPopupImage');
Route::get('/manageIntermediateActivityAnswer/{id}', 'Admin\Level4IntermediateActivityManagementController@manageIntermediateActivityAnswer');
Route::post('/updatelevel4IntermediateOption/', 'Admin\Level4IntermediateActivityManagementController@updatelevel4IntermediateOption');
Route::get('/manageIntermediateActivityMedia/{id}', 'Admin\Level4IntermediateActivityManagementController@manageActivityMedia');
Route::post('/savelevel4IntermediateMedia/', 'Admin\Level4IntermediateActivityManagementController@savelevel4IntermediateMedia');
Route::post('/deleteLevel4IntermediateMediaById/', 'Admin\Level4IntermediateActivityManagementController@deleteLevel4IntermediateMediaById');
Route::get('/deletelevel4IntermediateActivity/{id}', 'Admin\Level4IntermediateActivityManagementController@delete');

Route::get('/listlevel4advanceactivity', 'Admin\Level4AdvanceActivityManagementController@index');
Route::post('/listlevel4advanceactivity', 'Admin\Level4AdvanceActivityManagementController@index');
Route::get('/editlevel4advanceactivity/{id}', 'Admin\Level4AdvanceActivityManagementController@edit');
Route::get('/deletelevel4advanceactivity/{id}', 'Admin\Level4AdvanceActivityManagementController@delete');
Route::post('/savelevel4advanceactivity', 'Admin\Level4AdvanceActivityManagementController@savelevel4advanceactivity');
Route::get('/level4advanceactivity', 'Admin\Level4AdvanceActivityManagementController@add');
Route::get('/level4AdvanceActivityUserTask', 'Admin\Level4AdvanceActivityManagementController@level4AdvanceActivityUserTask');
Route::post('/level4AdvanceActivityUserTask', 'Admin\Level4AdvanceActivityManagementController@level4AdvanceActivityUserTask');
Route::get('/viewUserAllAdvanceActivities/{teenager}/{profession}/{type}', 'Admin\Level4AdvanceActivityManagementController@viewUserAllAdvanceActivities');
Route::post('/verifyUserAdvanceTask/', 'Admin\Level4AdvanceActivityManagementController@verifyUserAdvanceTask');
Route::post('/deleteUserAdvanceTask/', 'Admin\Level4AdvanceActivityManagementController@deleteUserAdvanceTask');

//Advance Parent Task
Route::get('/level4AdvanceActivityParentTask', 'Admin\Level4AdvanceActivityManagementController@level4AdvanceActivityParentTask');
Route::post('/level4AdvanceActivityParentTask', 'Admin\Level4AdvanceActivityManagementController@level4AdvanceActivityParentTask');
Route::get('/viewParentAllAdvanceActivities/{parent}/{profession}/{type}', 'Admin\Level4AdvanceActivityManagementController@viewParentAllAdvanceActivities');
Route::post('/verifyParentAdvanceTask/', 'Admin\Level4AdvanceActivityManagementController@verifyParentAdvanceTask');
Route::post('/deleteParentAdvanceTask/', 'Admin\Level4AdvanceActivityManagementController@deleteParentAdvanceTask');

//Learning Guidance
Route::get('/level4LearningStyle', 'Admin\LearningStyleManagementController@index')->name('level4LearningStyle');
Route::post('/level4LearningStyle', 'Admin\LearningStyleManagementController@index')->name('level4LearningStyle');
Route::get('/addLearningStyle', 'Admin\LearningStyleManagementController@add')->name('addLearningStyle');
Route::post('/saveLearningStyle', 'Admin\LearningStyleManagementController@saveLearningStyle')->name('saveLearningStyle');
Route::get('/editLearningStyle/{id}', 'Admin\LearningStyleManagementController@editLearningStyle')->name('editLearningStyle');

//Profession Learning Guidance
Route::get('/professionLearningStyle', 'Admin\ProfessionLearningStyleManagementController@index');
Route::post('/professionLearningStyle', 'Admin\ProfessionLearningStyleManagementController@index');
Route::get('/addProfessionLeaningStyle', 'Admin\ProfessionLearningStyleManagementController@add');
Route::get('/editProfessionLearningStyle/{id}', 'Admin\ProfessionLearningStyleManagementController@edit');
Route::post('/saveProfessionLearningStyle', 'Admin\ProfessionLearningStyleManagementController@save');
Route::get('/importLearningStyle','Admin\ProfessionLearningStyleManagementController@importExcel');
Route::post('/addLeaningStyleImportExcel','Admin\ProfessionLearningStyleManagementController@addimportExcel');


//PRO-VERSION
Route::get('/copyConcept', 'Admin\Level4TemplateManagementController@copyConcept');
Route::post('/saveCopyConcept', 'Admin\Level4TemplateManagementController@saveCopyConcept');
Route::post('/getProfessionConcepts', 'Admin\ReportController@getProfessionConcepts');

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
Route::post('/bulkDeleteCartoonIcons', 'Admin\Level1CartoonIconManagementController@bulkDeleteCartoonIcons');

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
Route::post('/bulkDeleteHumanIcons', 'Admin\Level1HumanIconManagementController@bulkDeleteHumanIcons');

//Traits
Route::get('/level1Traits', 'Admin\Level1TraitsManagementController@index')->name('level1Traits');
Route::get('/deleteLevel1Traits/{id}', 'Admin\Level1TraitsManagementController@delete')->name('deleteLevel1Traits');
Route::get('/addLevel1Traits', 'Admin\Level1TraitsManagementController@add')->name('addLevel1Traits');
Route::get('/editLevel1Traits/{id}', 'Admin\Level1TraitsManagementController@edit')->name('editLevel1Traits');
Route::post('/saveLevel1Traits', 'Admin\Level1TraitsManagementController@save')->name('saveLevel1Traits');

//Level 2 Section

//Activities
Route::get('/level2Activity', 'Admin\Level2ActivityManagementController@index')->name('level2Activity');
Route::post('/level2Activity', 'Admin\Level2ActivityManagementController@index')->name('level2Activity');
Route::get('/deleteLevel2Activity/{id}', 'Admin\Level2ActivityManagementController@delete')->name('deleteLevel2Activity');
Route::get('/addLevel2Activity', 'Admin\Level2ActivityManagementController@add')->name('addLevel2Activity');
Route::get('/editLevel2Activity/{id}', 'Admin\Level2ActivityManagementController@edit')->name('editLevel2Activity');
Route::post('/saveLevel2Activity', 'Admin\Level2ActivityManagementController@save')->name('saveLevel2Activity');

//L2 School Activity
Route::get('/schoolLevel2Activity', 'Admin\Level2ActivityManagementController@schoolLevel2Activity')->name('schoolLevel2Activity');
Route::post('/schoolLevel2Activity', 'Admin\Level2ActivityManagementController@schoolLevel2Activity')->name('schoolLevel2Activity');
Route::post('/searchSchoolLevel2Activity', 'Admin\Level2ActivityManagementController@searchSchoolLevel2Activity');

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
Route::post('/exportProfessoin', 'Admin\ProfessionManagementController@exportData');
Route::get('/exportProfessoinCountrySelection', 'Admin\ProfessionManagementController@exportDataCountrySelection');
Route::post('/getUserCompetitorsData','Admin\ProfessionManagementController@getUserCompetitorsData');
Route::get('/exportCompetitors/{id}', 'Admin\ProfessionManagementController@exportCompetotorsData');
Route::get('/addProfessionWiseCertificationBulk', 'Admin\ProfessionManagementController@professionWiseCertificationAddBulk');
Route::post('/saveProfessionWiseCertificationBulk', 'Admin\ProfessionManagementController@professionWiseCertificationSaveBulk');
Route::get('/addProfessionWiseSubjectBulk', 'Admin\ProfessionManagementController@professionWiseSubjectAddBulk');
Route::post('/saveProfessionWiseSubjectBulk', 'Admin\ProfessionManagementController@professionWiseSubjectSaveBulk');
Route::get('/addProfessionWiseTagBulk', 'Admin\ProfessionManagementController@professionWiseTagAddBulk');
Route::post('/saveProfessionWiseTagBulk', 'Admin\ProfessionManagementController@professionWiseTagSaveBulk');
Route::post('/getProfessions', 'Admin\ProfessionManagementController@listWithAjax');

//Profession Headers
Route::get('/headers', 'Admin\ProfessionHeadersManagementController@index')->name('headers');
Route::post('/headers', 'Admin\ProfessionHeadersManagementController@index')->name('headers');
//Route::get('/headers/{page}', 'Admin\ProfessionHeadersManagementController@index')->name('');
//Route::post('/headers/{page}', 'Admin\ProfessionHeadersManagementController@index')->name('');
Route::get('/addHeader', 'Admin\ProfessionHeadersManagementController@add')->name('addHeader');
Route::get('/editHeader/{id}/{countryId}', 'Admin\ProfessionHeadersManagementController@edit')->name('editHeader');
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

//Reports Section

//Teens
Route::get('/userReport/', 'Admin\ReportController@userReport');
Route::post('/userReport/', 'Admin\ReportController@userReport');

//School
Route::get('/schoolReport/', 'Admin\ReportController@schoolReport');
Route::post('/schoolReport/', 'Admin\ReportController@schoolReport');
Route::post('/getClass/', 'Admin\ReportController@getClass');

//Level1 Survey
Route::get('/level1Chart/', 'Admin\ReportController@level1');
Route::post('/level1Chart/', 'Admin\ReportController@level1');

//Level1 Icon Report
Route::get('/iconReport/', 'Admin\ReportController@iconReport');
Route::post('/iconReport/', 'Admin\ReportController@iconReport');

//Level1 Quality Report
Route::get('/iconQualityReport/', 'Admin\ReportController@iconQualityReport');
Route::post('/iconQualityReport/', 'Admin\ReportController@iconQualityReport');

//Level2 Chart
Route::get('/level2Chart/', 'Admin\ReportController@level2');
Route::post('/level2Chart/', 'Admin\ReportController@level2');

//Teen Promise
Route::get('/userApi/', 'Admin\ReportController@getuserapiscore');
Route::post('/userApi/', 'Admin\ReportController@getuserapiscore');

//Level3
Route::get('/level3Report/', 'Admin\ReportController@level3Report');
Route::post('/level3Report/', 'Admin\ReportController@level3Report');

//Level4 Basic Report
Route::get('/level4BasicReport/', 'Admin\ReportController@level4BasicReport');
Route::post('/level4BasicReport/', 'Admin\ReportController@level4BasicReport');

//Level4 Intermediate Report
Route::get('/level4IntermediateReport/', 'Admin\ReportController@level4IntermediateReport');
Route::post('/level4IntermediateReport/', 'Admin\ReportController@level4IntermediateReport');
Route::post('/getProfessionConcepts/', 'Admin\ReportController@getProfessionConcepts');

//Level4 Advance Report
Route::get('/level4AdvanceReport/', 'Admin\ReportController@level4AdvanceReport');
Route::post('/level4AdvanceReport/', 'Admin\ReportController@level4AdvanceReport');

//Testimonial
Route::get('/testimonials', 'Admin\TestimonialManagementController@index')->name('testinomials');
Route::post('/testimonials', 'Admin\TestimonialManagementController@index')->name('testinomials');
Route::get('/addTestimonial', 'Admin\TestimonialManagementController@add')->name('addTestinomial');
Route::post('/saveTestimonial', 'Admin\TestimonialManagementController@save')->name('saveTestinomial');
Route::get('/editTestimonial/{id}', 'Admin\TestimonialManagementController@edit')->name('editTestinomial');
Route::get('/deleteTestimonial/{id}', 'Admin\TestimonialManagementController@delete')->name('deleteTestinomial');

//Helptext
Route::get('/helpText', 'Admin\HelpTextManagementController@index')->name('helpText');
Route::post('/helpText', 'Admin\HelpTextManagementController@index')->name('helpText');
Route::get('/addHelpText', 'Admin\HelpTextManagementController@add')->name('addHelpText');
Route::post('/saveHelpText', 'Admin\HelpTextManagementController@save')->name('saveHelpText');
Route::get('/editHelpText/{id}', 'Admin\HelpTextManagementController@edit')->name('editHelpText');
Route::get('/deleteHelpText/{id}', 'Admin\HelpTextManagementController@delete')->name('deleteHelpText');

//Profession Certifications
Route::get('/professionCertifications', 'Admin\CertificationManagementController@index')->name('helpText');
Route::post('/professionCertifications', 'Admin\CertificationManagementController@index')->name('helpText');
Route::get('/addProfessionCertification', 'Admin\CertificationManagementController@add')->name('addHelpText');
Route::post('/saveProfessionCertification', 'Admin\CertificationManagementController@save')->name('saveHelpText');
Route::get('/editProfessionCertification/{id}', 'Admin\CertificationManagementController@edit')->name('editHelpText');
Route::get('/deleteProfessionCertification/{id}', 'Admin\CertificationManagementController@delete')->name('deleteHelpText');

//Profession Wise Certifications
Route::get('/professionWiseCertifications', 'Admin\CertificationManagementController@professionWiseCertificationIndex')->name('pwc_index');
Route::get('/addProfessionWiseCertification', 'Admin\CertificationManagementController@professionWiseCertificationAdd')->name('pwc_add');
Route::post('/saveProfessionWiseCertification', 'Admin\CertificationManagementController@professionWiseCertificationSave')->name('pwc_save');
// Route::post('/saveProfessionWiseCertificationBulk', 'Admin\CertificationManagementController@professionWiseCertificationSaveBulk')->name('pwc_add_bulk');
Route::get('/editProfessionWiseCertification/{id}', 'Admin\CertificationManagementController@professionWiseCertificationEdit')->name('pwc_edit');
Route::get('/deleteProfessionWiseCertification/{id}', 'Admin\CertificationManagementController@professionWiseCertificationDelete')->name('pwc_delete');

//Profession Subjects
Route::get('/professionSubjects', 'Admin\ProfessionSubjectManagementController@index')->name('professionSubjects');
Route::post('/professionSubjects', 'Admin\ProfessionSubjectManagementController@index')->name('professionSubjects');
Route::get('/addProfessionSubject', 'Admin\ProfessionSubjectManagementController@add')->name('addProfessionSubject');
Route::post('/saveProfessionSubject', 'Admin\ProfessionSubjectManagementController@save')->name('saveProfessionSubject');
Route::get('/editProfessionSubject/{id}', 'Admin\ProfessionSubjectManagementController@edit')->name('editProfessionSubject');
Route::get('/deleteProfessionSubject/{id}', 'Admin\ProfessionSubjectManagementController@delete')->name('deleteProfessionSubject');

//Profession Tags
Route::get('/professionTags', 'Admin\ProfessionTagManagementController@index');
Route::get('/addProfessionTag', 'Admin\ProfessionTagManagementController@add');
Route::post('/saveProfessionTag', 'Admin\ProfessionTagManagementController@save');
Route::get('/editProfessionTag/{id}', 'Admin\ProfessionTagManagementController@edit');
Route::get('/deleteProfessionTag/{id}', 'Admin\ProfessionTagManagementController@delete');

//App Version
Route::get('/appVersions', 'Admin\AppVersionManagementController@index');
Route::get('/addAppVersion', 'Admin\AppVersionManagementController@add');
Route::post('/saveAppVersion', 'Admin\AppVersionManagementController@save');
Route::get('/editAppVersion/{id}', 'Admin\AppVersionManagementController@edit');
Route::get('/deleteAppVersion/{id}', 'Admin\AppVersionManagementController@delete');

//Forum
Route::get('/forumQuestions', 'Admin\ForumQuestionManagementController@index');
Route::get('/forumAnswer/{queId}', 'Admin\ForumQuestionManagementController@getForumAnswer');
Route::get('/addForumQuestion', 'Admin\ForumQuestionManagementController@add');
Route::post('/saveForumQuestion', 'Admin\ForumQuestionManagementController@save');
Route::get('/editForumQuestion/{id}', 'Admin\ForumQuestionManagementController@edit');
Route::get('/deleteForumQuestion/{id}', 'Admin\ForumQuestionManagementController@delete');
Route::get('/changeanswerstatus/{ansId}/{status}', 'Admin\ForumQuestionManagementController@changeAnswerStatus');

//School import
Route::get('/professionInstitute', 'Admin\ProfessionManagementController@professionInstitutes');
Route::post('/getProfessionInstitute', 'Admin\ProfessionManagementController@getProfessionInstitutesListAjax');
Route::get('/addProfessionInstituteCourseList', 'Admin\ProfessionManagementController@professionInstitutesListAdd');
Route::post('/saveProfessionInstituteCourseList', 'Admin\ProfessionManagementController@professionInstitutesListSave');
Route::post('/saveProfessionInstitutePhoto', 'Admin\ProfessionManagementController@professionInstitutesPhotoUpdate');
Route::get('/professionInstituteUpload/{uploadType}', 'Admin\ProfessionManagementController@professionInstitutesArtisanUpload');
Route::get('/deleteallprofessioninstitute', 'Admin\ProfessionManagementController@deleteAllProfessionInstitutes');
Route::get('/exportInstitute', 'Admin\ProfessionManagementController@exportInstitute');
Route::get('/updateeducationspeciality', 'Admin\ProfessionManagementController@updateEducationSpeciality');
