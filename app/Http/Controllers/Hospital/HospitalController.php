<?php

namespace App\Http\Controllers\Hospital;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\Hospital;
use App\Projects;
use App\User;
use App\UserAccess;
use \Input;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($hospitalSlug)
    {
        return redirect()->intended($hospitalSlug.'/projects');
        
        $inputs = Input::get(); 
        // $projectId = (isset($inputs['projectId']))?$inputs['projectId']:0;

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('-1 months'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));

        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray(); 
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];


        if(\Auth::user()->type=='hospital_user' && \Auth::user()->project_access=='no')
        {
            $userId = \Auth::user()->id;
            $projectIds = UserAccess::where(['user_id'=>$userId,'object_type'=>'project'])->lists('object_id')->toArray(); 
            $allProjects = Projects::where('hospital_id',$hospital['id'])->whereIn('id',$projectIds)->get()->toArray();  
        }
        else
            $allProjects = Projects::where('hospital_id',$hospital['id'])->get()->toArray(); 


        // if($projectId)
        //     $project = Projects::where('hospital_id',$hospital['id'])->where('id',$projectId)->first()->toArray();
        // else
        $project = Projects::where('hospital_id',$hospital['id'])->first(); 
        if($project==null)
        {
            $project=[];
            $projectId = 0;
        }
        else
        {
            $project = $project->toArray();
            $projectId = intval($project['id']);
        }
        

           
        
        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        $patients = User::where(['project_id'=>$projectId])->lists('reference_code')->take(3)->toArray();
        

        $projectResponseCount = $this->getProjectResponseCount($projectId,$startDateObj,$endDateObj);
        $projectOpenFlags =  $this->projectOpenFlags($projectId,$startDateObj,$endDateObj);
        $submissionFlags =  $this->patientSubmissionSummary($projectId,$startDateObj,$endDateObj);
        $patientFlagSummary = $this->patientFlagSummary($projectId,$startDateObj,$endDateObj);
        $patientsSummary = $this->patientSummary($patients ,$projectId,$startDateObj,$endDateObj);
        $patientsSummary = $patientsSummary['patientResponses'];
        
         
        return view('hospital.dashbord')->with('active_menu', 'dashbord')
                                        ->with('projectResponseCount', $projectResponseCount)
                                        ->with('projectOpenFlags', $projectOpenFlags)
                                        ->with('submissionFlags', $submissionFlags)
                                        ->with('patientFlagSummary', $patientFlagSummary)
                                        ->with('patientsSummary', $patientsSummary)
                                        ->with('project', $project)
                                        ->with('allProjects', $allProjects)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl);
    }

    public function getProjectResponseCount($projectId,$startDate,$endDate)
    {
        //project patient count
        $patientsCount = User::where(['project_id'=>$projectId])->get()->count();

        //Total submission count
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","completed");
        $responseQry->equalTo("project",$projectId);

        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThan("occurrenceDate",$endDate);
        $submissionCount = $responseQry->count(); 

        //get all answers for project
        $anwsers = $this->getResponseAnswers($projectId,0,[] ,$startDate,$endDate);
                 
        $totalFlags = [];
        $totalFlagCount =0;
        $baseLineOpenFlags = [];
        $baseLineOpenFlagCount = 0;
        $previousOpenFlags = [];
        $previousOpenFlagCount = 0;
        
        foreach ($anwsers as $anwser)
        {
            $baseLineFlagStatus = $anwser->get("baseLineFlagStatus");
            $previousFlagStatus = $anwser->get("previousFlagStatus");
            $responseId = $anwser->get("response")->getObjectId();
            $responseStatus = $anwser->get("response")->get("status");
            $questionId = $anwser->get("question")->getObjectId();
            $questionType = $anwser->get("question")->get("type");
 

            if($responseStatus!='completed')
                continue;

            if(!isset($totalFlags[$responseId][$questionId]))
            {
                $totalFlagCount ++;
                $totalFlags[$responseId][$questionId] = $baseLineFlagStatus;
            }
 
            if($baseLineFlagStatus =='open') 
            {

                if(!isset($baseLineOpenFlags[$responseId][$questionId]))
                {
                    $baseLineOpenFlagCount ++;
                    $baseLineOpenFlags[$responseId][$questionId] = $baseLineFlagStatus;
                }
            }

            if($previousFlagStatus =='open') 
            {

                if(!isset($previousOpenFlags[$responseId][$questionId]))
                {
                    $previousOpenFlagCount ++;
                    $previousOpenFlags[$responseId][$questionId] = $previousFlagStatus;
                }
            }
            
        }


        $projectResponseCount['patientsCount'] = $patientsCount;
        $projectResponseCount['submissionCount'] = $submissionCount;
        $projectResponseCount['baseLineOpenFlagsCount'] = $baseLineOpenFlagCount;
        $projectResponseCount['previousOpenFlagsCount'] = $previousOpenFlagCount;
        $projectResponseCount['totalFlagsCount'] = $totalFlagCount;

        return $projectResponseCount;
    }

    public function getResponseAnswers($projectId,$page=0,$anwsersData,$startDate,$endDate)
    {
        $displayLimit = 20; 

        $answersQry = new ParseQuery("Answer");
        $answersQry->equalTo("project",$projectId);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->limit($displayLimit);
        $answersQry->skip($page * $displayLimit);
        $answersQry->ascending("createdAt");
        $answersQry->greaterThanOrEqualTo("createdAt",$startDate);
        $answersQry->lessThan("createdAt",$endDate);
        $anwsers = $answersQry->find();
        $anwsersData = array_merge($anwsers,$anwsersData); 

        if(!empty($anwsers))
        {
            $page++;
            $anwsersData = $this->getResponseAnswers($projectId,$page,$anwsersData ,$startDate,$endDate);
        }  
        
        return $anwsersData;
     
    }

    public function projectOpenFlags($projectId,$startDate,$endDate)
    {

        $anwsers = $this->getResponseAnswers($projectId,0,[],$startDate,$endDate);

        $openFlagsByDate = [];
        $basLineOpenFlags =[];
        $previousOpenFlags =[];
        
        foreach ($anwsers as $anwser)
        {

            $baseLineFlagStatus = $anwser->get("baseLineFlagStatus");
            $previousFlagStatus = $anwser->get("previousFlagStatus");
            $responseId = $anwser->get("response")->getObjectId();
            $responseStatus = $anwser->get("response")->get("status");
            $questionId = $anwser->get("question")->getObjectId();
            $questionType = $anwser->get("question")->get("type");
            $answerDate = $anwser->get("response")->get("occurrenceDate")->format('d-m-Y');
            $answerDate = strtotime($answerDate);
 

            if($responseStatus!='completed')
                continue;

            if($baseLineFlagStatus =='open') 
            {
                if(isset($basLineOpenFlags[$responseId][$questionId]))
                    continue;

                if(isset($openFlagsByDate[$answerDate]['baseLine']))
                    $openFlagsByDate[$answerDate]['baseLine'] += 1; 
                else
                    $openFlagsByDate[$answerDate]['baseLine'] = 1;

                $basLineOpenFlags[$responseId][$questionId] = $baseLineFlagStatus;
            }

            if($previousFlagStatus =='open') 
            {
                if(isset($previousOpenFlags[$responseId][$questionId]))
                    continue;

                if(isset($openFlagsByDate[$answerDate]['previous']))
                    $openFlagsByDate[$answerDate]['previous'] += 1; 
                else
                    $openFlagsByDate[$answerDate]['previous'] = 1;

                $previousOpenFlags[$responseId][$questionId] = $previousFlagStatus;
            }
            
        }

        ksort($openFlagsByDate);
        return $openFlagsByDate;
    }

    public function projectTotalFlags($projectId,$startDate,$endDate)
    {

        $anwsers = $this->getResponseAnswers($projectId,0,[],$startDate,$endDate);

        $totalFlagsByDate = [];
        $totalFlags = [];
        
        foreach ($anwsers as $anwser)
        {

            $flagStatus = $anwser->get("flagStatus");
            $responseId = $anwser->get("response")->getObjectId();
            $responseStatus = $anwser->get("response")->get("status");
            $questionId = $anwser->get("question")->getObjectId();
            $questionType = $anwser->get("question")->get("type");
            $answerDate = $anwser->get("response")->get("occurrenceDate")->format('d-m-Y');
 

            if($responseStatus!='completed')
                continue;

            if(isset($totalFlags[$responseId][$questionId]))
                continue;

            if(isset($totalFlagsByDate[$answerDate]))
                $totalFlagsByDate[$answerDate] += 1; 
            else
                $totalFlagsByDate[$answerDate] = 1;

            $totalFlags[$responseId][$questionId] = $flagStatus;
             
            
        }

        return $totalFlagsByDate;
    }

    public function projectSubmissions($projectId,$startDate,$endDate)
    {

        $submissionByDate =[];

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",["completed","missed"]);
        $responseQry->equalTo("project",$projectId);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $responseQry->ascending("occurrenceDate");
        $submissions = $responseQry->find(); 
        
        foreach ($submissions as $submission)
        {

            $occurrenceDate = $submission->get("occurrenceDate")->format('d-m-Y');
 

            if(isset($submissionByDate[$occurrenceDate]))
                $submissionByDate[$occurrenceDate] += 1; 
            else
                $submissionByDate[$occurrenceDate] = 1;             
            
        }

        return $submissionByDate;
    }

    public function patientSubmissionSummary($projectId,$startDate,$endDate)
    {
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","completed");
        $responseQry->equalTo("project",$projectId);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $responseQry->limit(2);
        $responseQry->descending("occurrenceDate");
        $responses = $responseQry->find();

        $submissionFlags = $this->responseAnswerFlags($responses);
 

        return $submissionFlags;
       
    }

    public function responseAnswerFlags($responses)
    {
        $answersQry = new ParseQuery("Answer");
        $answersQry->containedIn("response", $responses);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $anwsers = $answersQry->find();
         
        $submissionFlags = [];
        $patientIds = [];
        
        
        foreach ($anwsers as  $answer) {

            $comparedToBaslineScore = $answer->get("response")->get("comparedToBaseLine");
            $comparedToPrevious = $answer->get("response")->get("comparedToPrevious");
            $responseId = $answer->get("response")->getObjectId();
            $baseLineFlagStatus = $answer->get("response")->get("baseLineFlagStatus");
            $previousFlagStatus = $answer->get("response")->get("previousFlagStatus");
            $patient = $answer->get("response")->get("patient");
            $sequenceNumber = $answer->get("response")->get("sequenceNumber");
            $occurrenceDate = $answer->get("response")->get("occurrenceDate")->format('dS M');
            $questionType = $answer->get("question")->get("type");


            $baseLineFlag = $answer->get("baseLineFlag");
            $previosFlag = $answer->get("previousFlag");
  
            // $patientIds[$responseId] = $patient;
            if(!isset($submissionFlags[$responseId]))
            {
                $submissionFlags[$responseId]['baseLineFlag']['red']=[];
                $submissionFlags[$responseId]['baseLineFlag']['green']=[];
                $submissionFlags[$responseId]['baseLineFlag']['amber']=[];

                $submissionFlags[$responseId]['previosFlag']['red']=[];
                $submissionFlags[$responseId]['previosFlag']['green']=[];
                $submissionFlags[$responseId]['previosFlag']['amber']=[];
            }

            $submissionFlags[$responseId]['patient'] = $patient;
            $submissionFlags[$responseId]['baseLineFlagStatus'] = $baseLineFlagStatus;
            $submissionFlags[$responseId]['previousFlagStatus'] = $previousFlagStatus;
            $submissionFlags[$responseId]['baselineScore']= $comparedToBaslineScore;
            $submissionFlags[$responseId]['previousScore']= $comparedToPrevious;
            $submissionFlags[$responseId]['sequenceNumber']= $sequenceNumber;
            $submissionFlags[$responseId]['occurrenceDate']= $occurrenceDate;
            

            if($baseLineFlag !=null )
            {   
                $submissionFlags[$responseId]['baseLineFlag'][$baseLineFlag][]= $baseLineFlag;
                $submissionFlags[$responseId]['previosFlag'][$baseLineFlag][]= $previosFlag;
                
            }
 
        }
 

        return $submissionFlags;
    }

    // public function patientFlagSummary($projectId,$startDate,$endDate)
    // {
    //     $responseQry = new ParseQuery("Response");
    //     $responseQry->equalTo("status","completed");
    //     $responseQry->equalTo("project",$projectId);
    //     $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
    //     $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
    //     $responseQry->limit(2);
    //     $responseQry->descending("createdAt");
    //     $responses = $responseQry->find();

    //     $flagSummary =[];       
    //     foreach ($responses as  $response) {

    //         $responseId = $response->getObjectId();
    //         $responseStatus = $response->get("flagStatus");
    //         $patient = $response->get("patient");
    //         $reason = $response->get("reason");
    //         $sequenceNumber = $response->get("sequenceNumber");
    //         $occurrenceDate = $response->get("occurrenceDate")->format('dS M');

    //         $flagSummary[$responseId]['patient'] = $patient;
    //         $flagSummary[$responseId]['status'] = $responseStatus;
    //         $flagSummary[$responseId]['reason'] = $reason;
    //         $flagSummary[$responseId]['sequenceNumber']= $sequenceNumber;
    //         $flagSummary[$responseId]['occurrenceDate']= $occurrenceDate;
 
    //     }
 

    //     return $flagSummary;
       
    // }

    public function patientFlagSummary($projectId,$startDate,$endDate)
    {
        $answersQry = new ParseQuery("Answer");
        $answersQry->equalTo("project",$projectId);
        $answersQry->equalTo("value",null);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->greaterThanOrEqualTo("createdAt",$startDate);
        $answersQry->lessThan("createdAt",$endDate);
        $answersQry->descending("createdAt");
        $answersQry->limit(2);
        $anwsers = $answersQry->find();

        $flagSummary =[];       
        foreach ($anwsers as  $anwser) {

            $answerId = $anwser->getObjectId();
            $baseLineStatus = $anwser->get("baseLineFlagStatus");
            $previousStatus = $anwser->get("previousFlagStatus");
            $patient = $anwser->get("patient");
            $sequenceNumber = $anwser->get("response")->get("sequenceNumber");
            $occurrenceDate = $anwser->get("response")->get("occurrenceDate")->format('dS M');
            $baseLineFlag = $anwser->get("baseLineFlag");
            $previousFlag = $anwser->get("previousFlag");

            $flagSummary[$answerId]['patient'] = $patient;
            $flagSummary[$answerId]['baseLineFlag'] = $baseLineFlag;
            $flagSummary[$answerId]['previousFlag'] = $previousFlag;
            $flagSummary[$answerId]['sequenceNumber']= $sequenceNumber;
            $flagSummary[$answerId]['occurrenceDate']= $occurrenceDate;
            $flagSummary[$answerId]['baseLineStatus']= $baseLineStatus;
            $flagSummary[$answerId]['previousStatus']= $previousStatus;
 
        }
         

        return $flagSummary;
       
    }

    public function patientSummary($patients ,$projectId,$startDate,$endDate)
    {
        $scheduleQry = new ParseQuery("Schedule");
        $scheduleQry->containedIn("patient",$patients);
        $schedules = $scheduleQry->find();

        $patientNextOccurrence = [];
        $patientResponses = [];
        $patientData = [];
        foreach($schedules as $schedule)
        {
            $patientId = $schedule->get("patient");
            $nextOccurrence = $schedule->get("nextOccurrence")->format('dS M');
            // $patientNextOccurrence[$patientId]=$nextOccurrence;
            $patientResponses[$patientId]['nextSubmission'] = $nextOccurrence;
            $patientResponses[$patientId]['totalFlags']=[];
            $patientResponses[$patientId]['missed'] =[];
            $patientResponses[$patientId]['count'] =[];
            $patientResponses[$patientId]['lastSubmission'] = '-' ;

        }

        $responses = $this->getPatientsResponses($patients,$projectId,0,[] ,$startDate,$endDate);  
        $completedResponses = [];
        $missedResponses = [];
        
        foreach ($responses as $key => $response) {
            $status = $response->get("status");
            $patient = $response->get("patient");
            $responseId = $response->getObjectId();
            $occurrenceDate = $response->get("occurrenceDate")->format('dS M');
 
            if(!isset($patientData[$patient]))
            {
                $patientResponses[$patient]['lastSubmission'] = $occurrenceDate;
                $patientData[$patient] = $occurrenceDate;
            }

            $patientResponses[$patient]['count'][]=$responseId;
            if($status=='missed')
            {
                $patientResponses[$patient]['missed'][]=$responseId;
                $missedResponses[]=$responseId;
                continue;
            }

            $completedResponses[]=$response;
        }
         

        $answersQry = new ParseQuery("Answer");
        $answersQry->containedIn("response", $completedResponses);
        $answersQry->includeKey("response");
        $anwsers = $answersQry->find();
        
         
        // $submissionFlags = [];  
         
        foreach ($anwsers as  $anwser) {

            $baseLineFlag = $anwser->get("baseLineFlag");
            $previousFlag = $anwser->get("previousFlag");
            $patient = $anwser->get("patient");
            $responseId = $anwser->get("response")->getObjectId();
            $occurrenceDate = $anwser->get("response")->get("occurrenceDate")->format('dS M');

            if(!isset($patientResponses[$patient]))
            {
                $patientResponses[$patient]['baseLineFlag']['red']=[];
                $patientResponses[$patient]['previousFlag']['red']=[];
                $patientResponses[$patient]['baseLineFlag']['green']=[];
                $patientResponses[$patient]['previousFlag']['green']=[];
                $patientResponses[$patient]['baseLineFlag']['amber']=[];
                $patientResponses[$patient]['previousFlag']['amber']=[];

            }

            if($baseLineFlag !=null )
            {   
                $patientResponses[$patient]['baseLineFlag'][$baseLineFlag][]= $baseLineFlag;
                $patientResponses[$patient]['previousFlag'][$previousFlag][]= $previousFlag;
                $patientResponses[$patient]['totalFlags'][]= $previousFlag;
                
            }

        }

        $responseRate = (count($responses)) ? (count($completedResponses)/count($responses)) * 100 :0;
        $responseRate =  round($responseRate,2);
         

        // foreach ($responses as   $response) {

        //     $responseId = $response->getObjectId();
        //     $patient = $response->get("patient");
        //     $occurrenceDate = $response->get("occurrenceDate")->format('dS M');
        //     $status = $response->get("status");
           
        //     if(!isset($patientResponses[$patient]))
        //     {
        //         $patientResponses[$patient]['lastSubmission'] = $occurrenceDate;
        //         $patientResponses[$patient]['nextSubmission'] = $patientNextOccurrence[$patient];
        //         $patientResponses[$patient]['missed'] = [];
        //         $patientResponses[$patient]['totalFlags'] =[];
        //     }

        //     if($status=='missed')
        //     {
        //         $patientResponses[$patient]['missed'][]=$responseId;
        //     }
        //     else
        //     {
        //         $patientResponses[$patient]['baseLineFlag'] = $submissionFlags[$patient]['baseLineFlag'];
        //         $patientResponses[$patient]['previousFlag'] = $submissionFlags[$patient]['previousFlag'];
        //         $patientResponses[$patient]['totalFlags'] = $submissionFlags[$patient]['totalFlags'];
        //     }

        //     $patientResponses[$patient]['count'][]=$responseId;
            
        // }
        $data['patientResponses']=$patientResponses;
        $data['responseRate']=$responseRate; 
        $data['completedResponses']=count($completedResponses);
        $data['missedResponses']=count($missedResponses);

        return $data;
        
    }

    public function getPatientsResponses($patients,$projectId,$page=0,$responseData,$startDate,$endDate)
    {
        $displayLimit = 20; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",["completed","missed"]);
        $responseQry->containedIn("patient",$patients);

        if($projectId)
             $responseQry->equalTo("project",$projectId);

        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $responseQry->ascending("occurrenceDate");
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 

        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getPatientsResponses($patients,$projectId,$page,$responseData ,$startDate,$endDate);
        }  
        
        return $responseData;
     
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
