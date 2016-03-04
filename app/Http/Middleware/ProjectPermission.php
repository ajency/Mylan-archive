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
                        '{hospitalslug}.{projectslug}.patients.index'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.patients.create'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.patients.store'=>['edit'],
                        '{hospitalslug}.{projectslug}.patients.show'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.patients.edit'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.patients.update'=>['edit'],

                        '{hospitalslug}.{projectslug}.projects.index'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.projects.create'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.projects.store'=>['edit'],
                        '{hospitalslug}.{projectslug}.projects.show'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.projects.edit'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.projects.update'=>['edit'],

                        '{hospitalslug}.{projectslug}.users.index'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.users.create'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.users.store'=>['edit'],
                        '{hospitalslug}.{projectslug}.users.show'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.users.edit'=>['view','edit'],
                        '{hospitalslug}.{projectslug}.users.update'=>['edit'],

                        '{hospitalslug}.{projectslug}.submissions.index'=>['view'],
                        '{hospitalslug}.{projectslug}.submissions.show'=>['view'],

                        '{hospitalslug}.{projectslug}.user-access.destroy'=>['edit'],

                    ]; 
                    
        $uri        =[  '{hospitalslug}/{projectslug}'=>['view'],
                        '{hospitalslug}/{projectslug}/dashboard'=>['view'],
                        '{hospitalslug}/{projectslug}/flags'=>['view'],
                        '{hospitalslug}/{projectslug}/reports'=>['view'],
                        '{hospitalslug}/{projectslug}/submissions/{id}/updatesubmissionstatus'=>['edit'],

                        '{hospitalslug}/{projectslug}/getsubmissionlist'=>['view'],
                        '{hospitalslug}/{projectslug}/getpatientsummarylist'=>['view'],
                        '{hospitalslug}/{projectslug}/notifications'=>['view'],
                        '{hospitalslug}/{projectslug}/questionnaire-setting'=>['view','edit'],


                        '{hospitalslug}/{projectslug}/patients/{id}/patient-reports'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/submissions'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/flags'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/base-line-score/list'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/base-line-score/{responseId}'=>['view'],
                        '{hospitalslug}/{projectslug}/patients/{id}/base-line-score'=>['view','edit'],
                        '{hospitalslug}/{projectslug}/patients/{id}/base-line-score-edit'=>['view','edit'],
                        '{hospitalslug}/{projectslug}/patients/{id}/validatereferncecode'=>['edit'],
                        ]; 

 
        $resourceName = $request->route()->getName(); 
        $uriPath =$request->route()->getPath();  

        $hospitalSlug = \Illuminate\Support\Facades\Route::input('hospitalslug'); 
        $projectSlug = \Illuminate\Support\Facades\Route::input('projectslug'); 

        if($resourceName!='' && isset($resources[$resourceName]))
            $permission = $resources[$resourceName];
        elseif(isset($uri[$uriPath]))  
            $permission = $uri[$uriPath];
        else
            abort(403);

        
        if(!hasProjectPermission($hospitalSlug ,$projectSlug,$permission))
            abort(403);

        return $next($request);
    }
}
