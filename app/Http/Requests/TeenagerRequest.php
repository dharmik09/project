<?php

namespace App\Http\Requests;

use Config;
use App\Http\Requests\Request;

class TeenagerRequest extends Request
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
                't_name'         => 'required',
                //'t_nickname'     => 'required',
                //'t_email'         => "required | email | unique:".Config::get('databaseconstants.TBL_TEENAGERS').",t_email," . $this->get('id'),
                't_email'         => "required | email",
                't_uniqueid'   => 'required | unique:'.Config::get('databaseconstants.TBL_TEENAGERS').',t_uniqueid,' . $this->get('id'),
                'password'         => 'required',
                'confirm_password'    => 'required | same:password',
                't_phone'         => 'numeric | unique:'.Config::get('databaseconstants.TBL_TEENAGERS').',t_phone,' . $this->get('id'),
                //'t_phone'         => 'required | numeric',
                't_birthdate'         => 'required',
                't_photo'          => 'mimes:jpeg,jpg,bmp,png',
                //'t_level'    => 'required',
                't_pincode'  => 'required',
                't_credit'         => 'numeric ',
                't_boosterpoints'         => 'numeric ',
                'deleted' => 'required',
            ];
        }
        else
        {
            return [
                'id'=> 'required',
                't_name'         => 'required',
                //'t_nickname'     => 'required',
                 //'t_email'         => "required | email | unique:".Config::get('databaseconstants.TBL_TEENAGERS').",t_email," . $this->get('id'),
                't_email'         => "required | email",
                't_uniqueid'   => 'required | unique:'.Config::get('databaseconstants.TBL_TEENAGERS').',t_uniqueid,' . $this->get('id'),
                //'password'         => 'required',
                //'confirm_password'    => 'required | same:password',
                't_phone'         => 'numeric | unique:'.Config::get('databaseconstants.TBL_TEENAGERS').',t_phone,' . $this->get('id'),
                //'t_phone'         => 'required | numeric',
                't_birthdate'         => 'required',
                't_photo'          => 'mimes:jpeg,jpg,bmp,png',
                //'t_level'    => 'required',
                't_pincode'  => 'required',
                't_credit'         => 'numeric ',
                't_boosterpoints'         => 'numeric ',
                'deleted' => 'required',
            ];
        }
    }

    public function messages() {
        return [
            't_name.required' => trans('validation.namerequiredfield'),
            't_nickname.required' => trans('validation.nicknamerequiredfield'),
            't_email.required' => trans('validation.emailrequiredfield'),
            't_email.email' => trans('validation.validemail'),
            't_email.unique' => trans('validation.emailrepeat'),
            't_uniqueid.required' => trans('validation.uniqueidrequiredfield'),
            't_uniqueid.unique' => trans('validation.uniqueidrepeat'),
            'password.required' => trans('validation.passwordrequiredfield'),
            'confirm_password.required' => trans('validation.confirmpasswordrequiredfield'),
            'confirm_password.same' => trans('validation.passwordnotmatch'),
            //'t_phone.required' => trans('validation.phonerequiredfield'),
            't_phone.numeric' => trans('validation.digitsonly'),
            't_phone.unique' => trans('validation.phonerepeat'),
            't_birthdate.required' => trans('validation.bdaterequiredfield'),
            't_photo.required' => trans('validation.photorequired'),
            't_photo.mimes' => trans('validation.validphotorequired'),
            't_level.required' => trans('validation.levelrequired'),
            't_pincode.required' => trans('validation.pincoderequired'),
            't_credit.numeric' => trans('validation.digitsonly'),
            't_boosterpoints.numeric' => trans('validation.digitsonly'),
            'deleted.required' => trans('validation.statusrequired'),
        ];
    }
}
