<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForumAnswerRequest;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Auth;
use Config;
use Storage;
use Input;
use Helpers;
use Redirect;
use Request;
use App\ForumQuestion;
use App\ForumAnswers;
use Carbon\Carbon;  
use Crypt;

class ForumController extends Controller {

    public function __construct(TeenagersRepository $teenagersRepository) 
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objForumQuestion = new ForumQuestion();
        $this->objForumAnswers = new ForumAnswers();
    }

    /*
     * forum page 
     */
    public function index()
    {
        return view('teenager.forumQuestionLisintg');        
    }

    /*
     * forum questions page wise 
     */
    public function getIndex()
    {
        $pageNo = Input::get('page_no');
        $record = $pageNo * 5;
        
        $limit = 5;
        $forumQuestionData = $this->objForumQuestion->getAllForumQuestionAndAnswersWithTeenagerData($limit,$record);
        
        $view = view('teenager.basic.forumQuestion',compact('forumQuestionData'));
        $response['questionsCount'] = count($forumQuestionData);
        $response['questions'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;
    }

    /*
     * forum question by que id 
     */
    public function getQuestionByQuestionId($id)
    {
        $queId = Crypt::decrypt($id);
        $forumQuestionData = $this->objForumQuestion->find($queId);
        return view('teenager.singleQuestion', compact('forumQuestionData'));        
    }

    /*
     * forum question's Answer by que id 
     */
    public function getAnswerByQuestionId()
    {
        $queId = Input::get('queId');
        $pageNo = Input::get('page_no');
        $record = $pageNo * 5;
        $forumAnswerData = $this->objForumAnswers->getPageWiseForumAnswersWithTeenagerDataByQuestionId($queId,$record);
        $view = view('teenager.basic.forumAnswer',compact('forumAnswerData'));
        $response['answersCount'] = count($forumAnswerData);
        $response['answers'] = $view->render();
        $response['pageNo'] = $pageNo+1;
        return $response;     
    }

    /*
     * Save Forum question's Answer by que id 
     */
    public function saveForumAnswer(ForumAnswerRequest $ForumAnswerRequest)
    {
        $loggedInTeen = Auth::guard('teenager')->user();
        $data = [];
        $data['fq_ans'] = e(Input::get('answer'));
        $data['fq_que_id'] = e(Input::get('queId'));
        $data['fq_teenager_id'] = $loggedInTeen->id;

        $response = $this->objForumAnswers->insertUpdate($data);
        if ($response) {
             return Redirect::to("teenager/forum-question/".Crypt::encrypt($data['fq_que_id']))->with('success',trans('labels.forumanswerupdatesuccess'));
        } else {
            return Redirect::to("teenager/forum-question/".Crypt::encrypt($data['fq_que_id']))->with('error', trans('labels.commonerrormessage'));
        } 
    }


}
