<?php

namespace App\Http\Middleware;

use Closure;

class HospitalPermission
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
        $resources =[

                        '{hospitalslug}.projects.index'=>['view','edit'],
                        '{hospitalslug}.projects.create'=>['edit'],
                        '{hospitalslug}.projects.store'=>['edit'],
                        '{hospitalslug}.projects.show'=>['view','edit'],
                        '{hospitalslug}.projects.edit'=>['view','edit'],
                        '{hospitalslug}.projects.update'=>['edit'],

                        '{hospitalslug}.users.index'=>['view','edit'],
                        '{hospitalslug}.users.create'=>['edit'],
                        '{hospitalslug}.users.store'=>['edit'],
                        '{hospitalslug}.users.show'=>['view','edit'],
                        '{hospitalslug}.users.edit'=>['view','edit'],
                        '{hospitalslug}.users.update'=>['edit'],

                        '{hospitalslug}.user-access.destroy'=>['edit'],

                        '{hospitalslug}.attributes.destroy'=>['edit'],

                    ]; 
        $uri        =[  '{hospitalslug}'=>['view','edit'],
                        '{hospitalslug}/dashboard'=>['view','edit'],
                        '{hospitalslug}/changepassword'=>['view','edit'],
                        '{hospitalslug}/patients/{id}/validatereferncecode'=>['edit'],
                        '{hospitalslug}/users/{id}/authuseremail'=>['edit'],
                        '{hospitalslug}/delete-user-access/{id}'=>['edit'],
                        ]; 

        $resourceName = $request->route()->getName(); 
        $uriPath =$request->route()->getPath();  
        
        $hospitalSlug = \Illuminate\Support\Facades\Route::input('hospitalslug'); 

        if($resourceName!='' && isset($resources[$resourceName]))
            $permission = $resources[$resourceName];
        elseif(isset($uri[$uriPath]))  
            $permission = $uri[$uriPath];
        else
            abort(403);

        
        if(!hasHospitalPermission($hospitalSlug ,$permission))
            abort(403);

        return $next($request);
    }
}
