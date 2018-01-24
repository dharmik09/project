<?php

namespace App\Http\Controllers\Teenager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Helpers;
use Config;
use Mail;
use Session;
use Storage;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Redirect;
use Response;
use Input;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->middleware('teenager');
    }

    public function setSoundOnOff($data) {
        $response = [ 'status' => 0, 'message' => trans('appmessages.default_error_msg') ];
        $user = Auth::guard('teenager')->user();
        if($user) {
            $userId = $user->id;
            $sound = ($data != "" && $data == "1") ? 1 : 0;
            $user->is_sound_on = $sound;
            $user->save();
            $response['message'] = "Success";
            $response['status'] = 1;
            $response['sound'] = $sound;
        } else {
            $response['message'] = "Something went wrong!";
        }
        return response()->json($response, 200);
        exit;
    }
}
