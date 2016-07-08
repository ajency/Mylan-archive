<?php

namespace App\Http\Middleware;

use Closure;

class MylanPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uriPath =$request->route()->getPath();  

        if($uriPath=='admin' && (\Auth::user()->type=='hospital_user' || \Auth::user()->type=='project_user' ))
            return redirect(url('/admin/login-links'));
        
        $commonUri = ['admin/hospital/{hospital}/patients','admin/changepassword'];
        if(\Auth::user()->type=='hospital_user' && in_array($uriPath, $commonUri))
        {
             // do nothing
        }
        elseif(!hasMylanPermission())
            abort(403);

        return $next($request);
    }
}
