<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([ 'middleware' => ['api-outside'] ], function () {
	Route::post('/apiVersion', 'Webservice\RestLessController@apiVersion');
	Route::post('/login', 'Webservice\LoginController@login');
	Route::post('/getCountryList', 'Webservice\RestLessController@getCountryList');
	Route::post('/getSponsors', 'Webservice\RestLessController@getSponsors');
	Route::post('/verifyOTP', 'Webservice\PasswordController@verifyOTP');
	Route::post('/resetPassword', 'Webservice\PasswordController@resetPassword');
	Route::post('/forgotPassword', 'Webservice\PasswordController@forgotPassword');
	//Dought
	Route::post('/userLogout', 'Webservice\LoginController@userLogout');
	
	Route::post('/saveUpdatedDeviceToken', 'Webservice\LoginController@saveUpdatedDeviceToken');
	Route::post('/updateTeenagerLoginToken', 'Webservice\LoginController@updateTeenagerLoginToken');
	//Dought for login token required in response or not. If yes then have to pass required information
	Route::post('/signup', 'Webservice\SignupController@signup');
});

Route::group([ 'middleware' => ['api-support'] ], function () {
	Route::post('/setPassword', 'Webservice\PasswordController@setPassword');
	Route::post('/changePassword', 'Webservice\PasswordController@changePassword');
	//profile page
	Route::post('/updateProfile', 'Webservice\ProfileController@updateProfile');
	Route::post('/getTeenagerProfileData', 'Webservice\ProfileController@getTeenagerProfileData');
	Route::post('/deleteTeenagerData', 'Webservice\ProfileController@deleteTeenagerData');
	Route::post('/saveTeenagerAboutInfo', 'Webservice\ProfileController@saveTeenagerAboutInfo');
	Route::post('/getTeenagerEarnAchievement', 'Webservice\ProfileController@getTeenagerEarnAchievement');

	Route::post('/getActiveTeenagers', 'Webservice\TeenagerController@getActiveTeenagers');
	Route::post('/getActiveTeenagersBySearch', 'Webservice\TeenagerController@getActiveTeenagersBySearch');
	//Parent&Mentor invitation section
	Route::post('/getParentMentorList', 'Webservice\ParentController@getParentMentorList');
	Route::post('/parentTeenPair', 'Webservice\ParentController@parentTeenPair');
	//First Level Part 1 Question/Answer Route
	Route::post('/getLevel1Questions', 'Webservice\Level1ActivityController@getFirstLevelActivity');
	Route::post('/submitLevel1Answers', 'Webservice\Level1ActivityController@saveFirstLevelActivity');
	//First Level Part 2 Route
	Route::post('/getLevel1Part2Options', 'Webservice\Level1ActivityController@getLevel1Part2Options');
	Route::post('/getLevel1Part2Category', 'Webservice\Level1ActivityController@getLevel1Part2Category');
	Route::post('/getLevel1Part2IconData', 'Webservice\Level1ActivityController@getLevel1Part2IconData');
	Route::post('/getSearchLevel1Part2IconData', 'Webservice\Level1ActivityController@getSearchLevel1Part2IconData');
	Route::post('/submitSelfIcon', 'Webservice\Level1ActivityController@submitSelfIcon');
	Route::post('/submitRelationIcon', 'Webservice\Level1ActivityController@submitRelationIcon');
	Route::post('/submitLevel1Part2QualitiesData', 'Webservice\Level1ActivityController@submitLevel1Part2QualitiesData');
	//
	Route::post('/addIcon', 'Webservice\Level1ActivityController@addIcon');
	Route::post('/submitLevel1Part2Icon', 'Webservice\Level1ActivityController@submitLevel1Part2Icon');
	Route::post('/submitLevel1Part2Qualities', 'Webservice\Level1ActivityController@submitLevel1Part2Qualities');
	Route::post('/teenagerUpdateImageAndNickname', 'Webservice\Level1ActivityController@teenagerUpdateImageAndNickname');

	//Education & Achievement Route
	Route::post('/getTeenagerAcademicInfo', 'Webservice\ProfileController@getTeenagerAcademicInfo');
	Route::post('/saveTeenagerAcademicInfo', 'Webservice\ProfileController@saveTeenagerAcademicInfo');
	Route::post('/getTeenagerAchievementInfo', 'Webservice\ProfileController@getTeenagerAchievementInfo');
	Route::post('/saveTeenagerAchievementInfo', 'Webservice\ProfileController@saveTeenagerAchievementInfo');
	//Icons Route
	Route::post('/getTeenagerProfileIcons', 'Webservice\ProfileController@getTeenagerProfileIcons');
	//Help
	Route::post('/help', 'Webservice\HomeController@help');
	Route::post('/helpSearch', 'Webservice\HomeController@helpSearch');
	//Dashboard
	Route::post('/getDashboardDetail', 'Webservice\DashboardController@getDashboardDetail');
	Route::post('/getInterestDetail', 'Webservice\DashboardController@getInterestDetail');
	Route::post('/getStrengthDetail', 'Webservice\DashboardController@getStrengthDetail');
	Route::post('/getTeenagerMemberDetail', 'Webservice\TeenagerController@getTeenagerMemberDetail');
	Route::post('/getTeenagerCareers', 'Webservice\DashboardController@getTeenagerCareers');
	Route::post('/getTeenagerCareersConsider', 'Webservice\DashboardController@getTeenagerCareersConsider');
	Route::post('/getMemberConnections', 'Webservice\TeenagerController@getMemberConnections');
	
	//Community
	Route::post('/communityNewConnections', 'Webservice\CommunityController@communityNewConnections');
	Route::post('/communityMyConnections', 'Webservice\CommunityController@communityMyConnections');
	Route::post('/searchCommunityNewConnections', 'Webservice\CommunityController@searchCommunityNewConnections');
	Route::post('/searchCommunityMyConnections', 'Webservice\CommunityController@searchCommunityMyConnections');
	Route::post('/sendConnectionRequest', 'Webservice\CommunityController@sendConnectionRequest');
	Route::post('/acceptDeclineConnectionRequest', 'Webservice\CommunityController@acceptDeclineConnectionRequest');
	
	//Interest Management
	Route::post('/getInterestDetailPage', 'Webservice\DashboardController@getInterestDetailPage');
	Route::post('/getInterestPageRelatedCareers', 'Webservice\DashboardController@getInterestPageRelatedCareers');
	Route::post('/getStrengthDetailPage', 'Webservice\DashboardController@getStrengthDetailPage');
	Route::post('/getStrengthPageRelatedCareers', 'Webservice\DashboardController@getStrengthPageRelatedCareers');
	Route::post('/getMiAndInterestPageGurusDetails', 'Webservice\DashboardController@getMiAndInterestPageGurusDetails');
	//Dashboard Network Section
	Route::post('/getNetworkDetails', 'Webservice\DashboardController@getNetworkDetails');
	//Route::post('/getNetworkMemberDetails', 'Webservice\NetworkController@getNetworkMemberDetails');

	//Learning Guidance
	Route::post('/learningGuidance', 'Webservice\HomeController@learningGuidance');

	//Level 2 Activity
	Route::post('/getLevel2Activity', 'Webservice\level2ActivityController@getLevel2Activity');
	Route::post('/saveLevel2Activity', 'Webservice\level2ActivityController@saveLevel2Activity');

	//Level 1 Traits
	Route::post('/getLevel1Traits', 'Webservice\Level1ActivityController@getLevel1Traits');
	Route::post('/saveLevel1Traits', 'Webservice\Level1ActivityController@saveLevel1Traits');

	//Coupon
	Route::post('/getCoupons', 'Webservice\CouponController@getCoupons');
	Route::post('/consumeCoupon', 'Webservice\CouponController@consumeCoupon');

	//ProCoins Buy
	Route::post('/getProCoinsPackages', 'Webservice\CoinController@getProCoinsPackages');
	Route::post('/requestToParentForProCoins', 'Webservice\CoinController@requestToParentForProCoins');

	//ProCoins Gift
	Route::post('/getGiftedCoinsHistory', 'Webservice\CoinController@getGiftedCoinsHistory');
	Route::post('/searchTeenagerToGiftCoins', 'Webservice\CoinController@searchTeenagerToGiftCoins');
	Route::post('/saveGiftedCoins', 'Webservice\CoinController@saveGiftedCoins');

	//ProCoins History
	Route::post('/getProCoinsTransactionsHistory', 'Webservice\CoinController@getProCoinsTransactionsHistory');
	Route::post('/getProCoinsPromisePlusData', 'Webservice\CoinController@getProCoinsPromisePlusData');
	Route::post('/getProCoinsLearningGuidanceData', 'Webservice\CoinController@getProCoinsLearningGuidanceData');
	Route::post('/getProCoinsL4ConceptData', 'Webservice\CoinController@getProCoinsL4ConceptData');

	//Level 3 Baskets and Profession
	Route::post('/getAllBasktes', 'Webservice\level3ActivityController@getAllBasktes');
	Route::post('/getCareersByBasketId', 'Webservice\level3ActivityController@getCareersByBasketId');
	Route::post('/getAllCareers', 'Webservice\level3ActivityController@getAllCareers');
	Route::post('/searchCareers', 'Webservice\level3ActivityController@getCareersSearch');
	Route::post('/getBasketByCareerId', 'Webservice\level3ActivityController@getBasketByCareerId');
	Route::post('/getCareersDetails', 'Webservice\level3ActivityController@getCareersDetailsByCareerSlug');
	Route::post('/getTeenagerCareersWithBasket', 'Webservice\level3ActivityController@getTeenagerCareersWithBaket');
	Route::post('/searchTeenagerCareersWithBasket', 'Webservice\level3ActivityController@getTeenagerCareersSearch');
	Route::post('/addStarToCareer', 'Webservice\level3ActivityController@addStarToCareer');
	Route::post('/getCareerFans', 'Webservice\level3ActivityController@getCareerFansPageWise');
	Route::post('/getMyCareerPageFilterDetails', 'Webservice\level3ActivityController@getMyCareerPageFilterDetails');

	//Notification
	Route::post('/getNotification', 'Webservice\NotificaionController@getNotificationPageWise');
	Route::post('/getNotificationCount', 'Webservice\NotificaionController@getUnreadNotificationCount');
	Route::post('/deleteNotification', 'Webservice\NotificaionController@deleteNotification');
	Route::post('/readNotification', 'Webservice\NotificaionController@changeNotificationStatus');

	//Forum
	Route::post('/getForumQuestion', 'Webservice\ForumController@getForumQuestionPageWise');
	Route::post('/getForumAnswer', 'Webservice\ForumController@getForumQuestionByQuestionIdPageWise');
	Route::post('/saveForumAnswer', 'Webservice\ForumController@saveForumQuestionByQuestionId');

	//Tag
	Route::post('/getTagDetails', 'Webservice\TagController@getTagDetails');
	
	//HelpText
	Route::post('/getHelpText', 'Webservice\HelpController@getHelpTextBySlug');

	//L4 Activity
	Route::post('/getScholarshipProgramsDetails', 'Webservice\Level4ActivityController@getScholarshipProgramsDetails');
	Route::post('/applyForScholarshipProgram', 'Webservice\Level4ActivityController@applyForScholarshipProgram');
	Route::post('/getParentAndMentorListForChallengePlay', 'Webservice\Level4ActivityController@getParentAndMentorListForChallengePlay');
	Route::post('/challengeToParentOrMentorForProfession', 'Webservice\Level4ActivityController@challengeToParentOrMentorForProfession');
	Route::post('/getCareerPageAdvanceViewDetails', 'Webservice\Level4ActivityController@getCareerPageAdvanceViewDetails');

	//Advertisements
	Route::post('/getDashboardAdvertisements', 'Webservice\DashboardController@getDashboardAdvertisements');

	//L4 Advance Activity
	Route::post('/getL4AdvanceActivityMediaWiseDescription', 'Webservice\Level4AdvanceActivityController@getL4AdvanceActivityMediaWiseDescription');

    //L3 Research Activity 
    Route::post('/saveL3BoosterPointCareerResearch', 'Webservice\level3ActivityController@saveL3BoosterPointCareerResearch');
});
