<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ProfessionSubjectRequest extends Request
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
                'ps_name'      => 'required',
                'ps_image'      => 'required|mimes:jpeg,jpg,bmp,png',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'ps_name'      => 'required',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            'ps_name.required' => trans('validation.nameisrequired'),
            'ps_image.required' => trans('validation.imageisrequired')
        ];
    }
}
