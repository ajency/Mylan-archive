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
                        '{hospitalslug}.patients.index'=>['view'],
                        '{hospitalslug}.patients.create'=>['edit'],
                        '{hospitalslug}.patients.store'=>['edit'],
                        '{hospitalslug}.patients.show'=>['view'],
                        '{hospitalslug}.patients.edit'=>['edit'],
                        '{hospitalslug}.patients.update'=>['edit'],

                        '{hospitalslug}.projects.index'=>['view'],
                        '{hospitalslug}.projects.create'=>['edit'],
                        '{hospitalslug}.projects.store'=>['edit'],
                        '{hospitalslug}.projects.show'=>['view'],
                        '{hospitalslug}.projects.edit'=>['edit'],
                        '{hospitalslug}.projects.update'=>['edit'],

                        '{hospitalslug}.users.index'=>['view'],
                        '{hospitalslug}.users.create'=>['edit'],
                        '{hospitalslug}.users.store'=>['edit'],
                        '{hospitalslug}.users.show'=>['view'],
                        '{hospitalslug}.users.edit'=>['edit'],
                        '{hospitalslug}.users.update'=>['edit'],

                        '{hospitalslug}.submissions.index'=>['view'],
                        '{hospitalslug}.submissions.show'=>['view'],

                        '{hospitalslug}.user-access.destroy'=>['edit'],

                    ]; 
        $uri        =[  '{hospitalslug}'=>['view'],
                        '{hospitalslug}/dashbord'=>['view'],
                        '{hospitalslug}/patients/{id}/submission-reports'=>['view'],
                        '{hospitalslug}/patients/{id}/submissions'=>['view'],
                        '{hospitalslug}/patients/{id}/base-line-score'=>['view','edit'],
                        '{hospitalslug}/patients/{id}/base-line-score-edit'=>['edit'],
                        '{hospitalslug}/patients/{id}/validatereferncecode'=>['edit'],
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
