<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Auth;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($this->auth->viaRemember()) 
            return $next($request);

        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {  
                //return redirect()->guest('auth/login');
            }
 
        }

        if (!Auth::check())
        {
            Auth::logout();
           $routePrefix = $request->route()->getPrefix(); 

            if(str_contains($routePrefix, 'admin'))
                return redirect('/admin/login');
            elseif(str_contains($routePrefix, '{hospitalslug}/{projectslug}'))
            {
                $hospitalslug= $request->hospitalslug;
                $projectslug= $request->projectslug;

                return redirect($hospitalslug.'/'.$projectslug.'/login');
            }
            elseif(str_contains($routePrefix, '/{hospitalslug}'))
            {
                $hospitalslug= $request->hospitalslug;
                return redirect($hospitalslug.'/login');
            }
            else
            {
                return redirect('/auth/login');
            }
        }

        return $next($request);
    }
}
