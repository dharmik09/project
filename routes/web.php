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
Route::get('/privacy-policy', 'Home\HomeController@privacyPolicy');
Route::get('/terms-condition', 'Home\HomeController@termsCondition');
Route::get('/about-us', 'Home\HomeController@aboutUs');

Route::get('/share/{any?}', function() { return view('share'); });

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
	Route::post('/load-more-video', 'Teenager\HomeController@loadMoreVideo')->name('load-more-video');
	Route::get('/forgot-password-OTP-view', 'Teenager\PasswordController@forgotPasswordOTPView');
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
	Route::get('/forgot-password', 'Sponsor\PasswordManagementController@forgotPassword')->name('sponsor.forgot-password');
	Route::post('/forgot-password-OTP', 'Sponsor\PasswordManagementController@forgotPasswordOTP')->name('forgot-password-OTP');
	Route::post('/forgot-password-OTP-verify', 'Sponsor\PasswordManagementController@forgotPasswordOTPVerify')->name('forgot-password-OTP-verify');
	Route::post('/save-forgot-password', 'Sponsor\PasswordManagementController@saveForgotPassword')->name('save-forgot-password');

	Route::get('/forgot-password-OTP', 'Sponsor\PasswordManagementController@forgotPassword');
	Route::get('/forgot-password-OTP-verify', 'Sponsor\PasswordManagementController@forgotPassword');
	Route::get('/save-forgot-password', 'Sponsor\PasswordManagementController@forgotPassword');
	Route::get('/set-forgot-password', 'Sponsor\PasswordManagementController@forgotPassword');
	Route::post('/load-more-video', 'Sponsor\HomeController@loadMoreVideo')->name('load-more-video');
});

Route::group(['prefix' => 'parent'], function () {
	Route::get('/', 'Parent\HomeController@index');
	Route::get('/login', 'Parent\LoginController@login')->name('parent.login');
	Route::post('/login-check', 'Parent\LoginController@loginCheck')->name('parent.loginCheck');
	Route::post('/logout', 'Parent\LoginController@logout')->name('parent.logout');
	Route::get('/signup', 'Parent\SignupController@signup')->name('parent.signup');
	Route::post('/do-signup', 'Parent\SignupController@doSignup')->name('parent.do-signup');
	Route::get('/do-signup', 'Parent\SignupController@signup');
	Route::get('/verify-parent-teen-pair', 'Parent\LoginController@verifyParent')->name('verify-parent-teen-pair');
	Route::get('/verify-parent-teen-pair-registration', 'Parent\LoginController@verifyParentTeenRegistration')->name('verify-parent-teen-pair-registration');
	Route::get('/verify-parent-registration', 'Parent\LoginController@verifyParentRegistration')->name('verify-parent-registration');
	Route::get('/forgot-password', 'Parent\PasswordManagementController@forgotPassword')->name('forgot-password');
	Route::post('/forgot-password-OTP', 'Parent\PasswordManagementController@forgotPasswordOTP')->name('forgot-password-OTP');
	Route::post('/forgot-password-OTP-verify', 'Parent\PasswordManagementController@forgotPasswordOTPVerify')->name('forgot-password-OTP-verify');
	Route::post('/save-forgot-password', 'Parent\PasswordManagementController@saveForgotPassword')->name('save-forgot-password');

	Route::get('/forgot-password-OTP', 'Parent\PasswordManagementController@forgotPassword');
	Route::get('/forgot-password-OTP-verify', 'Parent\PasswordManagementController@forgotPassword');
	Route::get('/set-forgot-password', 'Parent\PasswordManagementController@forgotPassword')->name('set-forgot-password');
	Route::get('/save-forgot-password', 'Parent\PasswordManagementController@forgotPassword');
	Route::post('/load-more-video', 'Parent\HomeController@loadMoreVideo')->name('load-more-video');
	Route::post('/set-profile', 'Parent\LoginController@setProfile')->name('set-profile');
});

//Counselor
Route::group(['prefix' => 'counselor'], function () {
	Route::get('/', 'Parent\HomeController@loginCounselor');
	Route::get('/login', 'Parent\CounselorManagementController@login');
	Route::get('/signup', 'Parent\CounselorManagementController@signup');
});

//School Section
Route::group(['prefix' => 'school'], function () {
	Route::get('/', 'School\HomeController@index');
	Route::get('/login', 'School\LoginController@login');
	Route::get('/login-check', 'School\LoginController@login');
	Route::post('/login-check', 'School\LoginController@loginCheck')->name('school.login-check');
	Route::post('/logout', 'School\LoginController@logout')->name('logout');
	Route::get('/signup', 'School\SignupController@signup')->name('signup');
	Route::post('/do-signup', 'School\SignupController@doSignup')->name('do-signup');
	Route::get('/do-signup', 'School\SignupController@signup');
	Route::get('/forgot-password', 'School\PasswordController@forgotPassword')->name('forgot-password');
	Route::post('/forgot-password-OTP', 'School\PasswordController@forgotPasswordOTP')->name('forgot-password-OTP');
	Route::post('/forgot-password-OTP-verify', 'School\PasswordController@forgotPasswordOTPVerify')->name('forgot-password-OTP-verify');
	Route::post('/save-forgot-password', 'School\PasswordController@saveForgotPassword')->name('save-forgot-password');

	Route::get('/forgot-password-OTP', 'School\PasswordController@forgotPassword');
	Route::get('/forgot-password-OTP-verify', 'School\PasswordController@forgotPassword');
	Route::get('/set-forgot-password', 'School\PasswordController@forgotPassword')->name('set-forgot-password');
	Route::get('/save-forgot-password', 'School\PasswordController@forgotPassword');
	Route::post('/load-more-video', 'School\HomeController@loadMoreVideo')->name('load-more-video');
});

Route::get('/verify-parent-teen-pair', 'Parent\LoginController@verifyParent')->name('parent.verify-parent-teen-pair');
Route::post('/ccavenue/response', 'PaymentController@orderResponse');

//SEO Pages
Route::get('/careers', 'Teenager\SEOCareerController@careers');
Route::get('/career-detail/{slug}', 'Teenager\SEOCareerController@careerDetails');
