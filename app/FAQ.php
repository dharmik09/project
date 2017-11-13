<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class FAQ extends Model
{
    protected $table = 'pro_f_faq';
    protected $fillable = ['id', 'f_question_text','f_que_answer','f_que_group', 'f_photo', 'deleted'];


    public function getAllFAQ()
    {
        $faqDetail = FAQ::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $faqDetail;
    }

    public function saveFAQDetail($faqData)
    {
        if ($faqData['id'] != '' && $faqData['id'] > 0) {
            $return = $this->where('id', $faqData['id'])->update($faqData);
        } else {
            $return = $this->create($faqData);
        }
        return $return;
    }

    public function deleteFAQ($id) {
        $faq         = $this->find($id);
        $faq->deleted = config::get('constant.DELETED_FLAG');
        $response          = $faq->save();
        if($response)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getAllFAQDetail() {
        $result = FAQ::selectRaw('f_que_group, GROUP_CONCAT(f_question_text SEPARATOR "###")  AS f_question_text, GROUP_CONCAT(f_que_answer SEPARATOR "###")  AS f_que_answer , GROUP_CONCAT(f_photo SEPARATOR "###")  AS f_photo')
                ->where('deleted' ,'1')
                ->groupBy('f_que_group')
                ->orderBy('id')
                ->get();

        return $result;
    }

}