<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\User;
use Chrisbjr\ApiGuard\Models\ApiKey;
 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $patients = User::where('type','patient')->orderBy('created_at')->get()->toArray();
        return view('admin.patients.list')->with('active_menu', 'patients')
                        ->with('patients', $patients);
    }

    public function dashbord()
    {
        return view('admin.dashbord')->with('active_menu', 'dashbord');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hospitalQry = new ParseQuery("Hospital");
        $hospitalData = $hospitalQry->find(); 
        $hospitals = [];
        foreach ($hospitalData as $key => $hospital) {
             $hospitals[$key] = ['id'=>$hospital->getObjectId(),'name'=>$hospital->get('name')];

         }

        $projectQry = new ParseQuery("Project");
        $projectData = $projectQry->find();
        $projects = [];
        foreach ($projectData as $key => $project) {
             $projects[$key] = ['id'=>$project->getObjectId(),'name'=>$project->get('name')];
              
         }


        
        return view('admin.patients.add')->with('active_menu', 'patients')
                                        ->with('hospitals', $hospitals)
                                        ->with('projects', $projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $referanceCode = $request->input('referance_code');
        $hospital = $request->input('hospital');
        $project = $request->input('project');
        
        $user = new User();
        $user->reference_code = $referanceCode;
        $user->password = '';
        $user->email = $referanceCode;
        $user->account_status = 'created';
        $user->hospital_id = $hospital;
        $user->project_id = $project;
        $user->type = 'patient';
        $user->save();
        $userId = $user->id;

        $apiKey                = new ApiKey;
        $apiKey->user_id       = $user->id;
        $apiKey->key           = $apiKey->generateKey();
        $apiKey->save();

        return redirect("/admin/patients"); 
 
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
