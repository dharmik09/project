<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class TestimonialRequest extends Request
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
                't_name'      => 'required',
                't_title'      => 'required',
                't_image'      => 'required',
                't_description' => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                't_name'      => 'required',
                't_title'      => 'required',
                't_description' => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            't_name.required' => trans('validation.nameisrequired'),
            't_title.required' => trans('validation.titleisrequired'),
            't_image.required' => trans('validation.imageisrequired'),
            't_description.required' => trans('validation.descriptionisrequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
