<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class FeedbackQuestionRequest extends Request
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

            return [
                'fq_level'         => 'required',
                'fq_question'         => 'required',
                'deleted' => 'required',
                 ];
    }

    public function messages() {
        return [
            'fq_level.required' => trans('validation.feedbacklevelrequiredfield'),
            'fq_question.required' => trans('validation.feedbackquestionrequiredfield'),
            'deleted.required' => trans('validation.statusrequired'),

        ];
    }
}
