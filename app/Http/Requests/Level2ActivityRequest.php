<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level2ActivityRequest extends Request
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
                'l2ac_text'          => 'required',
                'l2op_option'  => 'required',
                'deleted'         => 'required'
            ];
    }

    public function messages() {
        return [
            'l2ac_text.required' => trans('validation.activitytextrequired'),
            'l2op_option' => trans('validation.activityoptionrequired'),
            'deleted.required' => trans('validation.statusrequired')

        ];
    }
}
