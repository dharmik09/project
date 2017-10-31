<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level1ActivityRequest extends Request
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
                'l1ac_text'    => 'required',
                'l1op_option'  => 'required',
                'deleted'      => 'required'
            ];
    }

    public function messages() {
        return [
            'l1ac_text.required' => trans('validation.activitytextrequired'),
            'l1op_option' => trans('validation.activityoptionrequired'),
            'deleted.required' => trans('validation.statusrequired')

        ];
    }
}
