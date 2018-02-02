<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use Mail;
use Session;
use Storage;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Level1Activity\Contracts\Level1ActivitiesRepository;
use App\Services\FileStorage\Contracts\FileStorageRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Community\Contracts\CommunityRepository;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Http\Requests\TeenagerProfileUpdateRequest;
use App\TeenParentRequest;
use Carbon\Carbon;
use Event;
use App\Events\SendMail;
use Redirect;
use Response;
use App\Country;
use App\Teenagers;
use Input;
use Image;
use DB;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ProfessionsRepository $professionsRepository, CommunityRepository $communityRepository, ParentsRepository $parentsRepository, FileStorageRepository $fileStorageRepository, Level1ActivitiesRepository $level1ActivitiesRepository, SponsorsRepository $sponsorsRepository, TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->sponsorsRepository = $sponsorsRepository;
        $this->objCountry = new Country();
        $this->middleware('teenager');
        $this->teenOriginalImageUploadPath = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->teenThumbImageUploadPath = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH');
        $this->teenProfileImageUploadPath = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH');
        $this->level1ActivitiesRepository = $level1ActivitiesRepository;
        $this->parentsRepository = $parentsRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->teenThumbImageHeight = Config::get('constant.TEEN_THUMB_IMAGE_HEIGHT');
        $this->teenThumbImageWidth = Config::get('constant.TEEN_THUMB_IMAGE_WIDTH');
        $this->cartoonThumbImageUploadPath = Config::get('constant.CARTOON_THUMB_IMAGE_UPLOAD_PATH');
        $this->humanThumbImageUploadPath = Config::get('constant.HUMAN_THUMB_IMAGE_UPLOAD_PATH');
        $this->relationIconThumbImageUploadPath = Config::get('constant.RELATION_ICON_THUMB_IMAGE_UPLOAD_PATH');
        $this->communityRepository = $communityRepository;
        $this->professionsRepository = $professionsRepository;
    }

    public function setSoundOnOff($data) {
        $response = [ 'status' => 0, 'message' => trans('appmessages.default_error_msg') ];
        $user = Auth::guard('teenager')->user();
        if($user) {
            $userId = $user->id;
            $sound = ($data != "" && $data == "1") ? 1 : 0;
            $user->is_sound_on = $sound;
            $user->save();
            $response['message'] = "Success";
            $response['status'] = 1;
            $response['sound'] = $sound;
        } else {
            $response['message'] = "Something went wrong!";
        }
        return response()->json($response, 200);
        exit;
    }

    //Set profile form
    public function setProfile()
    {
        $data = [];
        $teenSponsorIds = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenThumbImageUploadPath.'proteen-logo.png');
        $countries = $this->objCountry->getAllCounries();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($user->id);
        $teenagerParents = $this->teenagersRepository->getTeenParents($user->id);
        foreach($teenagerSponsors as $teenagerSponsor) {
            $teenSponsorIds[] = $teenagerSponsor->ts_sponsor;
        }
        $level1Activities = $this->level1ActivitiesRepository->getNotAttemptedActivities(Auth::guard('teenager')->user()->id);
        $teenagerMeta = Helpers::getTeenagerMetaData(Auth::guard('teenager')->user()->id);
        return view('teenager.setUpProfile', compact('level1Activities', 'data', 'user', 'countries', 'sponsorDetail', 'teenSponsorIds', 'teenagerParents', 'teenagerMeta'));   
    }

    //My profile data
    public function profile()
    {
        $data = [];
        $teenSponsorIds = [];
        $user = Auth::guard('teenager')->user();
        $data['user_profile'] = (Auth::guard('teenager')->user()->t_photo != "" && Storage::size($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) > 0) ? Storage::url($this->teenThumbImageUploadPath.Auth::guard('teenager')->user()->t_photo) : asset($this->teenThumbImageUploadPath.'proteen-logo.png');
        $countries = $this->objCountry->getAllCounries();
        $sponsorDetail = $this->sponsorsRepository->getApprovedSponsors();
        $teenagerSponsors = $this->teenagersRepository->getTeenagerSelectedSponsor($user->id);
        $teenagerParents = $this->teenagersRepository->getTeenParents($user->id);
        foreach($teenagerSponsors as $teenagerSponsor) {
            $teenSponsorIds[] = $teenagerSponsor->ts_sponsor;
        }
        $level1Activities = $this->level1ActivitiesRepository->getNotAttemptedActivities(Auth::guard('teenager')->user()->id);
        $teenagerMeta = Helpers::getTeenagerMetaData(Auth::guard('teenager')->user()->id);
        $teenagerMyIcons = array();
        //Get teenager choosen Icon
        $teenagerIcons = $this->teenagersRepository->getTeenagerSelectedIcon(Auth::guard('teenager')->user()->id);
        $relationIcon = array();
        $fictionIcon = array();
        $nonFiction = array();
        if (isset($teenagerIcons) && !empty($teenagerIcons)) {
            foreach ($teenagerIcons as $key => $icon) {
                if ($icon->ti_icon_type == 1) {
                    if ($icon->fiction_image != '' && Storage::size($this->cartoonThumbImageUploadPath . $icon->fiction_image) > 0)  {
                        $fictionIcon[] = Storage::url($this->cartoonThumbImageUploadPath . $icon->fiction_image);
                    } else {
                        $fictionIcon[] = Storage::url($this->cartoonThumbImageUploadPath . 'proteen-logo.png');
                    }
                } else if ($icon->ti_icon_type == 2) {
                    if ($icon->nonfiction_image != '' && Storage::size($this->humanThumbImageUploadPath . $icon->nonfiction_image) > 0) {
                        $nonFiction[] = Storage::url($this->humanThumbImageUploadPath . $icon->nonfiction_image);
                    } else {
                        $nonFiction[] = Storage::url($this->humanThumbImageUploadPath . 'proteen-logo.png');
                    }
                } else {
                    if ($icon->ti_icon_image != '' && Storage::size($this->relationIconThumbImageUploadPath . $icon->ti_icon_image) > 0) {
                        $relationIcon[] = Storage::url($this->relationIconThumbImageUploadPath . $icon->ti_icon_image);
                    }
                }
            }
            $teenagerMyIcons = array_merge($fictionIcon, $nonFiction, $relationIcon);
        } else {
            $teenagerMyIcons = array();
        }
        $learningGuidance = Helpers::getCmsBySlug('learning-guidance-info');
        $myConnectionsCount = $this->communityRepository->getMyConnectionsCount($user->id);
        $myConnections = $this->communityRepository->getMyConnections($user->id);
        $myCareers = $this->professionsRepository->getMyCareersSlotWise($user->id);
        $myCareersCount = $this->professionsRepository->getMyCareersCount($user->id);
        return view('teenager.profile', compact('level1Activities', 'data', 'user', 'countries', 'sponsorDetail', 'teenSponsorIds', 'teenagerParents', 'teenagerMeta', 'teenagerMyIcons', 'learningGuidance', 'myConnectionsCount', 'myConnections', 'myCareers', 'myCareersCount'));   
    }

    //Store my profile data
    public function saveProfile(TeenagerProfileUpdateRequest $request)
    {
        $body = $request->all();
        $user = Auth::guard('teenager')->user();
        $user = Teenagers::find($user->id);
        $teenagerDetail['id'] = $user->id;
        $teenagerDetail['t_name'] = (isset($body['name']) && $body['name'] != '') ? $body['name'] : '';
        $teenagerDetail['t_lastname'] = (isset($body['lastname']) && $body['lastname'] != '') ? $body['lastname'] : '';
        //Nickname is ProTeen Code
        $teenagerDetail['t_nickname'] = (isset($body['proteen_code']) && $body['proteen_code'] != '') ? e($body['proteen_code']) : '';
        $stringVariable = $body['year']."-".$body['month']."-".$body['day'];
        $birthDate = Carbon::createFromFormat("Y-m-d", $stringVariable);
        $todayDate = Carbon::now();
        if (Helpers::validateDate($stringVariable, "Y-m-d") && $todayDate->gt($birthDate) ) {
            $teenagerDetail['t_birthdate'] = $stringVariable;
        } else {
            return Redirect::to("teenager/set-profile")->withErrors("Date is invalid")->withInput();
            exit;
        }
        $teenagerDetail['t_gender'] = (isset($body['gender']) && $body['gender'] != '') ? $body['gender'] : '';
        $t_email = (isset($body['email']) && $body['email'] != '') ? $body['email'] : '';
        $teenagerDetail['password'] = (isset($body['password']) && $body['password'] != '') ? bcrypt($body['password']) : $user->password;
        $teenagerDetail['t_phone'] = (isset($body['mobile']) && $body['mobile'] != '') ? $body['mobile'] : '';
        //Added new phone name field
        $teenagerDetail['t_phone_new'] = (isset($body['phone']) && $body['phone'] != '') ? $body['phone'] : '';
        $teenagerDetail['t_country'] = (isset($body['country']) && $body['country'] != '') ? $body['country'] : '';
        $teenagerDetail['t_pincode'] = (isset($body['pincode']) && $body['pincode'] != '') ? $body['pincode'] : '';
        if($teenagerDetail['t_pincode'] != "") {
            $getLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenagerDetail['t_pincode'].'&sensor=true'));
            $getCityArea = ( isset($getLocation->results[0]->address_components[1]->long_name) && $getLocation->results[0]->address_components[1]->long_name != "" ) ? $getLocation->results[0]->address_components[1]->long_name : "Default";
        } else if ($teenagerDetail['t_country'] != "") {
            $country = $this->objCountry->find($teenagerDetail['t_country']);
            $getCityArea = $country->c_name;
        } else {
            $getCityArea = "Default";
        }
        $teenagerDetail['t_location'] = $getCityArea;
        $teenagerDetail['is_search_on'] = (isset($body['public_profile']) && $body['public_profile'] != '') ? $body['public_profile'] : '0';
        $teenagerDetail['is_share_with_other_members'] = (isset($body['share_with_members']) && $body['share_with_members'] != '') ? $body['share_with_members'] : '0';
        $teenagerDetail['is_share_with_parents'] = (isset($body['share_with_parents']) && $body['share_with_parents'] != '') ? $body['share_with_parents'] : '0';
        $teenagerDetail['is_share_with_teachers'] = (isset($body['share_with_teachers']) && $body['share_with_teachers'] != '') ? $body['share_with_teachers'] : '0';
        $teenagerDetail['is_notify'] = (isset($body['notifications']) && $body['notifications'] != '') ? $body['notifications'] : '0';
        $teenagerDetail['t_view_information'] = (isset($body['t_view_information']) && $body['t_view_information'] != '') ? $body['t_view_information'] : '0';
        $teenagerDetail['t_about_info'] = (isset($body['t_about_info']) && $body['t_about_info'] != '') ? $body['t_about_info'] : '';

        //Check all default field value -> If those are entered dummy by users
        if ($teenagerDetail['t_name'] == '' || $teenagerDetail['t_lastname'] == '' || $teenagerDetail['t_country'] == '' || $teenagerDetail['t_pincode'] == '' || $t_email == '') {
            return Redirect::to("teenager/set-profile")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }
        if (!isset($body['selected_sponsor']) || count($body['selected_sponsor']) < 1) {
            return Redirect::to("teenager/set-profile")->withErrors("Please select atleast one sponsor choice")->withInput();
            exit;
        }

        if (!in_array($teenagerDetail['t_gender'], array("1", "2"))) {
            return Redirect::to("teenager/set-profile")->withErrors(trans('validation.someproblems'))->withInput();
            exit;
        }
        $teenagerMobileExist = false;
        $teenagerEmailExist = false;
        
        if ($t_email != '' && $user->t_social_provider == 'Normal') {
            $teenagerEmailExist = $this->teenagersRepository->checkActiveEmailExist($t_email, $user->id);
        }
        if ($teenagerDetail['t_phone'] != '' && $user->t_social_provider == 'Normal') {
            $teenagerMobileExist = $this->teenagersRepository->checkActivePhoneExist($teenagerDetail['t_phone'], $user->id);
        }

        if ($teenagerEmailExist) {
            $response['message'] = trans('appmessages.userwithsameemailaddress');
            return Redirect::to("teenager/set-profile")->withErrors(trans('appmessages.userwithsameemailaddress'))->withInput();
            exit;
        } else if ($teenagerMobileExist) {
            $response['message'] = trans('appmessages.userwithsamenumber');
            return Redirect::to("teenager/set-profile")->withErrors(trans('appmessages.userwithsamenumber'))->withInput();
            exit;
        } else {
            /* save sponser by teenager id if sponsor id is not blank */
            if (isset($body['selected_sponsor']) && !empty($body['selected_sponsor'])) {
                $sponserDetail = $this->teenagersRepository->saveTeenagerSponserId($user->id, implode(',', $body['selected_sponsor']));
            }
            if (Input::file()) {
                $file = Input::file('pic');
                if (!empty($file)) {
                    if(isset($user->t_photo) && !empty($user->t_photo)) {
                        $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($user->t_photo, $this->teenOriginalImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($user->t_photo, $this->teenThumbImageUploadPath, "s3");
                        $profileImageDelete = $this->fileStorageRepository->deleteFileToStorage($user->t_photo, $this->teenProfileImageUploadPath, "s3");
                    }
                    $fileName = 'teenager_' . time() . '.' . $file->getClientOriginalExtension();
                    $pathOriginal = public_path($this->teenOriginalImageUploadPath . $fileName);
                    $pathThumb = public_path($this->teenThumbImageUploadPath . $fileName);
                    $pathProfile = public_path($this->teenProfileImageUploadPath . $fileName);
                    Image::make($file->getRealPath())->save($pathOriginal);
                    Image::make($file->getRealPath())->resize($this->teenThumbImageWidth, $this->teenThumbImageHeight)->save($pathThumb);
                    Image::make($file->getRealPath())->resize(200, 200)->save($pathProfile);
                    //Uploading on AWS
                    $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenOriginalImageUploadPath, $pathOriginal, "s3");
                    $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenThumbImageUploadPath, $pathThumb, "s3");
                    $profileImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->teenProfileImageUploadPath, $pathProfile, "s3");
                    //Deleting Local Files
                    \File::delete($this->teenOriginalImageUploadPath . $fileName);
                    \File::delete($this->teenThumbImageUploadPath . $fileName);
                    \File::delete($this->teenProfileImageUploadPath . $fileName);
                    $teenagerDetail['t_photo'] = $fileName;
                }
            }
            $teenUpdate = $this->teenagersRepository->saveTeenagerDetail($teenagerDetail);
            if (isset($teenUpdate) && !empty($teenUpdate)) {
                return Redirect::to("teenager/my-profile#profile-info")->with('success', 'Profile updated successfully.');
            } else {
                return Redirect::to("teenager/my-profile#profile-info")->withErrors(trans('validation.somethingwrong'));
            }
            exit;
        }
    }

}
