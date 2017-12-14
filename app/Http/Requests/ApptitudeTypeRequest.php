<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ApptitudeTypeRequest extends Request
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
                'apt_name' => 'required',
                'apt_slug' => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'apt_name' => 'required',
                'apt_slug' => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'apt_name.required' => trans('validation.namerequiredfield'),
            'apt_slug.required' => trans('validation.slugisrequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
