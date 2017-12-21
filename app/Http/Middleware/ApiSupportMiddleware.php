<?php

namespace App\Http\Middleware;

use Closure;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Http\Request;
use App\TeenagerLoginToken;
use App\Teenagers;
use App\Services\Teenagers\Contracts\TeenagersRepository;

class ApiSupportMiddleware
{
    protected $teenagersRepository;

    public function __construct(TeenagersRepository $teenagersRepository)
    {
        $this->teenagersRepository = $teenagersRepository;
        $this->objTeenagerLoginToken = new TeenagerLoginToken();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //Check user active / verified or not
        if (!Teenagers::teenagerActiveStatus($request->userId)) {
            return response()->json([
                'status' => 0,
                'login' => 0,
                'message' => trans('appmessages.invalid_userid_msg') . ' or ' . trans('appmessages.notvarified_user_msg')
            ], 401);
        }
        //Check login token active or not
        if (!$this->objTeenagerLoginToken->validateAccessToken($request->userId, $request->loginToken)) {
            return response()->json([
                'status' => 0, 
                'login' => 0, 
                'message' => trans('appmessages.invalid_access')
            ], 401);
        }
        //Save user's last login activity
        $this->teenagersRepository->saveTeenagerActivityDetail($request->userId);
        
        return $next($request);
    }
}