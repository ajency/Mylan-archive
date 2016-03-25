<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Hospital;
use App\Projects;
use App\Attributes;
use \Session;

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

        $requestData = $request->all();
        
        $project = new Projects;
        $name =  ucfirst($requestData['name']);
        $project->name = $name;
        $project->hospital_id = $hospital['id'];
        $project->description = $requestData['description'];
        $project->project_slug = str_slug($name);
        $project->save();
        $projectId = $project->id;

        $attributeNames = $requestData['attribute_name'];
        $controltypes = $requestData['controltype'];
        $controltypevalues = (isset($requestData['controltypevalues']))?$requestData['controltypevalues']:[];
        $validate = (isset($requestData['validate']))?$requestData['validate']:[];

        $objecttype = 'Project';
        $attributes = [];
        if(!empty($attributeNames))
        {

            foreach ($attributeNames as $key => $attributeName) {
                 
                 if($attributeName=='')
                    continue;

                $controlTypeDefaultValues = (isset($controltypevalues[$key]))?$controltypevalues[$key]:'';
                $validateControlType = (isset($validate[$key]))?$validate[$key]:'';
                $attributes[] = new Attributes(['label' => ucfirst($attributeName), 'control_type' => $controltypes[$key], 'values' => $controlTypeDefaultValues,'object_type' => $objecttype, 'validate' => $validateControlType, 'object_id' => $projectId]);
            }
        }


        if (!empty($attributes)) {
            $project->attributes()->saveMany($attributes);
        }

        Session::flash('success_message','Project successfully created.');
         
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

        $project = Projects::find($projectId); 
        $projectAttributes = $project->attributes->toArray();
        
         
        return view('hospital.project-edit')->with('active_menu', 'project')
                                           ->with('project', $project->toArray())
                                           ->with('projectAttributes', $projectAttributes)
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
        $requestData = $request->all();

        $project = Projects::find($projectId);
        $name =  ucfirst($requestData['name']);
        $project->name = $name;
        $project->description = $requestData['description'];
        $project->project_slug = str_slug($name);
        $project->save();

        $attributeIds = $requestData['attribute_id'];
        $attributeNames = $requestData['attribute_name'];
        $controltypes = $requestData['controltype'];
        $controltypevalues = (isset($requestData['controltypevalues']))?$requestData['controltypevalues']:[];
        $validate = (isset($requestData['validate']))?$requestData['validate']:[];  //dd($validate);

        $objecttype = 'Project';
        $attributes = [];
        if(!empty($attributeNames))
        {

            foreach ($attributeNames as $key => $attributeName) {
                 
                if($attributeName=='')
                    continue;

                $controlTypeDefaultValues = (isset($controltypevalues[$key]))?$controltypevalues[$key]:'';
                $validateControlType = (isset($validate[$key]))? $validate[$key] :'';
                if($attributeIds[$key]=='')
                {
                    $attributes[] = new Attributes(['label' => ucfirst($attributeName), 'control_type' => $controltypes[$key], 'values' => $controlTypeDefaultValues,'object_type' => $objecttype, 'validate' => $validateControlType, 'object_id' => $projectId]);
                }
                else
                {
                    $data = array('label' => ucfirst($attributeName), 'control_type' => $controltypes[$key], 'values' => $controlTypeDefaultValues, 'validate' => $validateControlType);
                    Attributes::where('id', $attributeIds[$key])->update($data);
                }
            }
        }


        if (!empty($attributes)) {
            $project->attributes()->saveMany($attributes);
        }

        Session::flash('success_message','Project details successfully updated.'); 
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
