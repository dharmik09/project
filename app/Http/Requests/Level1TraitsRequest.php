<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class Level1TraitsRequest extends Request
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
                'tqq_text'    => 'required',
                'tqo_option'  => 'required',
                'deleted'      => 'required'
            ];
    }

    public function messages() {
        return [
            'tqq_text.required' => trans('validation.traitstextrequired'),
            'tqo_option' => trans('validation.traitsoptionrequired'),
            'deleted.required' => trans('validation.statusrequired')

        ];
    }
}
