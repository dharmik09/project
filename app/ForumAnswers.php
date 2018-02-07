<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class ForumAnswers extends Model
{
    protected $table = 'pro_fq_forum_answers';
    protected $fillable = ['fq_que_id','fq_teenager_id','fq_ans','created_at','updated_at','deleted'];

    /**
     * Insert and Update ForumAnswers
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ForumAnswers::where('id', $data['id'])->update($data);
        } else {
            return ForumAnswers::create($data);
        }
    }

    /**
     * get all ForumAnswers
     */
    public function getAllForumAnswers() {
        $return = ForumAnswers::where('deleted',Config::get('constant.ACTIVE_FLAG'))->get();
        return $return;
    }

    /**
     * get ForumAnswers data By Id
     */
    public function getForumAnswersById($id) {
        $return = ForumAnswers::where('id',$id)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->first();
        return $return;
    }

    /**
     * get Forum All Answers data By Id
     */
    public function getForumAllAnswersById($id) {
        $return = ForumAnswers::where('id',$id)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->get();
        return $return;
    }

    /**
     * Delete ForumAnswers
     */
    public function deleteForumAnswers($id) {
        $return = ForumAnswers::where('id',$id)->update(['deleted' => Config::get('constant.DELETED_FLAG')]);
        return $return;
    }

    public function teenager(){
        return $this->belongsTo(Teenagers::class, 'fq_teenager_id');
    }

    /**
     * get Page Wise Forum Question's Answers 
     * Returns Answers for perticular Question With teenager data
     */
    public function getPageWiseForumAnswersWithTeenagerDataByQuestionId($queId,$skip) {
        $return = ForumAnswers::with('teenager')
                                ->skip($skip)
                                ->take(5)
                                ->where('fq_que_id',$queId)
                                ->where('deleted',Config::get('constant.ACTIVE_FLAG'))
                                ->orderBy('created_at','DESC')
                                ->get();
        return $return;
    }

    /**
     * get all Forum Question's Answers 
     * Returns Answers for perticular Question With teenager data
     */
    public function getAllForumAnswersWithTeenagerDataByQuestionId($queId) {
        $return = ForumAnswers::with('teenager')
                                ->where('fq_que_id',$queId)
                                ->where('deleted','<>',Config::get('constant.DELETED_FLAG'))
                                ->orderBy('created_at','DESC')
                                ->get();
        return $return;
    }
    
}
