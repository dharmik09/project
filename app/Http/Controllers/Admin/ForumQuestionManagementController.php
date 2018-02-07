<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ForumQuestionRequest;
use App\Http\Controllers\Controller;
use App\ForumQuestion;
use Auth;
use Input;
use Config;
use Request;
use Redirect;

class ForumQuestionManagementController extends Controller
{
    public function __construct() {
        $this->objForumQuestion = new ForumQuestion;
    }

    public function index() {
        $data = $this->objForumQuestion->getAllForumQuestion();
        // echo "<pre>";
        // print_r($data);
        // exit;
        return view('admin.ListForumAnswers', compact('data'));
    }

    public function add() {
        $data = [];
        return view('admin.EditForumQuestions', compact('data'));
    }

    public function edit($id) {
        $data = $this->objForumQuestion->find($id);
        return view('admin.EditForumQuestions', compact('data'));
    }

    public function save(ForumQuestionRequest $ForumQuestionRequest) {
        $data = [];
        $data['id'] = e(Input::get('id'));
        $data['fq_que'] = e(Input::get('fq_que'));
        $data['deleted'] = e(Input::get('deleted'));

        $response = $this->objForumQuestion->insertUpdate($data);
        if ($response) {
             return Redirect::to("admin/forumQuestions")->with('success',trans('labels.forumquestionupdatesuccess'));
        } else {
            return Redirect::to("admin/forumQuestions")->with('error', trans('labels.commonerrormessage'));
        }
    }

    public function delete($id) {
        $response = $this->objForumQuestion->deleteForumQuestion($id);
        if ($response) {
             return Redirect::to("admin/forumQuestions")->with('success',trans('labels.forumquestiondeletesuccess'));
        } else {
            return Redirect::to("admin/forumQuestions")->with('error', trans('labels.commonerrormessage'));
        }
    }
}