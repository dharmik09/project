<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ProfessionTagRequest extends Request
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
                'pt_name'      => 'required',
                'pt_description'      => 'required',
                'pt_image'      => 'required|mimes:jpeg,jpg,bmp,png',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'pt_name'      => 'required',
                'pt_description'      => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'pt_name.required' => trans('validation.nameisrequired'),
            'pt_description.required' => trans('validation.descriptionisrequired'),
            'pt_image.required' => trans('validation.imageisrequired')
        ];
    }
}
