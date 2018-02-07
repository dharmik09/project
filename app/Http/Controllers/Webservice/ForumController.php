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
use App\ForumQuestion;
use App\ForumAnswers;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ForumController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) {
    	$this->teenagersRepository = $teenagersRepository;
        $this->objForumQuestion = new ForumQuestion();
        $this->objForumAnswers = new ForumAnswers();
        $this->log = new Logger('api-forum-controller');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));    
    }

    public function getForumQuestionPageWise(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getForumQuestion'));
        if($request->userId != "" && $teenager) {
            
            $record = 0;
            if($request->pageNo != '' && $request->pageNo > 1){
                $record = ($request->pageNo-1) * 5;
            }

            $limit = 5;
            $data = $this->objForumQuestion->getAllForumQuestionAndAnswersWithTeenagerData($limit, $record);
            
            if($data){
	            foreach($data as $key => $value){
	            	if(isset($value->latestAnswer->teenager)){

		            	if(isset($value->latestAnswer->teenager)){

			                if(isset($value->latestAnswer->teenager->t_photo) && $value->latestAnswer->teenager->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->latestAnswer->teenager->t_photo) > 0) {
			                    $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->latestAnswer->teenager->t_photo;
			                } else {
			                    $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
			                }
			                $value->latestAnswer->teenager_image = Storage::url($teenPhoto);
			                $value->latestAnswer->teenager_fname = $value->latestAnswer->teenager->t_name;
			                $value->latestAnswer->teenager_lname = $value->latestAnswer->teenager->t_lastname;
			                unset($value->latestAnswer->teenager);
			                
		            	}
		            }
	            }
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
            $this->log->error('Parameter missing error' , array('api-name'=> 'getForumQuestion'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function getForumQuestionByQuestionIdPageWise(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'getForumAnswer'));
        if($request->userId != "" && $teenager) {
            if($request->queId != "" && $teenager) {
	            $record = 0;
	            if($request->pageNo != '' && $request->pageNo > 1){
	                $record = ($request->pageNo-1) * 5;
	            }

	            $data = $this->objForumAnswers->getPageWiseForumAnswersWithTeenagerDataByQuestionId($request->queId, $record);            
	            if($data){
		            foreach($data as $key => $value){
		            
		            	if(isset($value->teenager)){

			            	if(isset($value->teenager)){

				                if(isset($value->teenager->t_photo) && $value->teenager->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$value->teenager->t_photo) > 0) {
				                    $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->teenager->t_photo;
				                } else {
				                    $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
				                }
				                $value->teenager_image = Storage::url($teenPhoto);
				                $value->teenager_fname = $value->teenager->t_name;
				                $value->teenager_lname = $value->teenager->t_lastname;
				                unset($value->teenager);

			            	}
			            }
		            
		            }
	                $response['data'] = $data;
	            }
	            else{
	                $response['data'] = trans('appmessages.data_empty_msg');
	            }

	            $response['status'] = 1;
	            $response['login'] = 1;
	            $response['message'] = trans('appmessages.default_success_msg');
	        }
	        else
	        {
	        	$response['message'] = trans('appmessages.missing_data_msg');
	        }

            $this->log->info('Response for fetch Forum Question\'s answer page wise' , array('api-name'=> 'getForumAnswer'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'getForumAnswer'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }

    public function saveForumQuestionByQuestionId(Request $request) {
        $response = [ 'status' => 0, 'login' => 0, 'message' => trans('appmessages.default_error_msg')];
        $teenager = $this->teenagersRepository->getTeenagerById($request->userId);
        $this->log->info('Get teenager detail for userId'.$request->userId , array('api-name'=> 'saveForumAnswer'));
        if($request->userId != "" && $teenager) {
            if($request->queId != "" && $request->answer != "") {

            	$answerData['fq_ans'] = $request->answer;
            	$answerData['fq_que_id'] = $request->queId;
            	$answerData['fq_teenager_id'] = $teenager->id;
	            
	            $data = $this->objForumAnswers->insertUpdate($answerData);            
	            if($data){
	                $response['status'] = 1;
                    $response['message'] = trans('appmessages.default_success_msg');
                }
                else{
                    $response['status'] = 0;
                    $response['message'] = trans('appmessages.default_error_msg');
                }
	            $response['login'] = 1;
	        }
	        else
	        {
	        	$response['message'] = trans('appmessages.missing_data_msg');
	        }

            $this->log->info('Response for Forum Question\'s Answer save or not' , array('api-name'=> 'saveForumAnswer'));
        } else {
            $this->log->error('Parameter missing error' , array('api-name'=> 'saveForumAnswer'));
            $response['message'] = trans('appmessages.missing_data_msg');
        }
        return response()->json($response, 200);
    }
}
