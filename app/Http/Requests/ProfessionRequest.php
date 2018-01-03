<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ProfessionRequest extends Request
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
                'pf_name' => 'required',
                'pf_logo' => 'mimes:jpeg,jpg,bmp,png',
                'normal' => 'mimes:mp4,3gp,wmv,mkv',
                'youtube' => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'pf_name' => 'required',
                'pf_logo' => 'mimes:jpeg,jpg,bmp,png',
                'normal' => 'mimes:mp4,3gp,wmv,mkv',
                'youtube' => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'pf_name.required' => trans('validation.namerequiredfield'),
            'pf_logo.required' => trans('validation.photorequired'),
            'pf_logo.mimes' => trans('validation.validphotorequired'),
            'normal.mimes' => trans('validation.validvideorequired'),
            'youtube.required' => trans('validation.youtuberequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
