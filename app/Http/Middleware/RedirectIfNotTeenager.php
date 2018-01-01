<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use Redirect;
use Config;
use DB;
use Route;
use Request;
use App\CMS;
use App\Services\Teenagers\Contracts\TeenagersRepository;


class RedirectIfNotTeenager
{
	public function __construct(Guard $auth, TeenagersRepository $teenagersRepository)
    {
        $this->auth = $auth;
        $this->cmsObj = new CMS();
        $this->teenagersRepository = $teenagersRepository;
    }
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = 'teenager')
	{
		if (!Auth::guard($guard)->check()) {
	        return redirect('teenager/login');
	    }

	    $teenagerId = Auth::guard('teenager')->id();
	    $teenager = $this->teenagersRepository->saveTeenagerActivityDetail($teenagerId);
        $notDeleted = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('id', $teenagerId)->where('deleted', '1')->first();

        if(isset($notDeleted) && !empty($notDeleted)) {
            if($notDeleted->t_isverified == 0) {
                Auth::guard('teenager')->logout();
                return Redirect::to('teenager/login')->with('error', 'Your account has not verified, Please contact administrator for the same.');
                exit;
            }

            if(($notDeleted->t_name == '' || $notDeleted->t_lastname == '' || $notDeleted->t_pincode == '' || $notDeleted->t_email == '' || $notDeleted->t_gender == '' || $notDeleted->t_gender == 0 || $notDeleted->t_birthdate == '') && Request::path() != 'teenager/edit-profile' && Request::path() != 'teenager/my-profile' && Request::path() != 'teenager/get-phone-code-by-country-for-profile'){
                return Redirect::to('teenager/edit-profile')->with('error', 'Please fillup the all mandatory fields to use the application.');
                exit;
            }

        } else {
            Auth::guard('teenager')->logout();
            return Redirect::to('teenager/login')->with('error', 'Your account either deleted or inactive.');
            exit;
        }    
	    
	    return $next($request);
	}
}