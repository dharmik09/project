<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class SystemLevelRequest extends Request
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
        if($this->get('id') == 0)
        {
            return [
                'sl_name'         => 'required',
                'sl_info'         => 'required',
                'sl_boosters'         => 'numeric',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'sl_name'         => 'required',
                'sl_info'         => 'required',
                'sl_boosters'         => 'numeric',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'sl_name.required' => trans('validation.namerequiredfield'),
            'sl_info.required' => trans('validation.systemlevelinforequiredfield'),
            'sl_boosters.numeric' => trans('validation.digitsonly'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
