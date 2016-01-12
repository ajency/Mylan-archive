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
use App\Http\Controllers\Hospital\HospitalController;

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
        $newPatients = [];
        $patientReferenceCode = [];
        foreach ($patients as  $patient) {
            
            if($patient['account_status']=='created')
                $newPatients[]= $patient['reference_code'];
            
            $patientReferenceCode[] = $patient['reference_code'];
        }

        $startDate =  date('d-m-Y', strtotime('-1 months'));
        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDate = date('d-m-Y', strtotime('+1 day'));
        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        $hospitalController = new HospitalController();
        $patientResponses = $hospitalController->patientSummary($patientReferenceCode ,0,$startDateObj,$endDateObj);
        $patientsSummary = $patientResponses['patientResponses'];
        $responseRate = $patientResponses['responseRate'];
        $completedResponses = $patientResponses['completedResponses'];
        $missedResponses = $patientResponses['missedResponses'];
 
      

        return view('hospital.patients.list')->with('hospital', $hospital)
                                          ->with('logoUrl', $logoUrl)
                                          ->with('active_menu', 'patients')
                                          ->with('newPatients', count($newPatients))
                                          ->with('patients', $patients)
                                          ->with('responseRate', $responseRate)
                                          ->with('completedResponses', $completedResponses)
                                          ->with('missedResponses', $missedResponses)
                                          ->with('patientsSummary', $patientsSummary);
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
        $referenceCode = $request->input('reference_code');
        $hospital = $hospital['id'];//$request->input('hospital');
        $project = $request->input('project');

        $validateRefernceCode = User::where('reference_code',$referenceCode)->get()->toArray();
        if(!empty($validateRefernceCode))
        {
           Session::flash('error_message','Error !!! Referance Code Already Exist ');    
           return redirect(url($hospitalSlug . '/patients/create'));
        }
        
        $user = new User();
        $user->reference_code = $referenceCode;
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

 
        //return redirect(url($hospitalSlug . '/patients/' . $userId)); 
        return redirect(url($hospitalSlug . '/patients/' . $userId . '/base-line-score-edit')); 
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
        // $referenceCode = $request->input('reference_code');
        // $hospital = $hospital['id'];//$request->input('hospital');
        // $project = $request->input('project');
        
        // $user = User::find($id);
        // $user->reference_code = $referenceCode;
        // $user->hospital_id = $hospital;
        // $user->project_id = $project;
        // $user->type = 'patient';
        // $user->save();


        return redirect(url($hospitalSlug . '/patients/' . $id . '/edit')); 
    }

    public function showpatientBaseLineScore($hospitalSlug ,$patientId)
    {
 
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];
        $projectId = $patient['project_id'];
        $projectId = intval ($projectId);

        $baseLineData = $this->getBaseLineData($projectId,$referenceCode);
        $questionnaireName = $baseLineData['questionnaireName']; 
        $questionsList = $baseLineData['questionsList']; 
        $optionsList = $baseLineData['optionsList']; 
        $answersList = $baseLineData['answersList']; 
         
        
        return view('hospital.patients.baselinescore')->with('active_menu', 'patients')
                                        ->with('active_tab', 'base_line')
                                        ->with('tab', '03')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('questionnaire', $questionnaireName)
                                        ->with('questionsList', $questionsList)
                                        ->with('optionsList', $optionsList)
                                        ->with('answersList', $answersList);
    }

    public function getpatientBaseLineScore($hospitalSlug ,$patientId)
    {
 
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];
        $projectId = $patient['project_id'];
        $projectId = intval ($projectId);

        $baseLineData = $this->getBaseLineData($projectId,$referenceCode);
        $questionnaireName = $baseLineData['questionnaireName']; 
        $questionnaireId = $baseLineData['questionnaireId']; 
        $questionsList = $baseLineData['questionsList']; 
        $optionsList = $baseLineData['optionsList']; 
        $answersList = $baseLineData['answersList']; 
        $baseLineResponseId = $baseLineData['baseLineResponseId'];  
        
        return view('hospital.patients.baselinescore-edit')->with('active_menu', 'patients')
                                        ->with('active_tab', 'base_line')
                                        ->with('tab', '03')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('baseLineResponseId', $baseLineResponseId)
                                        ->with('questionnaireId', $questionnaireId)
                                        ->with('questionnaire', $questionnaireName)
                                        ->with('questionsList', $questionsList)
                                        ->with('optionsList', $optionsList)
                                        ->with('answersList', $answersList);
    }

    public function setPatientBaseLineScore(Request $request, $hospitalSlug , $id)
    {
        $baseLineAnswers = $request->all(); //dd($baseLineAnswers);
        $questions = $baseLineAnswers['question'];
        $questionType = $baseLineAnswers['questionType'];
        $baseLineResponseId = $baseLineAnswers['baseLineResponseId'];
        $questionnaireId = $baseLineAnswers['questionnaireId'];
        $patientId = $baseLineAnswers['patientId'];

        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];
        $projectId = $patient['project_id'];
        $projectId = intval ($projectId);

        if($baseLineResponseId =='')
        {
            $questionnaireObj = new ParseQuery("Questionnaire");
            $questionnaire = $questionnaireObj->get($questionnaireId);

            $date = new \DateTime();
            //add
            $response = new ParseObject("Response");
            $response->set("questionnaire", $questionnaire);
            $response->set("patient", $referenceCode);
            $response->set("project", $projectId);
            $response->set("occurrenceDate", $date);
            $response->set("status", 'base_line');
            $response->save();
        }
        else
        {
            $responseObj = new ParseQuery("Response");
            $response = $responseObj->get($baseLineResponseId);

            $answers = new ParseQuery("Answer");
            $answers->equalTo("response", $response);
            $answersObjs = $answers->find();
            // ParseObject::destroyAll($answersObjs);
            foreach ($answersObjs as $answer) {
                $answer->destroy();
            }
        }

        $bulkAnswerInstances = [];
        foreach ($questions as $questionId=> $answers) {

            $questionObj = array('__type' => 'Pointer', 'className' => 'Questions', 'objectId' => $questionId);
            
            if($questionType[$questionId]=='single-choice')
            {
                $answerData = explode('-',$answers);
                $optionId = $answerData[0];
                $score = intval ($answerData[1]);
            
                $optionObj = array('__type' => 'Pointer', 'className' => 'Options', 'objectId' => $optionId);
                
                $answer = new ParseObject("Answer");
                $answer->setAssociativeArray("question", $questionObj);
                $answer->set("response", $response);
                $answer->set("patient", $referenceCode);
                $answer->setAssociativeArray("option", $optionObj);
                $answer->set("score", $score);
                $answer->set("project", $projectId);
                // $answer->save();
                $bulkAnswerInstances[] = $answer;
            }
            elseif($questionType[$questionId]=='descriptive')
            {
                $answer = new ParseObject("Answer");
                $answer->setAssociativeArray("question", $questionObj);
                $answer->set("response", $response);
                $answer->set("patient", $referenceCode);
                $answer->set("value", $answers);
                $answer->set("project", $projectId);
                // $answer->save();
                $bulkAnswerInstances[] = $answer;
            }
            elseif($questionType[$questionId]=='multi-choice')
            {
                foreach ($answers as  $options) {
                    $answerData = explode('-',$options);
                    
                    if(count($answerData)<=1)
                        continue;

                    
                    $optionId = $answerData[0];
                    $score = intval ($answerData[1]);

                    $optionObj = array('__type' => 'Pointer', 'className' => 'Options', 'objectId' => $optionId);

                    $answer = new ParseObject("Answer");
                    $answer->setAssociativeArray("question", $questionObj);
                    $answer->set("response", $response);
                    $answer->set("patient", $referenceCode);
                    $answer->setAssociativeArray("option", $optionObj);
                    $answer->set("score", $score);
                    $answer->set("project", $projectId);
                    // $answer->save();
                    $bulkAnswerInstances[] = $answer;
                }
            }
            elseif($questionType[$questionId]=='input')
            {
                foreach ($answers as $optionId => $value) {

                    if($value!='')
                    {
                        $optionObj = array('__type' => 'Pointer', 'className' => 'Options', 'objectId' => $optionId);

                        $answer = new ParseObject("Answer");
                        $answer->setAssociativeArray("question", $questionObj);
                        $answer->set("response", $response);
                        $answer->set("patient", $referenceCode);
                        $answer->setAssociativeArray("option", $optionObj);
                        $answer->set("project", $projectId);
                        $answer->set("value", $value);
                        // $answer->save();
                        $bulkAnswerInstances[] = $answer;
                    }
                    
                }
            }
        }

        ParseObject::saveAll($bulkAnswerInstances);
        

        return redirect(url($hospitalSlug . '/patients/' . $id . '/base-line-score-edit')); 
         
    }

    public function getBaseLineData($projectId,$referenceCode)
    {
        $questionnaireQry = new ParseQuery("Questionnaire");
        $questionnaireQry->equalTo("project", $projectId);
        $questionnaire = $questionnaireQry->first();  
        $questionnaireName = $questionnaire->get('name');
        $questionnaireId = $questionnaire->getObjectId();

        $questionQry = new ParseQuery("Questions");
        $questionQry->equalTo("questionnaire", $questionnaire);
        $questions = $questionQry->find(); 

        $optionsQry = new ParseQuery("Options");
        $optionsQry->containedIn("question", $questions);
        $options = $optionsQry->find(); 

        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $referenceCode); 
        $responseQry->equalTo("status", 'base_line'); 
        $response = $responseQry->first();
         
        $questionsList=[];
        $optionsList=[];
        $answersList=[];
        $baseLineResponseId = '';

        foreach ($questions as   $question) {
            $questionId = $question->getObjectId();
            $questionType = $question->get('type');
            $name = $question->get('question');
            $questionsList[$questionId] = ['question'=>$name,'type'=>$questionType];
        }

        foreach ($options as   $option) {
            $questionId = $option->get('question')->getObjectId();
            $optionId = $option->getObjectId();
            $label = $option->get('label');
            $score = $option->get('score');
            $optionsList[$questionId][] = ['id'=>$optionId,'score'=>$score,'label'=>$label];
        }

        if(!empty($response))
        {
            $baseLineResponseId = $response->getObjectId();
            $answersQry = new ParseQuery("Answer");
            $answersQry->includeKey("option");
            $answersQry->includeKey("question");
            $answersQry->equalTo("response", $response); 
            $answers = $answersQry->find(); 

            foreach ($answers as   $answer) {
                $answersId = $answer->getObjectId();
                $questionId = $answer->get('question')->getObjectId();
                $questionType = $answer->get('question')->get('type');
                $optionId = '';
                $label = '';
                if($questionType!='descriptive')
                {
                    $optionId = $answer->get('option')->getObjectId();
                    $label = $answer->get('option')->get('label');
                }

                
                $value = $answer->get('value');
                $score = $answer->get('score');

                if($questionType=='multi-choice')
                {
                    $answersList[$questionId][$optionId] = ['optionId'=>$optionId,'label'=>$label,'value'=>$value,'score'=>$score];
                }
                else
                {
                    $answersList[$questionId] = ['optionId'=>$optionId,'label'=>$label,'value'=>$value,'score'=>$score];
                }
                
            }
        } 

        $data['questionnaireId'] = $questionnaireId; 
        $data['questionnaireName'] = $questionnaireName; 
        $data['questionsList'] = $questionsList; 
        $data['optionsList'] = $optionsList; 
        $data['answersList'] = $answersList; 
        $data['baseLineResponseId'] = $baseLineResponseId; 

        return $data;
    }

    public function getSubmissionReports($hospitalSlug ,$patientId)
    {

        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $patient = User::find($patientId)->toArray();

        $responseArr=[];
        $responseStatus = ["completed",'missed'];
        $responses = $this->getResponses($patient['reference_code'],$responseStatus,0,[]);
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

   

    public function getResponses($patient,$responseStatus,$page=0,$responseData)
    {
        $displayLimit = 20; 

        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $patient);
        $responseQry->containedIn("status",$responseStatus);
        $responseQry->notEqualTo("occurrenceDate", null);
        $responseQry->ascending("createdAt");
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 

        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getResponses($patient,$responseStatus,$page,$responseData);
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
        $anwserQry->limit($displayLimit);
        $anwserQry->skip($page * $displayLimit);
        $anwsers = $anwserQry->find(); 
        $answersData = array_merge($anwsers,$answersData); 

        if(!empty($anwsers))
        {
            $page++;
            $answersData = $this->getAnswers($patient,$page,$answersData);
        }  
        
        return $answersData;
     
    }

    function getPatientSubmission($hospitalSlug ,$patientId)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $patient = User::find($patientId)->toArray();

        $responseArr=[];
        $responseStatus = ["completed"];
        $responses = $this->getResponses($patient['reference_code'],$responseStatus,0,[]);

        $hospitalController = new HospitalController();
        $submissionFlags = $hospitalController->responseAnswerFlags($responses); 

        return view('hospital.patients.submissions')->with('active_menu', 'patients')
                                                ->with('active_tab', 'submissions')
                                                ->with('tab', '02')
                                                ->with('patient', $patient)
                                                 ->with('hospital', $hospital)
                                                 ->with('logoUrl', $logoUrl)
                                                 ->with('submissionFlags', $submissionFlags);
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
