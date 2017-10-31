<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level1CartoonIconRequest extends Request
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
                'l1ci_name'         => 'required',
                'l1ci_image'         => 'required',
                'ci_category'  =>  'required'
                
            ];
        }
        else
        {
            return [
                'l1ci_name'         => 'required',
                'ci_category'  =>  'required'
            ];
        }
    }

    public function messages() {
        return [
            'l1ci_name.required' => trans('validation.cartoonnametextrequired'),
            'l1ci_image.required' => trans('validation.cartoonimagerequired'),
            'ci_category.required' => trans('validation.cartooncategoryrequired'),
        ];
    }
}
