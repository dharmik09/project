<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class ForumQuestion extends Model
{
    protected $table = 'pro_fq_forum_questions';
    protected $fillable = ['fq_que','created_at','updated_at','deleted'];

    /**
     * Insert and Update ForumQuestion
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ForumQuestion::where('id', $data['id'])->update($data);
        } else {
            return ForumQuestion::create($data);
        }
    }

    /**
     * get all ForumQuestion Returns All Active and Inactive records
     */
    public function getAllForumQuestion() {
        $return = ForumQuestion::where('deleted','<>',Config::get('constant.DELETED_FLAG'))->get();
        return $return;
    }

    /**
     * get ForumQuestion data By Id
     */
    public function getForumQuestionById($id) {
        $return = ForumQuestion::where('id',$id)->where('deleted',Config::get('constant.ACTIVE_FLAG'))->first();
        return $return;
    }

    /**
     * Delete ForumQuestion
     */
    public function deleteForumQuestion($id) {
        $return = ForumQuestion::where('id',$id)->update(['deleted' => Config::get('constant.DELETED_FLAG')]);
        return $return;
    }

    public function answers(){
        return $this->hasMany(ForumAnswers::class, 'fq_que_id');
    }

    public function latestAnswer(){
        return $this->hasOne(ForumAnswers::class, 'fq_que_id')->orderBy('created_at','DESC');
    }


    /**
     * get all ForumQuestion Returns All Questions With Answers and Teenager data
     */
    public function getAllForumQuestionAndAnswersWithTeenagerData($limit,$skip) {
        $return = ForumQuestion::with(['latestAnswer' => function($query){
                                    $query->with('teenager');
                                }])
                                ->skip($skip)
                                ->take($limit)
                                ->where('deleted',Config::get('constant.ACTIVE_FLAG'))
                                ->get();
        return $return;
    }
}