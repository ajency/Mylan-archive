<?php

namespace App\Http\Middleware;

use Closure;

class ProjectPermission
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
                        '{hospitalslug}.{projectslug}.patients.index'=>['view'],
                        '{hospitalslug}.{projectslug}.patients.create'=>['edit'],
                        '{hospitalslug}.{projectslug}.patients.store'=>['edit'],
                        '{hospitalslug}.{projectslug}.patients.show'=>['view'],
                        '{hospitalslug}.{projectslug}.patients.edit'=>['edit'],
                        '{hospitalslug}.{projectslug}.patients.update'=>['edit'],

                        '{hospitalslug}.{projectslug}.projects.index'=>['view'],
                        '{hospitalslug}.{projectslug}.projects.create'=>['edit'],
                        '{hospitalslug}.{projectslug}.projects.store'=>['edit'],
                        '{hospitalslug}.{projectslug}.projects.show'=>['view'],
                        '{hospitalslug}.{projectslug}.projects.edit'=>['edit'],
                        '{hospitalslug}.{projectslug}.projects.update'=>['edit'],

                        '{hospitalslug}.{projectslug}.users.index'=>['view'],
                        '{hospitalslug}.{projectslug}.users.create'=>['edit'],
                        '{hospitalslug}.{projectslug}.users.store'=>['edit'],
                        '{hospitalslug}.{projectslug}.users.show'=>['view'],
                        '{hospitalslug}.{projectslug}.users.edit'=>['edit'],
                        '{hospitalslug}.{projectslug}.users.update'=>['edit'],

                        '{hospitalslug}.{projectslug}.submissions.index'=>['view'],
                        '{hospitalslug}.{projectslug}.submissions.show'=>['view'],

                        '{hospitalslug}.{projectslug}.user-access.destroy'=>['edit'],

                    ]; 
        $uri        =[  '{hospitalslug}/{projectslug}'=>['view'],
                        '{hospitalslug}/{projectslug}/dashbord'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/patient-reports'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/submissions'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/base-line-score'=>['view','edit'],
                        '{hospitalslug}/{projectslug}/patients/{id}/base-line-score-edit'=>['edit'],
                        '{hospitalslug}/{projectslug}/patients/{id}/validatereferncecode'=>['edit'],
                        ]; 

        $resourceName = $request->route()->getName(); 
        $uriPath =$request->route()->getPath();  

        $projectSlug = \Illuminate\Support\Facades\Route::input('projectslug'); 

        if($resourceName!='' && isset($resources[$resourceName]))
            $permission = $resources[$resourceName];
        elseif(isset($uri[$uriPath]))  
            $permission = $uri[$uriPath];
        else
            abort(403);

        
        // if(!hasProjectPermission($hospitalSlug ,$permission))
        //     abort(403);

        return $next($request);
    }
}
