<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class AppVersionRequest extends Request
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
            'force_update' => 'required',
            'device_type' => 'required',
            'message' => 'required',
            'app_version' => 'required|numeric',
        ];
    }
}
