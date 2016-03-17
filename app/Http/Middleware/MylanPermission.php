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

        if($uriPath=='admin' && \Auth::user()->type!='mylan_admin')
            return redirect(url('/admin/login-links'));

        if(!hasMylanPermission())
            abort(403);

        return $next($request);
    }
}
