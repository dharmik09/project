<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class TeenagerBulkRequest extends Request
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
                'teenager_bulk' => 'required|mimes:csv,txt',
            ];
    }

    public function messages() {
        return [
            'teenager_bulk.required' => trans('validation.bulkrequired'),
            'teenager_bulk.mimes' => trans('validation.validbulkrequired')
        ];
    }
}
