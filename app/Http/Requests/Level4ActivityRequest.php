<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level4ActivityRequest extends Request
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
                'question_text'          => 'required',
                'options_text'  => 'required',
                'deleted'         => 'required'
            ];
    }

    public function messages() {
        return [
            'question_text.required' => trans('validation.activitytextrequired'),
            'options_text.required' => trans('validation.activityoptionrequired'),
            'deleted.required' => trans('validation.statusrequired')

        ];
    }
}
