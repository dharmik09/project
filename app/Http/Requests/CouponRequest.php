<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class CouponRequest extends Request
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
                'cp_code'         => 'required',
                'cp_image'          => 'mimes:jpeg,jpg,bmp,png',
                'cp_sponsor'    => 'required',
                'cp_validfrom'         => 'required',
                'cp_validto'         => 'required',
                'deleted' => 'required',
                 ];
        }
        else
        {
            return [
                'cp_code'         => 'required',
                'cp_image'          => 'mimes:jpeg,jpg,bmp,png',
                'cp_sponsor'    => 'required',
                'cp_validfrom'         => 'required',
                'cp_validto'         => 'required',
                'deleted' => 'required',

            ];
        }
    }

    public function messages() {
        return [
            'cp_name.required' => trans('validation.namerequiredfield'),
            'cp_code.required' => trans('validation.couponcoderequiredfield'),
            'cp_image.required' => trans('validation.photorequired'),
            'cp_image.mimes' => trans('validation.validphotorequired'),
            'cp_sponsor.required' => trans('validation.couponsponsorrequiredfield'),
            'cp_validfrom.required' => trans('validation.couponvalidfromrequiredfield'),
            'cp_validto.required' => trans('validation.couponvalidtorequiredfield'),
            'deleted.required' => trans('validation.statusrequired'),
           
        ];
    }
}
