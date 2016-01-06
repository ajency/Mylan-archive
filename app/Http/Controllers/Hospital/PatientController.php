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
use \Session;

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

        $validateRefernceCode = User::where('reference_code',$referanceCode)->get()->toArray();
        if(!empty($validateRefernceCode))
        {
           Session::flash('error_message','Error !!! Referance Code Already Exist ');    
           return redirect(url($hospitalSlug . '/patients/create'));
        }
        
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
    public function show($hospitalSlug , $patientId)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $patient = User::find($patientId);
        $projectName = Projects::find($patient['project_id'])->name;
        
        
        return view('hospital.patients.show')->with('active_menu', 'patients')
                                        ->with('active_tab', 'summary')
                                        ->with('tab', '01')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('projectName', $projectName);
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

        $responseArr=[];
        $responses = $this->getResponses($patient['reference_code'],0,[]);
        foreach ($responses as  $response) {
            $responseId = $response->getObjectId();
            $responseArr[$responseId] = $response->get("occurrenceDate")->format('d M');
        }

        $anwsers = $this->getAnswers($patient['reference_code'],0,[]);

        $baseLineArr = [];
        $submissionArr = [];
        $questionArr = [];
        $inputScores = [];
        $completedResponseArr=[];
        
        $inputBaseQuestionId = '';
        $inputLable = '';
        $inputBaseLineScore ='';

        foreach ($anwsers as   $anwser) {
            $responseStatus = $anwser->get("response")->get("status");
            $questionId = $anwser->get("question")->getObjectId();
            $questionType = $anwser->get("question")->get("type");
            $questionTitle = $anwser->get("question")->get("title");
            $responseId = $anwser->get("response")->getObjectId();
            $optionScore = $anwser->get("option")->get("score");
            $optionValue = $anwser->get("value");

           
            if($questionType=='input')
            {
                $optionScore = $optionValue;
                $inputBaseQuestionId = $questionId;
                $inputLable =  ucfirst(strtolower($questionTitle));


                if($responseStatus=="base_line")
                    $inputBaseLineScore = $optionScore;
                else
                    $inputScores[$responseId] = $optionScore;

                continue;
            }
            elseif ($questionType=='multi-choice') {        //if multichoise sum up scores
               if($responseStatus=="base_line")
                {
                    if(isset($baseLineArr[$questionId]))
                        $baseLineArr[$questionId] += $optionScore;
                    else
                        $baseLineArr[$questionId] = $optionScore;
                }
                else
                {
                    if(isset($submissionArr[$responseId][$questionId]))
                        $submissionArr[$responseId][$questionId] += $optionScore;
                    else
                        $submissionArr[$responseId][$questionId] = $optionScore;
                   
                }
            } 
            else  
            {
                if($responseStatus=="base_line")
                   $baseLineArr[$questionId] =$optionScore;
                else
                   $submissionArr[$responseId][$questionId] = $optionScore;

             } 
            
            $questionArr[$questionId]= $questionTitle;
            if($responseStatus!="base_line")
                $completedResponseArr[$responseId]= $anwser->get("response")->get("occurrenceDate")->format('d M'); //get('occurrenceData')

        }


        return view('hospital.patients.reports')->with('active_menu', 'patients')
                                        ->with('active_tab', 'reports')
                                        ->with('tab', '04')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('responseArr', $responseArr)
                                        ->with('completedResponseArr', $completedResponseArr)
                                        ->with('questionArr', $questionArr)
                                        ->with('baseLineArr', $baseLineArr)
                                        ->with('submissionArr', $submissionArr)
                                        ->with('inputBaseLineScore', $inputBaseLineScore)
                                        ->with('inputLable', $inputLable)
                                        ->with('inputScores', $inputScores); 

    }

    public function getResponses($patient,$page=0,$responseData)
    {
        $displayLimit = 20; 

        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $patient);
        $responseQry->containedIn("status",["completed",'missed']);
        $responseQry->notEqualTo("occurrenceDate", null);
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 

        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getResponses($patient,$page,$responseData);
        }  
        
        return $responseData;
     
    }

    public function getAnswers($patient,$page=0,$answersData)
    {
        $displayLimit = 20; 

        $anwserQry = new ParseQuery("Answer");
        $anwserQry->equalTo("patient", $patient);
        $anwserQry->notEqualTo("option", null);
        $anwserQry->includeKey("response");
        $anwserQry->includeKey("option");
        $anwserQry->includeKey("question");
        $anwserQry->descending("createdAt");
        $anwsers = $anwserQry->find(); 
        $answersData = array_merge($anwsers,$answersData); 

        if(!empty($anwsers))
        {
            $page++;
            $answersData = $this->getAnswers($patient,$page,$answersData);
        }  
        
        return $answersData;
     
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
