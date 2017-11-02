<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class VideoRequest extends Request
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
        if($this->get('id') == 0) {
            return [
              'v_link' => 'required',
              'v_title' => 'required',
              'deleted' => 'required',
              'v_photo' => 'required|mimes:jpeg,jpg,bmp,png',
            ];
        } else {
            return [
              'v_link' => 'required',
              'v_title' => 'required',
              'deleted' => 'required',
              'v_photo' => 'mimes:jpeg,jpg,bmp,png',
          ];
        }

    }
}
