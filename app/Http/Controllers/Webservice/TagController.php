<?php

namespace App\Http\Controllers\Webservice;

use App\Http\Controllers\Controller;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Config;
use Storage;
use Helpers;  
use Auth;
use Input;
use Redirect;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\ProfessionTag;

class TagController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) {
        $this->teenagersRepository = $teenagersRepository;
        $this->professionTag = new ProfessionTag();
		$this->log = new Logger('api-level1-activity-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }


    public function getTagDetails(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getForumQuestion'));
        if($request->userId != "" && $teenager) {
	        if($request->slug != "") {
	            $records = 0;
	            if($request->pageNo != '' && $request->pageNo > 1){
	                $records = ($request->pageNo-1) * 10;
	            }
	            $next = 0;
	            $nextData = $this->professionTag->getProfessionTagBySlugWithProfessionAndAttemptedProfessionByPage($request->slug,$teenager->id,($records+10));
	            if(isset($nextData->professionTags)){
	            	if(count($nextData->professionTags)>0){
	            		$next = 1;
	            	}
	            }
	            $data = $this->professionTag->getProfessionTagBySlugWithProfessionAndAttemptedProfessionByPage($request->slug,$teenager->id,$records);
	            
	            if($data){
	    			if(isset($data->pt_image) && $data->pt_image != '' && Storage::size(Config::get('constant.PROFESSION_TAG_ORIGINAL_IMAGE_UPLOAD_PATH').$data->pt_image) > 0) {
	                    $data->pt_image = Storage::url(Config::get('constant.PROFESSION_TAG_ORIGINAL_IMAGE_UPLOAD_PATH').$data->pt_image);
	                } else {
	                    $data->pt_image = Storage::url(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png');
	                }

	            	$profession = [];
	            	$getTeenagerHML = Helpers::getTeenagerMatchScale($request->userId);
            		$match = $nomatch = $moderate = [];

		            foreach($data->professionTags as $key => $value) {
		            	$professionData = [];
		            	if(isset($value->profession)){
		            		unset($value->profession->professionAttempted);
		            		$professionData = $value->profession;
		            		
			                $youtubeId = Helpers::youtube_id_from_url($value->profession->pf_video);
			                if($youtubeId != '') {
			                    $professionData['pf_video'] = $youtubeId;
			                    $professionData['type_video'] = '1'; //Youtube
			                } else {
			                    $professionData['type_video'] = '2'; //Dropbox
			                }
			                
			                $professionData['matched'] = isset($getTeenagerHML[$value->profession->id]) ? $getTeenagerHML[$value->profession->id] : '';
		                    if($professionData['matched'] == "match") {
		                        $match[] = $value->profession->id;
		                    } else if($professionData['matched'] == "nomatch") {
		                        $nomatch[] = $value->profession->id;
		                    } else if($professionData['matched'] == "moderate") {
		                        $moderate[] = $value->profession->id;
		                    } else {
		                        $notSetArray[] = $value->profession->id;
		                    }
		                    $professionComplete = Helpers::getProfessionCompletePercentage($request->userId, $value->profession->id); 
		                    $professionData['completed'] = (isset($professionComplete) && $professionComplete >= 100) ? 1 : 0;
			                $profession[] = $professionData;	                
		            	}
		            }

		            $response['strong'] = count($match);
		            $response['potential'] = count($moderate);
		            $response['unlikely'] = count($nomatch);
		            $response['next'] = $next;
		            
		            $data['profession'] = $profession;
		            unset($data->professionTags);
	                $response['data'] = $data;
	            }
	            else{
	                $response['data'] = trans('appmessages.data_empty_msg');
	            }

	            $response['status'] = 1;
	            $response['login'] = 1;
	            $response['message'] = trans('appmessages.default_success_msg');
	            $this->log->info('Response for fetch Forum Question page wise' , array('api-name'=> 'getForumQuestion'));
	        } else {
	        	$response['status'] = 0;
	            $response['login'] = 1;
	            $response['message'] = trans('appmessages.missing_data_msg');
	            $this->log->error('Parameter missing error' , array('api-name'=> 'getForumQuestion'));
	        }
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getForumQuestion'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

}
