<?php

namespace App\Http\Middleware;

use Closure;

class Permission
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
        //  $resources =[

        //              'admin.hospitals.index'=>['view'],
        //              'admin.hospitals.create'=>['edit'],
        //              'admin.hospitals.store'=>['edit'],
        //              'admin.hospitals.show'=>['view'],
        //              'admin.hospitals.edit'=>['edit'],
        //              'admin.hospitals.update'=>['edit'],

        //              'admin.users.index'=>['view'],
        //              'admin.users.create'=>['edit'],
        //              'admin.users.store'=>['edit'],
        //              'admin.users.show'=>['view'],
        //              'admin.users.edit'=>['edit'],
        //              'admin.users.update'=>['edit'],

        //             ];

        // $uri        =[  'admin'=>['view'],
        //                 'admin/dashboard'=>['view'],
        //                 'admin/hospital/{hospital}/uploadlogo'=>['edit'],
        //                 'admin/hospital/{hospital}/deletelogo'=>['edit'],
                    
        //                 ]; 

        // $resourceName = $request->route()->getName();
        // $uriPath =$request->route()->getPath();  

        // if($resourceName!='' && isset($resources[$resourceName]))
        //     $permission = $resources[$resourceName];
        // elseif(isset($uri[$uriPath]))  
        //     $permission = $uri[$uriPath];
        // else
        //     abort(403);

        
        // if(!hasMylanPermission($permission))
        //     abort(403);

         if((\Auth::user()->type!='hospital_admin' || \Auth::user()->type!='project_admin' ))
            abort(403);

        return $next($request);
    }
}
