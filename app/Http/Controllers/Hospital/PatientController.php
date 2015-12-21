<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\User;
use Chrisbjr\ApiGuard\Models\ApiKey;
use App\Hospital;
use App\Projects;

class PatientController extends Controller
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

        $patients = User::where('type','patient')->orderBy('created_at')->get()->toArray();

        return view('hospital.patients.list')->with('hospital', $hospital)
                                          ->with('logoUrl', $logoUrl)
                                          ->with('active_menu', 'patients')
                                          ->with('patients', $patients);
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
 

        $projects = Projects::where('hospital_id',$hospital['id'])->get()->toArray();  
        // $projectQry = new ParseQuery("Project");
        // $projectData = $projectQry->find();
        // $projects = [];
        // foreach ($projectData as $key => $project) {
        //      $projects[$key] = ['id'=>$project->getObjectId(),'name'=>$project->get('name')];
              
        //  }

        return view('hospital.patients.add')->with('active_menu', 'patients')
                                            ->with('hospital', $hospital)
                                            ->with('logoUrl', $logoUrl)
                                            ->with('projects', $projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$hospitalSlug)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $referanceCode = $request->input('reference_code');
        $hospital = $hospital['id'];//$request->input('hospital');
        $project = $request->input('project');
        
        $user = new User();
        $user->reference_code = $referanceCode;
        $user->password = '';
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

 
        return redirect(url($hospitalSlug . '/patients/' . $userId . '/edit')); 
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
    public function edit($hospitalSlug ,$patientId)
    {
 
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $projects = Projects::where('hospital_id',$hospital['id'])->get()->toArray();
        // $projectQry = new ParseQuery("Project");
        // $projectData = $projectQry->find();
        // $projects = [];
        // foreach ($projectData as $key => $project) {
        //      $projects[$key] = ['id'=>$project->getObjectId(),'name'=>$project->get('name')];
              
        //  }
        $patient = User::find($patientId)->toArray();
        
        return view('hospital.patients.edit')->with('active_menu', 'patients')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('projects', $projects);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $hospitalSlug , $id)
    {
        // $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray(); 
        // $referanceCode = $request->input('reference_code');
        // $hospital = $hospital['id'];//$request->input('hospital');
        // $project = $request->input('project');
        
        // $user = User::find($id);
        // $user->reference_code = $referanceCode;
        // $user->hospital_id = $hospital;
        // $user->project_id = $project;
        // $user->type = 'patient';
        // $user->save();


        return redirect(url($hospitalSlug . '/patients/' . $id . '/edit')); 
    }

    public function getSubmissionReports($hospitalSlug ,$patientId)
    {

        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $patient = User::find($patientId)->toArray();

        $anwserQry = new ParseQuery("Answer");
        $anwserQry->equalTo("patient", $patient['reference_code']);
        $anwserQry->includeKey("response");
        $anwserQry->includeKey("option");
        $anwserQry->includeKey("question");
        $anwsers = $anwserQry->find(); 

        $baseLineArr = [];
        $submissionArr = [];
        $questionArr = [];
        $responseArr = [];

        foreach ($anwsers as   $anwser) {
            $responseStatus = $anwser->get("response")->get("status");
            $questionId = $anwser->get("question")->getObjectId();
            $questionType = $anwser->get("question")->get("type");
            $questionTitle = $anwser->get("question")->get("title");
            $responseId = $anwser->get("response")->getObjectId();
            $optionScore = $anwser->get("option")->get("score");
            $optionValue = $anwser->get("value");

            $questionArr[$questionId]= $questionTitle;
            if($questionType=='input')
            {
                $optionScore = $optionValue;
            }  

            if($responseStatus=="base_line")
            {
               $baseLineArr[$questionId] =$optionScore;
            }
            else
            {
               $submissionArr[$responseId][$questionId] = $optionScore;
            }

            $responseArr[$responseId]= $anwser->get("response")->getCreatedAt()->format('d M');

        }
        

        return view('hospital.patients.submission-report')->with('active_menu', 'patients')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('responseArr', $responseArr)
                                        ->with('questionArr', $questionArr)
                                        ->with('baseLineArr', $baseLineArr)
                                        ->with('submissionArr', $submissionArr);

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

    public function validateRefernceCode(Request $request,$patientId) {
        $reference_code = $request->input('reference_code');
        
        $msg = '';
        $flag = true;


        if ($patientId)
            $patientData = User::where('reference_code', $reference_code)->where('id', '!=', $patientId)->get()->toArray();
        else
            $patientData = User::where('reference_code', $reference_code)->get()->toArray();


        
        $status = 201;
        if (!empty($patientData)) {
            $msg = 'Reference Code Already Taken';
            $flag = false;
            $status = 200;
        }


        return response()->json([
                    'code' => 'reference_validation',
                    'message' => $msg,
                    'data' => $flag,
                        ], $status);
    }
}
