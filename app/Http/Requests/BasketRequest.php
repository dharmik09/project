<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class BasketRequest extends Request
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
                'b_name'         => 'required',
                'b_logo'          => 'mimes:jpeg,jpg,bmp,png',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'b_name'         => 'required',
                'b_logo'          => 'mimes:jpeg,jpg,bmp,png',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'b_name.required' => trans('validation.namerequiredfield'),
            'b_intro.required' => trans('validation.basketintrorequiredfield'),
            'b_logo.required' => trans('validation.photorequired'),
            'b_logo.mimes' => trans('validation.validphotorequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
