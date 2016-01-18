<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Hospital;
use App\Projects;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($hospitalSlug)
    {
        
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $projects = Projects::where('hospital_id',$hospital['id'])->orderBy('created_at')->get()->toArray();
         
        return view('hospital.project-list')->with('active_menu', 'project')
                                          ->with('hospital', $hospital)
                                          ->with('logoUrl', $logoUrl)
                                          ->with('projects', $projects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($hospitalSlug)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        return view('hospital.project-add')->with('active_menu', 'project')
                                           ->with('hospital', $hospital)
                                           ->with('logoUrl', $logoUrl);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $hospitalSlug)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  

        $project = new Projects;
        $name =  ucfirst($request->input('name'));
        $project->name = $name;
        $project->hospital_id = $hospital['id'];
        $project->description = $request->input('description');
        $project->project_slug = str_slug($name);
        $project->save();
        $projectId = $project->id;
         
        return redirect(url($hospitalSlug . '/projects/' . $projectId . '/edit'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hospitalSlug,$projectId)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = Projects::find($projectId)->toArray(); 
         
        return view('hospital.project-edit')->with('active_menu', 'project')
                                           ->with('project', $project)
                                           ->with('hospital', $hospital)
                                           ->with('logoUrl', $logoUrl);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$hospitalSlug, $projectId)
    {
        $project = Projects::find($projectId);
        $name =  ucfirst($request->input('name'));
        $project->name = $name;
        $project->description = $request->input('description');
        $project->project_slug = str_slug($name);
        $project->save();
         
        return redirect(url($hospitalSlug . '/projects/' . $projectId . '/edit'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
