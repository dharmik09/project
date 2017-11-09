<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Input;
use Config;
use File;
use Image;
use Helpers;
use Redirect;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Pagination\Paginator;
use App\Professions;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionRequest;
use App\Services\Professions\Contracts\ProfessionsRepository;
use App\Services\ProfessionHeaders\Contracts\ProfessionHeadersRepository;
use App\Services\Baskets\Contracts\BasketsRepository;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Cache;
use App\Notifications;
use App\Services\FileStorage\Contracts\FileStorageRepository;

class ProfessionManagementController extends Controller {

    public function __construct(FileStorageRepository $fileStorageRepository, ProfessionHeadersRepository $ProfessionsHeadersRepository, ProfessionsRepository $ProfessionsRepository, BasketsRepository $BasketsRepository,TeenagersRepository $TeenagersRepository) {
        $this->objProfession = new Professions();
        $this->ProfessionsRepository = $ProfessionsRepository;
        $this->ProfessionHeadersRepository = $ProfessionsHeadersRepository;
        $this->BasketsRepository = $BasketsRepository;
        $this->TeenagersRepository = $TeenagersRepository;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->professionOriginalImageUploadPath = Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageUploadPath = Config::get('constant.PROFESSION_THUMB_IMAGE_UPLOAD_PATH');
        $this->professionThumbImageHeight = Config::get('constant.PROFESSION_THUMB_IMAGE_HEIGHT');
        $this->professionThumbImageWidth = Config::get('constant.PROFESSION_THUMB_IMAGE_WIDTH');
        $this->professionVideoUploadPath = Config::get('constant.PROFESSION_ORIGINAL_VIDEO_UPLOAD_PATH');
        $this->controller = 'ProfessionManagementController';
        $this->basketOriginalImageUploadPath = Config::get('constant.BASKET_ORIGINAL_IMAGE_UPLOAD_PATH');
        $this->basketThumbImageUploadPath = Config::get('constant.BASKET_THUMB_IMAGE_UPLOAD_PATH');
        $this->basketThumbImageHeight = Config::get('constant.BASKET_THUMB_IMAGE_HEIGHT');
        $this->basketThumbImageWidth = Config::get('constant.BASKET_THUMB_IMAGE_WIDTH');
        $this->getProfessionImagePath = Config::get('constant.GET_PROFESSION_IMAGES');

        $this->loggedInUser = Auth::guard('admin');
    }

    public function index() {
        $professions = $this->ProfessionsRepository->getAllProfessions();
        $uploadProfessionThumbPath = $this->professionThumbImageUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@index", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.ListProfession', compact('professions', 'uploadProfessionThumbPath'));
    }

    public function add() {
        $professionDetail = [];
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@add", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditProfession', compact('professionDetail'));
    }

    public function edit($id) {
        $professionDetail = $this->objProfession->find($id);
        $uploadProfessionThumbPath = $this->professionThumbImageUploadPath;
        $uploadVideoPath = $this->professionVideoUploadPath;
        Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_READ'), $this->controller . "@edit", $_SERVER['REQUEST_URI'], Config::get('constant.AUDIT_ORIGIN_WEB'), '', '', $_SERVER['REMOTE_ADDR']);
        return view('admin.EditProfession', compact('professionDetail', 'uploadProfessionThumbPath', 'uploadVideoPath'));
    }

    public function save(ProfessionRequest $ProfessionRequest) {
        $professionDetail = [];

        $professionDetail['id'] = e(input::get('id'));
        $hiddenLogo = e(input::get('hidden_logo'));
        $professionDetail['pf_logo'] = $hiddenLogo;
        $hiddenVideo = e(input::get('hidden_video'));
        $videotype = e(Input::get('pf_video_type'));
        $postData['pageRank'] = Input::get('pageRank');
        if ($videotype == '1') {
            $professionDetail['pf_video'] = $hiddenVideo;
        } else if ($videotype == '2') {
            $professionDetail['pf_video'] = trim(e(input::get('youtube')));
            if ($hiddenVideo != '') {
                $videoOriginal = public_path($this->professionVideoUploadPath . $hiddenVideo);
                File::delete($videoOriginal);
            }
        } else if ($videotype == '3') {
            $professionDetail['pf_video'] = e(input::get('vimeo'));
            if ($hiddenVideo != '') {
                $videoOriginal = public_path($this->professionVideoUploadPath . $hiddenVideo);
                File::delete($videoOriginal);
            }
        }
        $professionDetail['pf_video_type'] = $videotype;
        $professionDetail['pf_name'] = trim(input::get('pf_name'));
        $professionDetail['pf_basket'] = e(input::get('pf_basket'));
        /* $professionDetail['pf_intro']   = input::get('pf_intro');  */
        $professionDetail['deleted'] = e(input::get('deleted'));
        $professionDetail['pf_profession_alias'] = input::get('pf_profession_alias');
        $secondary_baskets = input::get('pf_related_basket');
        
        if (Input::file()) {
            $file = Input::file('pf_logo');
            $videoFile = Input::file('normal');
            if (!empty($file)) {
                $fileName = 'profession_' . time() . '.' . $file->getClientOriginalExtension();
                $pathOriginal = public_path($this->professionOriginalImageUploadPath . $fileName);
                $pathThumb = public_path($this->professionThumbImageUploadPath . $fileName);

                Image::make($file->getRealPath())->save($pathOriginal);
                Image::make($file->getRealPath())->resize($this->professionThumbImageWidth, $this->professionThumbImageHeight)->save($pathThumb);

                if ($hiddenLogo != '') {
                    $originalImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->professionThumbImageUploadPath, "s3");
                        $thumbImageDelete = $this->fileStorageRepository->deleteFileToStorage($hiddenLogo, $this->cartoonThumbImageUploadPath, "s3");
                }

                //Uploading on AWS
                $originalImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->professionOriginalImageUploadPath, $pathOriginal, "s3");
                $thumbImage = $this->fileStorageRepository->addFileToStorage($fileName, $this->professionThumbImageUploadPath, $pathThumb, "s3");
                
                \File::delete($this->professionOriginalImageUploadPath . $fileName);
                \File::delete($this->professionThumbImageUploadPath . $fileName);

                $professionDetail['pf_logo'] = $fileName;
            }
            if (!empty($videoFile)) {
                $fileName = 'profession_' . time() . '.' . $videoFile->getClientOriginalExtension();
                $pathOriginal = public_path($this->professionVideoUploadPath);

                $videoFile->move($pathOriginal, $fileName);
                if ($hiddenVideo != '') {
                    $videoOriginal = public_path($this->professionVideoUploadPath . $hiddenVideo);
                    File::delete($videoOriginal);
                }

                $professionDetail['pf_video'] = $fileName;
            }
        }
        
        //Check if profession name already exist
        if(isset($secondary_baskets) && !empty($secondary_baskets))
        {
            $professionDetail['pf_related_basket'] = implode(',', $secondary_baskets);            
        }else{
            $professionDetail['pf_related_basket'] = '0';
        }
        $response = $this->ProfessionsRepository->saveProfessionDetail($professionDetail); 
        Cache::forget('professions');
        if ($response) {
            // $teenagers = $this->TeenagersRepository->getAllActiveTeenagersForNotification();
            // foreach ($teenagers AS $key => $value) {
            //     $message = 'Profession "' .$professionDetail['pf_name'].'" has been added/updated in ProTeen!';
            //     $return = Helpers::saveAllActiveTeenagerForSendNotifivation($value->id, $message);
            // }
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.professionupdatesuccess'), serialize($professionDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/professions".$postData['pageRank'])->with('success', trans('labels.professionupdatesuccess'));
        } else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_UPDATE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $response, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), serialize($professionDetail), $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/professions".$postData['pageRank'])->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $return = $this->ProfessionsRepository->deleteProfession($id);
        $return2 = $this->ProfessionHeadersRepository->deleteProfessionHeader($id);
        if ($return && $return2) {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.professiondeletesuccess'), '', $_SERVER['REMOTE_ADDR']);

            return Redirect::to("admin/professions")->with('success', trans('labels.professionandheaderdeletesuccess'));
        } 
        else if ($return) 
        {
            return Redirect::to("admin/professions")->with('success', trans('labels.professiondeletesuccess'));
        }else {
            Helpers::createAudit($this->loggedInUser->user()->id, Config::get('constant.AUDIT_ADMIN_USER_TYPE'), Config::get('constant.AUDIT_ACTION_DELETE'), Config::get('databaseconstants.TBL_PROFESSIONS'), $id, Config::get('constant.AUDIT_ORIGIN_WEB'), trans('labels.somethingwrong'), '', $_SERVER['REMOTE_ADDR']);
            return Redirect::to("admin/professions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function addbulk() {
        return view('admin.AddProfessionBulk');
    }

    public function saveprofessionbulk() {
        $response = '';
        $profession = Input::file('p_bulk');
        Excel::selectSheetsByIndex(0)->load($profession, function($reader) {
            foreach ($reader->toArray() as $row) {
                if ($row['basket_name'] != '' && $row['profession_name'] != '') {
                    $professtionDetail = [];
                    $professtionDetail['pf_name'] = trim($row['profession_name']);
                    $professtionDetail['pf_video'] = trim($row['profession_video']);
                    $basketDetail = [];
                    $basketDetail['b_name'] = $row['basket_name'];
                    $basketDetail['b_video'] = $row['basket_video'];
                    
                    $headerDetail = [];
                    $headerDetail[0] = $row['job_workplace'];
                    $headerDetail[1] = $row['skill_personality'];
                    $headerDetail[2] = $row['path_growth'];
                    $headerDetail[3] = $row['trends_infolinks'];

                    $response = $this->ProfessionsRepository->saveProfessionBulkDetail($professtionDetail, $basketDetail, $headerDetail);
                }
            }
        });
        return Redirect::to("admin/professions")->with('success', trans('labels.professionbulkupdatesuccess'));
    }

    public function getProfessionImageByName() {
        $allProfessions = Professions::where('deleted', 1)->get();
        $professions = $allProfessions->toArray();
        if (!empty($professions)) {
            foreach ($professions as $key => $data) {
                $imageName = $data['pf_name'];
                $replacedImage = str_replace(' ', '_', $imageName);
                $imageUrl = asset($this->getProfessionImagePath . $replacedImage . '.png');
                if (file_exists($this->getProfessionImagePath . $replacedImage . '.png')) {
                    $profession_logo_path = public_path($this->professionOriginalImageUploadPath . $replacedImage . '.png');
                    $profession_logo_thumb_path = public_path($this->professionThumbImageUploadPath . $replacedImage . '.png');
                    $img = Image::make($imageUrl);
                    $img->save($profession_logo_path);
                    $img->resize($this->professionThumbImageWidth, $this->professionThumbImageHeight)->save($profession_logo_thumb_path);

                    Professions::where('id', $data['id'])->update(['pf_logo' => $replacedImage . '.png']);
                } else {
                    $notImported[] = array('id' => $data['id'], 'profession' => $data['pf_name']);
                }
            }
        }
        echo "<pre>";
        print_r($notImported);
        exit;
    }

    public function exportData() {
        $result = $this->ProfessionsRepository->getExportProfession();
        Excel::create('profession', function($excel) use($result) {
            $excel->sheet('Sheet 1', function($sheet) use($result) {
                $sheet->fromArray($result);
            });
        })->export('xlsx');
    }

    public function getUserCompetitorsData() {
        $profession_Id = $_REQUEST['Professionid'];
        $pf_name = $this->ProfessionsRepository->getProfessionNameById($profession_Id);
        $data = Helpers::getCompetingUserList($profession_Id);

        return view('admin.UserCompetitorspopupDetail',compact('data','pf_name'));
    }

    public function exportCompetotorsData($id) {

         $data = Helpers::getCompetingUserList($id);
         $AllData = [];
         foreach ($data AS $key => $value) {
            $ProData = [];
            $ProData['Name'] = $value['name'];
            $ProData['Phone No'] = $value['t_phone'];
            $ProData['Email Id'] = $value['t_email'];
            if ($value['yourScore'] == '') {
                $value['yourScore'] = 'N/A';
            }
            $ProData['Score'] = $value['yourScore'];
            $ProData['Rank'] = $value['rank'];
            $AllData[] = $ProData;
         }
                Excel::create('competitors', function($excel) use($AllData) {
            $excel->sheet('Sheet 1', function($sheet) use($AllData) {
                $sheet->fromArray($AllData);
            });
        })->export('xlsx');
    }

}
