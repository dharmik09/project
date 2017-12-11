<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class HelptextRequest extends Request
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
                'h_title'      => 'required',
                'h_slug'      => 'required',
                'h_description' => 'required',
                'h_page' => 'required',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'h_title'      => 'required',
                'h_slug'      => 'required',
                'h_description' => 'required',
                'h_page' => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'h_title.required' => trans('validation.nameisrequired'),
            'h_slug.required' => trans('validation.titleisrequired'),
            'h_description.required' => trans('validation.imageisrequired'),
            'h_page.required' => trans('validation.descriptionisrequired'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
