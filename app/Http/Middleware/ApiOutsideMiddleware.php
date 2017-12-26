<?php

namespace App\Http\Middleware;

use Closure;
use Redirect;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Http\Request;

class ApiOutsideMiddleware
{
    public function __construct()
    {
        //
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
        //Check Authorization token active or not
        if (apache_request_headers()['Authorization'] != "YWRtaW4udXNlckBwcm90ZWVuLmNvbTokMnkkMTAkWWlqUTNNR2owOEtuTEhtdS9JS3E4dU55dFFMajBwMHV4VVhBUjZ5eHNjSDlrMUxIVzZBdDI") {
            return response()->json([
                'status' => 0, 
                'login' => 0,
                'message' => "Authorization header is not valid"
            ], 401);
        }

        return $next($request);
    }
}