<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class FAQRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
     public function rules()
    {
        if($this->get('id') == 0) {
            return [
              'f_question_text' => 'required',
              'f_que_answer' => 'required',
              'deleted' => 'required',
              'f_que_group' => 'required',
            ];
        } else {
            return [
              'f_question_text' => 'required',
              'f_que_answer' => 'required',
              'deleted' => 'required',
              'f_que_group' => 'required',
          ];
        }

    }
}
