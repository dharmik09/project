<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-state/{id}', 'StateCityController@getState');
Route::get('/get-city/{id}', 'StateCityController@getCity');

Route::get('/', 'Home\HomeController@index');
Route::get('/home', 'Home\HomeController@index');
Route::get('/faq', 'Home\HomeController@faq');
Route::get('/team', 'Home\HomeController@team');
Route::get('/contact-us', 'Home\HomeController@contactUs');

//Get call back route
Route::get('facebook/callback', 'Teenager\SocialLoginController@handleProviderCallbackFacebook');
Route::get('google/callback', 'Teenager\SocialLoginController@handleProviderCallbackGooglePlus');

Route::group(['prefix' => 'admin'], function () {
	Route::get('/', 'Admin\LoginController@login');
	Route::get('/login', 'Admin\LoginController@login')->name('admin.login');
	Route::post('/loginCheck', 'Admin\LoginController@loginCheck')->name('loginCheck');
	Route::get('/loginCheck', 'Admin\LoginController@login');
	Route::post('/logout', 'Admin\LoginController@logout')->name('logout');
  	Route::get('/register', 'Admin\RegisterController@showRegistrationForm')->name('register');
  	Route::post('/register', 'Admin\RegisterController@register');
});

Route::group(['prefix' => 'teenager'], function () {
	Route::get('/', 'Teenager\HomeController@index');
	Route::get('/login', 'Teenager\LoginController@login')->name('login');
	Route::post('/login-check', 'Teenager\LoginController@loginCheck')->name('loginCheck');
	Route::post('/logout', 'Teenager\LoginController@logout')->name('logout');
	Route::get('/signup', 'Teenager\SignupController@signup')->name('signup');
	Route::post('/do-signup', 'Teenager\SignupController@doSignup');
	Route::post('/get-phone-code-by-country', 'Teenager\SignupController@getPhoneCodeByCountry');
	Route::get('/resend-verification/{t_uniqueid}', 'Teenager\VerifyTeenManagementController@resendVerification');
	Route::get('/verify-teenager', 'Teenager\VerifyTeenManagementController@index');
	Route::get('/forgot-password', 'Teenager\PasswordController@forgotPassword');
	Route::get('/set-forgot-password', 'Teenager\PasswordController@forgotPassword');
	Route::get('/forgot-password-OTP', 'Teenager\PasswordController@forgotPassword');
	Route::post('/forgot-password-OTP', 'Teenager\PasswordController@forgotPasswordOTP');
	Route::post('/forgot-password-OTP-verify', 'Teenager\PasswordController@forgotPasswordOTPVerify');
	Route::post('/save-forgot-password', 'Teenager\PasswordController@saveForgotPassword');
	Route::post('/resend-OTP', 'Teenager\PasswordController@resendOTP');
	Route::get('/facebook', 'Teenager\SocialLoginController@redirectToProviderFacebook');
	Route::get('/google', 'Teenager\SocialLoginController@redirectToProviderGooglePlus');
});

Route::group(['prefix' => 'developer'], function () {
	Route::get('/', 'Developer\LoginController@login');
	Route::get('/login', 'Developer\LoginController@login')->name('developer.login');
	Route::post('/loginCheck', 'Developer\LoginController@loginCheck')->name('loginCheck');
	Route::post('/logout', 'Developer\LoginController@logout')->name('logout');
});

Route::group(['prefix' => 'sponsor'], function () {
	Route::get('/', 'Sponsor\HomeController@index');
	Route::get('/login', 'Sponsor\LoginController@login')->name('sponsor.login');
	Route::post('/login-check', 'Sponsor\LoginController@loginCheck')->name('sponsor.loginCheck');
	Route::post('/logout', 'Sponsor\LoginController@logout')->name('sponsor.logout');
	Route::get('/signup', 'Sponsor\SignupController@signup')->name('sponsor.signup');
	Route::post('/do-signup', 'Sponsor\SignupController@doSignup')->name('sponsor.doSignup');
	Route::get('/do-signup', 'Sponsor\SignupController@signup');
	Route::get('/enterprise-request', 'Sponsor\SignupController@preLoginPackagePurchase')->name('sponsor.enterprise-request');
	Route::get('/forgot-password', 'Sponsor\PasswordController@forgotPassword')->name('sponsor.forgot-password');
	Route::post('/forgot-password-OTP', 'Sponsor\PasswordController@forgotPasswordOTP')->name('forgot-password-OTP');
	Route::post('/forgot-password-OTP-verify', 'Sponsor\PasswordController@forgotPasswordOTPVerify')->name('forgot-password-OTP-verify');
	Route::post('/save-forgot-password', 'Sponsor\PasswordController@saveForgotPassword')->name('save-forgot-password');

	Route::get('/forgot-password-OTP', 'Sponsor\PasswordController@forgotPassword');
	Route::get('/forgot-password-OTP-verify', 'Sponsor\PasswordController@forgotPassword');
	Route::get('/save-forgot-password', 'Sponsor\PasswordController@forgotPassword');
	Route::get('/set-forgot-password', 'Sponsor\PasswordController@forgotPassword');
});

Route::group(['prefix' => 'parent'], function () {
	Route::get('/', 'Parent\HomeController@index');
	Route::get('/login', 'Parent\LoginController@login')->name('parent.login');
	Route::post('/login-check', 'Parent\LoginController@loginCheck')->name('parent.loginCheck');
	Route::post('/logout', 'Parent\LoginController@logout')->name('parent.logout');
	Route::get('/signup', 'Parent\ParentSignupController@signup')->name('parent.signup');
	Route::post('/do-signup', 'Parent\ParentSignupController@doSignup')->name('parent.do-signup');
	Route::get('/do-signup', 'Parent\ParentSignupController@signup');
	Route::get('/verify-parent-teen-pair', 'Parent\LoginController@verifyParent')->name('verify-parent-teen-pair');
	Route::get('/verify-parent-registration', 'Parent\LoginController@verifyParentRegistration')->name('verify-parent-registration');
	Route::get('/forgot-password', 'Parent\PasswordController@forgotPassword')->name('forgot-password');
	Route::post('/forgot-password-OTP', 'Parent\PasswordController@forgotPasswordOTP')->name('forgot-password-OTP');
	Route::post('/forgot-password-OTP-verify', 'Parent\PasswordController@forgotPasswordOTPVerify')->name('forgot-password-OTP-verify');
	Route::post('/save-forgot-password', 'Parent\PasswordController@saveForgotPassword')->name('save-forgot-password');

	Route::get('/forgot-password-OTP', 'Parent\PasswordController@forgotPassword');
	Route::get('/forgot-password-OTP-verify', 'Parent\PasswordController@forgotPassword');
	Route::get('/set-forgot-password', 'Parent\PasswordController@forgotPassword')->name('set-forgot-password');
	Route::get('/save-forgot-password', 'Parent\PasswordController@forgotPassword');
});

//Counselor
Route::group(['prefix' => 'counselor'], function () {
	Route::get('/', 'Parent\HomeController@loginCounselor');
	Route::get('/login', 'Parent\CounselorController@login');
	Route::get('/signup', 'Parent\CounselorController@signup');
});

//School Section
Route::group(['prefix' => 'school'], function () {
	Route::get('/', 'School\HomeController@index');
	Route::get('/login', 'School\LoginController@login');
	Route::get('/dashboard', 'School\DashboardController@index');
	Route::get('/signup', 'School\SignupController@signup');
	Route::post('/doSignup', 'School\SignupController@doSignup');
	Route::get('/doSignup', 'School\SignupController@signup');
	Route::post('/dashboard/', 'School\LoginController@loginCheck');
	Route::get('/logout', 'School\LoginController@getLogout');
	Route::get('/changepassword', 'School\PasswordController@changePassword');
	Route::post('/updatePassword', 'School\PasswordController@updatePassword');
	Route::post('/userSearchForSchoolData/', 'School\DashboardController@userSearchForSchoolData');

	Route::get('/forgotPassword', 'School\PasswordController@forgotPassword');
	Route::post('/forgotPasswordOTP', 'School\PasswordController@forgotPasswordOTP');
	Route::post('/forgotPasswordOTPVerify', 'School\PasswordController@forgotPasswordOTPVerify');
	Route::post('/saveForgotPassword', 'School\PasswordController@saveForgotPassword');

	Route::get('/forgotPasswordOTP', 'School\PasswordController@forgotPassword');
	Route::get('/forgotPasswordOTPVerify', 'School\PasswordController@forgotPassword');
	Route::get('/setForgotPassword', 'School\PasswordController@forgotPassword');
	Route::get('/saveForgotPassword', 'School\PasswordController@forgotPassword');
	Route::get('/updateprofile', 'School\UpdateProfileController@updateProfile');
	Route::post('/saveProfile', 'School\UpdateProfileController@saveProfile');
	Route::get('/progress', 'School\UpdateProfileController@progress');
	Route::get('/progress/{cid}', 'School\UpdateProfileController@progress');
	//Route::get('/progress/getClassAsPerSchool/{cid}', 'School\UpdateProfileController@getTeenDataAsPerClass');

	Route::get('/bulkimport', 'School\DashboardController@bulkImport');
	Route::post('/saveschoolbulkimport', 'School\DashboardController@savebulkdata');
	Route::get('/inactive/{id}/{status}', 'School\DashboardController@inactive');
	Route::get('/sendemail', 'School\DashboardController@sendemail');
	Route::post('/sendemail', 'School\DashboardController@sendemail');  
	Route::get('verifyTeenfromSchool', 'Teenager\VerifyTeenManagementController@verifyTeenFromSchool');

	Route::get('/school/exportpdf/{id}', 'School\DashboardController@exportPDF');
});
