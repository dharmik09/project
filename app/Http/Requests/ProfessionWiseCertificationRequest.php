<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class ProfessionWiseCertificationRequest extends Request
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
        return [
            'profession_id' => 'required',
            'certificate_id' => 'required',
        ];
    }

    public function messages() {
        return [
            'profession_id.required' => trans('validation.professionisrequired'),
            'certificate_id.required' => trans('validation.certificateisrequired')
        ];
    }
}
