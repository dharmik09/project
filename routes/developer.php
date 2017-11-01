<?php

Route::get('/home', function () {
    $users[] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('developer')->user();

    //dd($users);

    return view('developer.home');
})->name('home');

/* System Level Module Routes */
Route::get('/systemLevel', 'Developer\SystemLevelManagementController@index')->name('systemLevel');
Route::post('/systemLevel', 'Developer\SystemLevelManagementController@index')->name('systemLevel');
Route::get('/addSystemLevel', 'Developer\SystemLevelManagementController@add')->name('addSystemLevel');
Route::get('/editSystemLevel/{id}', 'Developer\SystemLevelManagementController@edit')->name('editSystemLevel');
Route::get('/deleteSystemLevel/{id}', 'Developer\SystemLevelManagementController@delete')->name('deleteSystemLevel');
Route::post('/saveSystemLevel', 'Developer\SystemLevelManagementController@save')->name('saveSystemLevel');

/* Apptitude Type Module Routes */
Route::get('/apptitudeType', 'Developer\ApptitudeTypeManagementController@index')->name('apptitudeType');
Route::post('/apptitudeType', 'Developer\ApptitudeTypeManagementController@index')->name('apptitudeType');
Route::get('/addApptitudeType', 'Developer\ApptitudeTypeManagementController@add')->name('addApptitudeType');
Route::get('/editApptitudeType/{id}', 'Developer\ApptitudeTypeManagementController@edit')->name('editApptitudeType');
Route::get('/deleteApptitudeType/{id}', 'Developer\ApptitudeTypeManagementController@delete')->name('deleteApptitudeType');
Route::post('/saveApptitudeType', 'Developer\ApptitudeTypeManagementController@save')->name('saveApptitudeType');

/* Personality Type Module Routes */
Route::get('/personalityType', 'Developer\PersonalityTypeManagementController@index')->name('personalityType');
Route::post('/personalityType', 'Developer\PersonalityTypeManagementController@index')->name('personalityType');
Route::get('/addPersonalityType', 'Developer\PersonalityTypeManagementController@add')->name('addPersonalityType');
Route::get('/editPersonalityType/{id}', 'Developer\PersonalityTypeManagementController@edit')->name('editPersonalityType');
Route::post('/savePersonalityType', 'Developer\PersonalityTypeManagementController@save')->name('savePersonalityType');
Route::get('/deletePersonalityType/{id}', 'Developer\PersonalityTypeManagementController@delete')->name('deletePersonalityType');

/* Multiple Intelligence Type Module Routes */
Route::get('/multipleintelligenceType', 'Developer\MultipleIntelligenceTypeManagementController@index')->name('multipleintelligenceType');
Route::post('/multipleintelligenceType', 'Developer\MultipleIntelligenceTypeManagementController@index')->name('multipleintelligenceType');
Route::get('/addMultipleintelligenceType', 'Developer\MultipleIntelligenceTypeManagementController@add')->name('addMultipleintelligenceType');
Route::get('/editMultipleintelligenceType/{id}', 'Developer\MultipleIntelligenceTypeManagementController@edit')->name('editMultipleintelligenceType');
Route::post('/saveMultipleintelligenceType', 'Developer\MultipleIntelligenceTypeManagementController@save')->name('saveMultipleintelligenceType');
Route::get('/deleteMultipleintelligenceType/{id}', 'Developer\MultipleIntelligenceTypeManagementController@delete')->name('deleteMultipleintelligenceType');

/* Interest Type Module Routes */
Route::get('/interestType', 'Developer\InterestTypeManagementController@index')->name('interestType');
Route::post('/interestType', 'Developer\InterestTypeManagementController@index')->name('interestType');
Route::get('/addInterestType', 'Developer\InterestTypeManagementController@add')->name('addInterestType');
Route::get('/editInterestType/{id}', 'Developer\InterestTypeManagementController@edit')->name('editInterestType');
Route::post('/saveInterestType', 'Developer\InterestTypeManagementController@save')->name('saveInterestType');
Route::get('/deleteInterestType/{id}', 'Developer\InterestTypeManagementController@delete')->name('deleteInterestType');

/* Multipleintelligence Type Scale Module Routes */
Route::get('/multipleintelligenceTypeScale', 'Developer\MultipleIntelligenceTypeScaleManagementController@index')->name('multipleintelligenceTypeScale');
Route::post('/multipleintelligenceTypeScale', 'Developer\MultipleIntelligenceTypeScaleManagementController@index')->name('multipleintelligenceTypeScale');
Route::get('/addmultipleintelligenceTypeScale', 'Developer\MultipleIntelligenceTypeScaleManagementController@add')->name('addmultipleintelligenceTypeScale');
Route::get('/editMultipleintelligenceTypeScale', 'Developer\MultipleIntelligenceTypeScaleManagementController@edit')->name('editMultipleintelligenceTypeScale');
Route::post('/saveMultipleintelligenceTypeScale', 'Developer\MultipleIntelligenceTypeScaleManagementController@save')->name('saveMultipleintelligenceTypeScale');
// Route::get('/deleteMultipleIntelligenceTypeScale/{id}', 'Developer\MultipleIntelligenceTypeScaleManagementController@delete')->name('deleteMultipleIntelligenceTypeScale');

/* Personality Type Scale Module Routes */
Route::get('/personalityTypeScale', 'Developer\PersonalityTypeScaleManagementController@index')->name('personalityTypeScale');
Route::post('/personalityTypeScale', 'Developer\PersonalityTypeScaleManagementController@index')->name('personalityTypeScale');
Route::get('/addPersonalityTypeScale', 'Developer\PersonalityTypeScaleManagementController@add')->name('addPersonalityTypeScale');
Route::get('/editPersonalityTypeScale', 'Developer\PersonalityTypeScaleManagementController@edit')->name('editPersonalityTypeScale');
Route::post('/savePersonalityTypeScale', 'Developer\PersonalityTypeScaleManagementController@save')->name('savePersonalityTypeScale');
// Route::get('/deletePersonalityTypeScale/{id}', 'Developer\PersonalityTypeScaleManagementController@delete')->name('deletePersonalityTypeScale');

/* Apptitude Type Scale Module Routes */
Route::get('/apptitudeTypeScale', 'Developer\ApptitudeTypeScaleManagementController@index')->name('apptitudeTypeScale');
Route::post('/apptitudeTypeScale', 'Developer\ApptitudeTypeScaleManagementController@index')->name('apptitudeTypeScale');
Route::get('/addApptitudeTypeScale', 'Developer\ApptitudeTypeScaleManagementController@add')->name('addApptitudeTypeScale');
Route::get('/editApptitudeTypeScale', 'Developer\ApptitudeTypeScaleManagementController@edit')->name('editApptitudeTypeScale');
// Route::post('/saveApptitudeTypeScale', 'Developer\ApptitudeTypeScaleManagementController@save')->name('saveApptitudeTypeScale');

/* Level1 Qualities Module Routes */
Route::get('/level1Qualities', 'Developer\Level1QualityManagementController@index')->name('level1Qualities');
Route::post('/level1Qualities', 'Developer\Level1QualityManagementController@index')->name('level1Qualities');
Route::get('/addLevel1Quality', 'Developer\Level1QualityManagementController@add')->name('addLevel1Quality');
Route::get('/editLevel1Quality/{id}', 'Developer\Level1QualityManagementController@edit')->name('editLevel1Quality');
Route::get('/deleteLevel1Quality/{id}', 'Developer\Level1QualityManagementController@delete')->name('deleteLevel1Quality');
Route::post('/saveLevel1Quality', 'Developer\Level1QualityManagementController@save')->name('saveLevel1Quality');

/* Level4 Promise Plus Module Routes */
Route::get('/level4PromisePlus', 'Developer\Level4PromisePlusManagementController@index')->name('level4PromisePlus');
Route::get('/addLevel4PromisePlus', 'Developer\Level4PromisePlusManagementController@add')->name('addLevel4PromisePlus');
Route::post('/saveLevel4PromisePlus', 'Developer\Level4PromisePlusManagementController@save')->name('saveLevel4PromisePlus');
Route::get('/editLevel4PromisePlus', 'Developer\Level4PromisePlusManagementController@edit')->name('editLevel4PromisePlus');

/* Interpretation Range Module Routes */
Route::get('/interpretationRange', 'Developer\InterpretationRangeManagementController@index')->name('interpretationRange');
Route::get('/addInterpretationRange', 'Developer\InterpretationRangeManagementController@add')->name('addInterpretationRange');
Route::post('/saveInterpretationRange', 'Developer\InterpretationRangeManagementController@save')->name('saveInterpretationRange');
Route::get('/editInterpretationRange', 'Developer\InterpretationRangeManagementController@edit')->name('editInterpretationRange');
