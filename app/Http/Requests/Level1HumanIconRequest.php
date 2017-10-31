<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level1HumanIconRequest extends Request
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
                'hi_name'         => 'required',
                'hi_image'         => 'required',
                'hi_category'  =>  'required'
            ];
        }
        else
        {
            return [
                'hi_name'         => 'required',
                'hi_category'  =>  'required'
            ];
        }
    }

    public function messages() {
        return [
            'hi_name.required' => trans('validation.humannametextrequired'),
            'hi_image.required' => trans('validation.humanimagerequired'),
            'hi_category.required' => trans('validation.humancategoryrequired'),

        ];
    }
}
