<?php

namespace App\Http\Controllers\Project;

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
use App\Http\Controllers\Project\PatientController;

class ProjectController extends Controller
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
    public function show($hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $inputs = Input::get(); 

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('-7 day'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        $responseStatus = ["completed","late","missed"];
        $projectResponses = $this->getProjectResponsesByDate($projectId,0,[],$startDateObj,$endDateObj,$responseStatus); 
        $projectAnwers = $this->getProjectAnwersByDate($projectId,0,[],$startDateObj,$endDateObj);

        $responseCount = $this->getProjectResponseCounts($projectResponses,$projectAnwers);
        $projectFlagsCount = $this->projectFlagsCount($projectAnwers); 
        $projectSubmissionCount = $this->projectSubmissionCount($projectResponses);
        $patientsFlagSummary = $this->patientsFlagSummary($projectAnwers);  
        $submissionsSummary = $this->getSubmissionsSummary($projectAnwers); 

        
        $patients = User::where('type','patient')->where('project_id',$project['id'])->orderBy('created_at')->get()->toArray();
        $newPatients = [];
        $patientReferenceCode = [];
        foreach ($patients as  $patient) {
            
            if($patient['account_status']=='created')
                $newPatients[]= $patient['reference_code'];
            
            $patientReferenceCode[] = $patient['reference_code'];
        }
        
        $patients['patientsCount'] = count($patients);
        $patients['newPatients'] = count($newPatients);

        $allPatients = User::where('type','patient')->where(['project_id'=>$projectId])->get()->take(5)->toArray();
        $patientController = new PatientController();
        $patientSummaryData  = $patientController->patientsSummary($patientReferenceCode ,$startDateObj,$endDateObj);
        $patientsSummary = $patientSummaryData['patientResponses'];
        

        return view('project.dashbord')->with('active_menu', 'dashbord')
                                        ->with('responseCount', $responseCount) 
                                        ->with('projectFlagsCount', $projectFlagsCount)
                                        ->with('projectSubmissionCount', $projectSubmissionCount)
                                        ->with('patientsFlagSummary', $patientsFlagSummary)
                                        ->with('submissionsSummary', $submissionsSummary)
                                        ->with('patientsSummary', $patientsSummary)
                                        ->with('allPatients', $allPatients)
                                        ->with('project', $project)
                                        ->with('patients', $patients)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl);

    }

    public function getProjectAnwersByDate($projectId,$page=0,$anwsersData,$startDate,$endDate)
    {
        $displayLimit = 20; 

        $answersQry = new ParseQuery("Answer");
        $answersQry->equalTo("project",$projectId);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->limit($displayLimit);
        $answersQry->skip($page * $displayLimit);
        $answersQry->ascending("occurrenceDate");
        $answersQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $answersQry->lessThan("occurrenceDate",$endDate);
        $anwsers = $answersQry->find();
        $anwsersData = array_merge($anwsers,$anwsersData); 

        if(!empty($anwsers))
        {
            $page++;
            $anwsersData = $this->getProjectAnwersByDate($projectId,$page,$anwsersData ,$startDate,$endDate);
        }  
        
        return $anwsersData;
     
    }

    public function getProjectResponsesByDate($projectId,$page=0,$responseData,$startDate,$endDate,$status)
    {
        $displayLimit = 20; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",$status);  //["completed","late","missed"]
        $responseQry->equalTo("project",$projectId);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responseQry->ascending("occurrenceDate");
        $responses = $responseQry->find();
        $responseData = array_merge($responses,$responseData); 
         
        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getProjectResponsesByDate($projectId,$page,$responseData ,$startDate,$endDate,$status);
        }  
        
        return $responseData;
     
    }

    public function getProjectResponseCounts($projectResponses,$projectAnwers)
    {
        $redFlags['baseLineFlag'] =[];
        $redFlags['previousFlag'] =[];
        $amberFlags['baseLineFlag'] = [];
        $amberFlags['previousFlag'] = [];
        $missed = [];
        $completed = [];
        $openSubmissions=[];

        $missedByDate = [];
        $openSubmissionsByDate=[];
        $completedByDate = [];

        $missedSubmissionData = [];
        $openSubmissionData=[];
        $completedSubmissionData = [];
 

        foreach ($projectResponses as $response) {
            $responseId = $response->getObjectId();
            $status = $response->get("status");
            $reviewed = $response->get("reviewed");
            $responseDate = $response->get("occurrenceDate")->format('d-m-Y');
            $responseDate = strtotime($responseDate);

            if($status=='missed')
            {
                $missed[]=$responseId;
                $missedByDate[$responseDate][] =$responseId;
            }

            if($status=='completed')
            {
                $completed[]=$responseId;
                $completedByDate[$responseDate][] =$responseId;
            }

            if($reviewed=='open')
            {
                $openSubmissions[]=$responseId;
                $openSubmissionsByDate[$responseDate][] =$responseId;
            }


        }
         
        ksort($missedByDate);
        $i=0;
        foreach($missedByDate as $date => $value)
        { 
            $missedSubmissionData[$i]["date"] = date('Y-m-d',$date);
            $missedSubmissionData[$i]["missed"] = count($value);
            $i++;
        }

        ksort($completedByDate);
        $i=0;
        foreach($completedByDate as $date => $value)
        { 
            $completedSubmissionData[$i]["date"] = date('Y-m-d',$date);
            $completedSubmissionData[$i]["completed"] = count($value);
            $i++;
        }

        ksort($openSubmissionsByDate);
        $i=0;
        foreach($openSubmissionsByDate as $date => $value)
        { 
            $openSubmissionData[$i]["date"] = date('Y-m-d',$date);
            $openSubmissionData[$i]["open_review"] = count($value);
            $i++;
        }

        foreach ($projectAnwers as $answer) {
            $answerId = $answer->getObjectId();
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");

            if($baseLineFlag=='red')
                $redFlags['baseLineFlag'][]=$answerId;

            if($previousFlag=='red')
                $redFlags['previousFlag'][]=$answerId;

            if($baseLineFlag=='amber')
                $amberFlags['baseLineFlag'][]=$answerId;

            if($previousFlag=='amber')
                $amberFlags['previousFlag'][]=$answerId;
        }

        $data['redFlags'] = $redFlags;
        $data['amberFlags'] = $amberFlags;
        $data['missed'] = count($missed);
        $data['completed'] = count($completed);
        $data['openSubmissions'] = count($openSubmissions);

        $data['missedSubmissionData'] = json_encode($missedSubmissionData);
        $data['completedSubmissionData'] = json_encode($completedSubmissionData);
        $data['openSubmissionData'] = json_encode($openSubmissionData);
         
        return $data;
    }

    public function projectFlagsCount($projectAnwers)
    {

        $redFlagsByDate = [];
        $amberFlagsByDate = [];
        $basLineRedFlags =[];
        $previousRedFlags =[];
      
        foreach ($projectAnwers as $answer)
        {
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $responseId = $answer->get("response")->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y');
            $answerDate = strtotime($answerDate);

            if($questionType!='single-choice')
                continue;

            if(!isset($redFlagsByDate[$answerDate]))
            {
                $redFlagsByDate[$answerDate]['baseLine']=[];
                $amberFlagsByDate[$answerDate]['baseLine']=[];
                $redFlagsByDate[$answerDate]['previous']=[];
                $amberFlagsByDate[$answerDate]['previous']=[];
            }

            if($baseLineFlag=='red')
            {
              $redFlagsByDate[$answerDate]['baseLine'][] = $responseId;
            }

            if($baseLineFlag=='amber')
            {
                $amberFlagsByDate[$answerDate]['baseLine'][] = $responseId;

            }

            if($previousFlag =='red') 
            {
                $redFlagsByDate[$answerDate]['previous'][] = $responseId;
            }

            if($previousFlag =='amber') 
            {
                $amberFlagsByDate[$answerDate]['previous'][] = $responseId;
            }

            
        }

        $redFlagData = [];
        $amberFlagData = [];

        ksort($redFlagsByDate);
        $i=0;
        foreach($redFlagsByDate as $date => $value)
        { 
            $redFlagData[$i]["date"] = date('Y-m-d',$date);
            $redFlagData[$i]["base_line"] = count($value['baseLine']);
            $redFlagData[$i]["previous"] = count($value['previous']) ;
 
            $i++;
        }

        ksort($amberFlagsByDate);
        $i=0;
        foreach($amberFlagsByDate as $date => $value)
        { 
            $amberFlagData[$i]["date"] = date('Y-m-d',$date);
            $amberFlagData[$i]["base_line"] = count($value['baseLine']);
            $amberFlagData[$i]["previous"] = count($value['previous']) ;
 
            $i++;
        }
       

        $data['redFlags'] = json_encode($redFlagData);
        $data['amberFlags'] = json_encode($amberFlagData);

        return $data;
    }

    public function projectSubmissionCount($projectResponses)
    {

        $totalSubmissionsByDate = [];
        $totalMissedByDate = [];
        $totalOpenSubmissionsFlags =[];
        
        foreach ($projectResponses as $response) {
            $responseId = $response->getObjectId();
            $status = $response->get("status");
            $reviewed = $response->get("reviewed");
            $responseDate = $response->get("occurrenceDate")->format('d-m-Y');
            $responseDate = strtotime($responseDate);


            if($status=='missed')
                $totalMissedByDate[$responseDate][]=$responseId;

            if($reviewed=='open')
                $totalOpenSubmissionsFlags[$responseDate][]=$responseId;

            $totalSubmissionsByDate[$responseDate][]=$responseId;
        }

 
        $totalSubmissionsData = [];
        $totalMissedData = [];
        $totalOpenSubmissionData = [];

        ksort($totalSubmissionsByDate);
        $i=0;
        foreach($totalSubmissionsByDate as $date => $value)
        { 
            $totalSubmissionsData[$i]["date"] = date('Y-m-d',$date);
            $totalSubmissionsData[$i]["submission"] = count($value);
 
            $i++;
        }

        ksort($totalMissedByDate);
        $i=0;
        foreach($totalMissedByDate as $date => $value)
        { 
            $totalMissedData[$i]["date"] = date('Y-m-d',$date);
            $totalMissedData[$i]["missed"] = count($value);
 
            $i++;
        }

        ksort($totalOpenSubmissionsFlags);
        $i=0;
        foreach($totalOpenSubmissionsFlags as $date => $value)
        { 
            $totalOpenSubmissionData[$i]["date"] = date('Y-m-d',$date);
            $totalOpenSubmissionData[$i]["open_review"] = count($value);
 
            $i++;
        }
       

        $data['submission'] = json_encode($totalSubmissionsData);
        $data['missed'] = json_encode($totalMissedData);
        $data['openReview'] = json_encode($totalOpenSubmissionData);
          
        return $data;
    }

    public function patientsFlagSummary($projectAnwers)
    {
        $patientFlagsData = [];

      
        foreach ($projectAnwers as $answer)
        {
            $patient = $answer->get("patient");
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $responseId = $answer->get("response")->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y');
            $answerDate = strtotime($answerDate);

            if($questionType!='single-choice')
                continue;

            if(!isset($patientFlagsData[$patient]))
            {
                $patientFlagsData[$patient]['redBaseLine']=[];
                $patientFlagsData[$patient]['amberBaseLine']=[];
                $patientFlagsData[$patient]['greenBaseLine']=[];
                $patientFlagsData[$patient]['redPrevious']=[];
                $patientFlagsData[$patient]['amberPrevious']=[];
                $patientFlagsData[$patient]['greenPrevious']=[];
 
            }

            if($baseLineFlag=='red')
            {
              $patientFlagsData[$patient]['redBaseLine'][] = $responseId;
            }

            if($baseLineFlag=='amber')
            {
               $patientFlagsData[$patient]['amberBaseLine'][] = $responseId;

            }

            if($baseLineFlag=='green')
            {
               $patientFlagsData[$patient]['greenBaseLine'][] = $responseId;

            }

            if($previousFlag =='red') 
            {
                $patientFlagsData[$patient]['redPrevious'][] = $responseId;
            }

            if($previousFlag =='amber') 
            {
                $patientFlagsData[$patient]['amberPrevious'][] = $responseId;
            }

            if($baseLineFlag=='green')
            {
               $patientFlagsData[$patient]['greenPrevious'][] = $responseId;

            }

            
        }

         

        return $patientFlagsData;
    }

    // public function getSubmissionsSummary($projectAnwers)
    // {
    //     $submissionsData = [];

    //     foreach ($projectAnwers as $answer) {
    //         $patient = $answer->get("patient");
    //         $baseLineFlag = $answer->get("baseLineFlag");
    //         $previousFlag = $answer->get("previousFlag");
    //         $score = $answer->get("score");
    //         $totalScore = $answer->get("response")->get("totalScore");
    //         $responseId = $answer->get("response")->getObjectId();
    //         $responseStatus = $answer->get("response")->get("status");
    //         $questionId = $answer->get("question")->getObjectId();
    //         $questionType = $answer->get("question")->get("type");
    //         $sequenceNumber = $answer->get("response")->get("sequenceNumber");
    //         $occurrenceDate = $answer->get("response")->get("occurrenceDate")->format('dS M');
    //         $comparedToBaslineScore = $answer->get("response")->get("comparedToBaseLine");
    //         $comparedToPrevious = $answer->get("response")->get("comparedToPrevious");

    //         if($responseStatus!='completed')
    //             continue;

    //         if($questionType!='single-choice')
    //             continue;

    //         if(!isset($submissionsData[$responseId]))
    //         {
    //             $submissionsData[$responseId]['patient'] ='';
    //             $submissionsData[$responseId]['sequenceNumber'] ='';
    //             $submissionsData[$responseId]['occurrenceDate'] ='';
    //             $submissionsData[$responseId]['totalScore'] = 0;
    //             $submissionsData[$responseId]['previousScore'] =0;
    //             $submissionsData[$responseId]['baseLineScore'] =0;
    //             $submissionsData[$responseId]['baseLineFlag']['red']=[];
    //             $submissionsData[$responseId]['baseLineFlag']['green']=[];
    //             $submissionsData[$responseId]['baseLineFlag']['amber']=[];

    //             $submissionsData[$responseId]['previousFlag']['red']=[];
    //             $submissionsData[$responseId]['previousFlag']['green']=[];
    //             $submissionsData[$responseId]['previousFlag']['amber']=[];

    //         }

             

    //         $submissionsData[$responseId]['patient'] = $patient;
    //         $submissionsData[$responseId]['sequenceNumber']= $sequenceNumber;
    //         $submissionsData[$responseId]['occurrenceDate']= $occurrenceDate;
    //         $submissionsData[$responseId]['totalScore'] = $totalScore;
    //         $submissionsData[$responseId]['baseLineScore']= $comparedToBaslineScore;
    //         $submissionsData[$responseId]['previousScore']= $comparedToPrevious;

    //         if($baseLineFlag !=null )
    //         {   
    //             $submissionsData[$responseId]['baseLineFlag'][$baseLineFlag][]= $baseLineFlag;
    //             $submissionsData[$responseId]['previousFlag'][$previousFlag][]= $previousFlag;
                
    //         }

 
    //     }
    //     // dd($submissionsData); 
    //     return $submissionsData;
    // }

    public function getSubmissionsSummary($responses)
    {
        $submissionsData = [];

        foreach ($responses as $response) {
            $patient = $response->get("patient");
            $baseLineFlag = $response->get("baseLineFlag");
            $previousFlag = $response->get("previousFlag");
            $score = $response->get("score");
            $totalScore = $response->get("totalScore");
            $responseId = $response->getObjectId();
            $reviewed = $response->get("reviewed");
            $status = $response->get("status");

            $baseLineTotalRedFlags = $response->get("baseLineTotalRedFlags");
            $baseLineTotalAmberFlags = $response->get("baseLineTotalAmberFlags");
            $baseLineTotalGreenFlags = $response->get("baseLineTotalGreenFlags");
            $previousTotalRedFlags = $response->get("previousTotalRedFlags");
            $previousTotalAmberFlags = $response->get("previousTotalAmberFlags");
            $previousTotalGreenFlags = $response->get("previousTotalGreenFlags");
 
 
            $sequenceNumber = $response->get("sequenceNumber");
            $occurrenceDate = $response->get("occurrenceDate")->format('dS M');
            $comparedToBaslineScore = $response->get("comparedToBaseLine");
            $comparedToPrevious = $response->get("comparedToPrevious");

            $submissionsData[$responseId]['totalBaseLineFlag'] = $baseLineFlag;
            $submissionsData[$responseId]['totalPreviousFlag'] = $previousFlag;
          
            $submissionsData[$responseId]['patient'] = $patient;
            $submissionsData[$responseId]['reviewed'] = $reviewed;
            $submissionsData[$responseId]['status'] = $status;
            $submissionsData[$responseId]['sequenceNumber']= $sequenceNumber;
            $submissionsData[$responseId]['occurrenceDate']= $occurrenceDate;
            $submissionsData[$responseId]['totalScore'] = $totalScore;
            $submissionsData[$responseId]['baseLineScore'] = $totalScore + $comparedToBaslineScore;
            $submissionsData[$responseId]['previousScore'] = ($previousFlag=='')?0:$totalScore + $comparedToPrevious;
            $submissionsData[$responseId]['comparedToBaslineScore']= $comparedToBaslineScore;
            $submissionsData[$responseId]['comparedToPrevious']= $comparedToPrevious;

            $submissionsData[$responseId]['baseLineFlag']['red']=$baseLineTotalRedFlags;
            $submissionsData[$responseId]['baseLineFlag']['green']=$baseLineTotalGreenFlags;
            $submissionsData[$responseId]['baseLineFlag']['amber']=$baseLineTotalAmberFlags;

            $submissionsData[$responseId]['previousFlag']['red']=$previousTotalRedFlags;
            $submissionsData[$responseId]['previousFlag']['green']=$previousTotalGreenFlags;
            $submissionsData[$responseId]['previousFlag']['amber']=$previousTotalAmberFlags;

 
        }
        // dd($submissionsData); 
        return $submissionsData;
    }

    public function reports($hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $inputs = Input::get();

        $referenceCode = (isset($inputs['referenceCode']))?$inputs['referenceCode']:0;
        $allPatients = User::where('type','patient')->lists('reference_code')->toArray();

        if(!$referenceCode)
        {
            $referenceCode = current($allPatients);
        }

        $patients[] = $referenceCode;
        $responseArr=[];
        
        $patientController = new PatientController();
        $responses  = $patientController->getPatientsResponses($patients,$projectId,0,[]);
 
        foreach ($responses as  $response) {
            $responseId = $response->getObjectId();
            $responseArr[$responseId] = $response->get("occurrenceDate")->format('d M');
        }



        $patientAnswers  = $patientController->getPatientAnwers($referenceCode,$projectId,0,[]);

        $patientChartdata = $patientController->getQuestionChartData($patientAnswers);



        $singleChoiceQuestion = $patientChartdata['singleChoiceQuestion']; 
        $questionLabels = $patientChartdata['questionLabels'];
        $questionChartData = $patientChartdata['chartData'];
        $questionBaseLine = $patientChartdata['questionBaseLine'];
        $submissions = $patientChartdata['submissions'];      

        return view('project.reports')->with('active_menu', 'reports')
                                        ->with('responseArr', $responseArr)
                                        ->with('hospital', $hospital)
                                        ->with('questionChartData', $questionChartData)
                                        ->with('questionLabels', $questionLabels)
                                        ->with('singleChoiceQuestion', $singleChoiceQuestion)
                                        ->with('questionBaseLine', $questionBaseLine)
                                        ->with('submissions', $submissions)
                                        ->with('allPatients', $allPatients)
                                        ->with('project', $project);


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
