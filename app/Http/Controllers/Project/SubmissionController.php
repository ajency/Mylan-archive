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
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Project\PatientController;
use App\UserAccess;
use \Input;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $inputs = Input::get(); 

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('-1 months'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));
 
  
        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        $responseRate = [];   
         // get missed count
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","missed");
        $responseQry->equalTo("project",$projectId);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        $responseRate['missedCount'] = $responseQry->count();

        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","late");
        $responseQry->equalTo("project",$projectId);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        $responseRate['lateCount'] = $responseQry->count();

        // get completed count
        $submissionStatus = '';
        if(isset($inputs['submissionStatus']))
        {
            
            $submissionStatus = $inputs['submissionStatus'];

            if($submissionStatus=='completed')
            {
              //$responseRate['missedCount'] =0;
              $responseRate['lateCount']  =0;
              $responseStatus = [$inputs['submissionStatus']];
            }
            // elseif($submissionStatus=='missed')
            // {
            //   $responseRate['lateCount']  =0;
            //   $responseStatus = [];
            // }
            elseif($submissionStatus=='late')
            {
              //$responseRate['missedCount'] =0;
              $responseStatus = [$inputs['submissionStatus']];
            }
        }
        else
        {
            $responseStatus = ["completed","late"];
        }

        $projectController = new ProjectController();
        
        $patientSubmissions = $projectController->getProjectResponsesByDate($projectId,0,[] ,$startDateObj,$endDateObj,$responseStatus);  
                
        $timeDifference = [];
        $completedResponses = [];
        
        foreach ($patientSubmissions as $key => $response) {
            $reviewed = $response->get("reviewed");
            $status = $response->get("status");
      
            $createdAt = $response->getCreatedAt()->format('Y-m-d H:i:s');
            $updatedAt = $response->getUpdatedAt()->format('Y-m-d H:i:s');
            $responseId = $response->getObjectId();

            if($status=='completed')
            {
              $completedResponses[] = $response;
            }

            if ($reviewed=='reviewed') {
                $datediff = strtotime($updatedAt) - strtotime($createdAt);
                $timeDifference[] = ceil($datediff/(60*60*24));
            }
             
        }

        $totalResponses = count($patientSubmissions)+$responseRate['missedCount']; 

        $responseRate['completedCount'] = count($completedResponses);

        $completed = ($totalResponses) ? (count($completedResponses)/$totalResponses) * 100 :0;
        $responseRate['completed'] =  round($completed,2);

        $missed = ($totalResponses) ? ($responseRate['missedCount']/$totalResponses) * 100 :0;
        $responseRate['missed'] =  round($missed,2);

        $late = ($totalResponses) ? ($responseRate['lateCount']/$totalResponses) * 100 :0;
        $responseRate['late'] =  round($late,2);



        $avgReviewTime = (count($timeDifference)) ? array_sum($timeDifference) / count($timeDifference) :0;

        $submissionsSummary = $projectController->getSubmissionsSummary($patientSubmissions);

        return view('project.submissions-list')->with('active_menu', 'submission')
                                                 ->with('hospital', $hospital)
                                                 ->with('project', $project)
                                                 ->with('logoUrl', $logoUrl)
                                                 ->with('endDate', $endDate)
                                                 ->with('startDate', $startDate)
                                                 ->with('avgReviewTime', round($avgReviewTime))
                                                 ->with('responseRate', $responseRate)
                                                 ->with('submissionStatus', $submissionStatus)
                                                 ->with('submissionsSummary', $submissionsSummary);
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
    public function show($hospitalSlug ,$projectSlug,$submissionId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $data =  $this->getSubmissionData($submissionId,true);
        $questionnaire = $data['questionnaire'];
        $date = $data['date']; 
        $answersList = $data['answers'];
        $response = $data['response'];
        $currentChartData = $data['chartData'];
        $previousSubmissionId = $data['previousSubmission'];
        $baseLineId = $data['baseLine'];
        

        $responseData['comparedToBaseLine'] = $response->get("comparedToBaseLine");
        $responseData['comparedToPrevious'] = $response->get("comparedToPrevious");
        $responseData['baseLineFlag'] = $response->get("baseLineFlag");
        $responseData['previousFlag'] = $response->get("previousFlag");
        $responseData['reviewed'] = $response->get("reviewed");

        $referenceCode = $response->get("patient");
        $sequenceNumber = $response->get("sequenceNumber");

        $patient = User::where('reference_code',$referenceCode)->first()->toArray(); 

        // $oldResponseQry = new ParseQuery("Response");
        // $oldResponseQry->notEqualTo("objectId", $submissionId);
        // $oldResponseQry->equalTo("patient", $referenceCode);
        // $oldResponseQry->equalTo("status", "completed");
        // $oldResponseQry->lessThan("createdAt", $response->getCreatedAt()); 
        
        // $oldResponse = $oldResponseQry->first();

        // $baseLineResponseQry = new ParseQuery("Response");
        // $baseLineResponseQry->equalTo("patient", $referenceCode);
        // $baseLineResponseQry->equalTo("status", "base_line");
        // $baseLineResponseQry->descending("createdAt");
        // $baseLineResponse = $baseLineResponseQry->first();

        $previousAnswersList =[];
        $previousChartData = [];
        if($previousSubmissionId!='')
        {
            $previousData =  $this->getSubmissionData($previousSubmissionId,false);
            $previousAnswersList = $previousData['answers'];
            $previousChartData = $previousData['chartData'];
        }

        $baseLineAnswersList =[];
        $baseChartData = [];
        if($baseLineId!='')
        {
            $baseLineData =  $this->getSubmissionData($baseLineId,false);
            $baseLineAnswersList = $baseLineData['answers'];
            $baseChartData = $baseLineData['chartData'];
        }
       
        // get patient submissions
        $allSubmissions = [];
        $patientController = new PatientController();
        $completedResponses = $patientController->getPatientsResponses([$referenceCode],0,[],["completed","late"]);

        foreach ($completedResponses as $key => $response) {
            $responseId = $response->getObjectId();
            $sequenceNumber = $response->get("sequenceNumber");
            $allSubmissions[$responseId] = "Submission ".$sequenceNumber;
        }

        $submissionChart = [];
        foreach ($baseChartData as $questionId => $chartData) {
            $currentScore = (isset($currentChartData[$questionId]['score']))?$currentChartData[$questionId]['score']:0;
            $baseScore = $chartData['score'];
            $previousScore = (isset($previousChartData[$questionId]['score']))?$previousChartData[$questionId]['score']:0;
            $question = $chartData['question'];
            $submissionChart[] =["question"=> $question,"base"=> $baseScore,"prev"=> $previousScore,"current"=> $currentScore];
             
        }

        $submissionJson = json_encode($submissionChart);

        
        return view('project.submissions-view')->with('active_menu', 'patients')
                                                ->with('active_tab', 'submissions')
                                                ->with('tab', '02')
                                                ->with('patient', $patient)
                                                ->with('referenceCode', $referenceCode)
                                                ->with('sequenceNumber', $sequenceNumber)
                                                ->with('hospital', $hospital)
                                                ->with('project', $project)
                                                ->with('logoUrl', $logoUrl)
                                                ->with('questionnaire', $questionnaire)
                                                ->with('date', $date)
                                                ->with('answersList', $answersList)
                                                ->with('currentSubmission', $submissionId)
                                                ->with('responseData', $responseData)
                                                ->with('allSubmissions', $allSubmissions)
                                                ->with('submissionJson', $submissionJson)
                                                ->with('previousAnswersList', $previousAnswersList)
                                                ->with('baseLineAnswersList', $baseLineAnswersList);
    }

     public function getSubmissionData($responseId,$flag)
    { 
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("objectId", $responseId);
        $responseQry->includeKey('questionnaire');
        $response = $responseQry->first();
        

        $answerQry = new ParseQuery("Answer");
        $answerQry->equalTo("response",$response);
        $answerQry->includeKey('question');
        $answerQry->includeKey('option');
        $answers = $answerQry->find(); 
         
        $answersList =[];
        $chartData =[];

        $questionnaire = $response->get("questionnaire")->get("name");
        if($flag)
        {
          $baseLine = $response->get("baseLine")->getObjectId();
          if(!is_null($response->get("previousSubmission")))
            $previousSubmission = $response->get("previousSubmission")->getObjectId();
          else
            $previousSubmission = '';
        }
        else
        {
          $baseLine = '';
          $previousSubmission = '';
        }
        $date = $response->getUpdatedAt()->format('d-m-Y');
        
        
        // ($response->get("previousSubmission")==null)?'' : $response->get("previousSubmission")->getObjectId();

 
          
        foreach($answers as $answers)
        {  
           $question =  $answers->get("question");
           $questionId =  $question->getObjectId();
           $questionType =  $question->get("type");


           if($questionType == 'multi-choice')
           {
                if(!isset($answersList[$questionId]))
                {
                   $answersList[$questionId]= [ 'id' => $answers->getObjectId(),
                                        'questionId' => $answers->get("question")->getObjectId(),
                                        'question' => $answers->get("question")->get("question"), 
                                        'questionType' => $questionType, 
                                        'option' => [$answers->get("option")->get("label")],  
                                        'value' => $answers->get("value"),   
                                        'updatedAt' => $answers->getUpdatedAt()->format('d-m-Y'),    
                          ]; 
                }
                else
                {

                   $answersList[$questionId]['option'][] = $answers->get("option")->get("label");
                }
                
           }
           else
           {
                $option = ($answers->get("option") !='')?$answers->get("option")->get("label"):'';
                $answersList[$questionId] = [ 'id' => $answers->getObjectId(),
                                        'questionId' => $answers->get("question")->getObjectId(),
                                        'question' => $answers->get("question")->get("question"), 
                                        'questionType' => $questionType, 
                                        'option' => $option,  
                                        'value' => $answers->get("value"),  
                                        'baseLineFlag' => $answers->get("baseLineFlag"),  
                                        'previousFlag' => $answers->get("previousFlag"), 
                                        'comparedToBaseLine' => $answers->get("comparedToBaseLine"),  
                                        'comparedToPrevious' => $answers->get("comparedToPrevious"),  
                                        'updatedAt' => $answers->getUpdatedAt()->format('d-m-Y'),    
                          ];
           }

           if($questionType == 'single-choice')
                $chartData[$answers->get("question")->getObjectId()] =['question'=>$answers->get("question")->get("title"),'score'=>$answers->get("score")];
           elseif($questionType == 'input')
                $chartData[$answers->get("question")->getObjectId()] =['question'=>$answers->get("question")->get("title"),'score'=>$answers->get("value")];
           
        }


        $data = ['questionnaire'=>$questionnaire ,'date'=>$date ,'baseLine'=>$baseLine ,'previousSubmission'=>$previousSubmission , 'answers'=>$answersList, 'response'=>$response,'chartData'=>$chartData] ;
        return $data;
    }

    public function getSubmissionFlags($hospitalSlug ,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $patientsllFlags = [];
        $patientsFlags = [];
        $responseOpenRedFlags = [];

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

        $filterType = (isset($inputs['type']))?$inputs['type']:'';
        $activeTab = (isset($inputs['active']))?$inputs['active']:'all';

        $responseStatus = ["completed","late","missed"];
        $projectController = new ProjectController(); 
        $projectAnswers = $projectController->getProjectAnwersByDate($projectId,0,[],$startDateObj,$endDateObj);
 
        $patientController = new PatientController(); 
        $submissionFlags =  $patientController->getsubmissionFlags($projectAnswers,$filterType); 
 

        return view('project.flags')->with('active_menu', 'flags')
                                               ->with('hospital', $hospital)
                                               ->with('project', $project)
                                               ->with('endDate', $endDate)
                                               ->with('startDate', $startDate)
                                               ->with('filterType', $filterType)
                                               ->with('activeTab', $activeTab)
                                               ->with('submissionFlags', $submissionFlags);
    }

    public function getProjectAnwers($projectId,$page=0,$anwsersData)
    {
        $displayLimit = 20; 

        $answersQry = new ParseQuery("Answer");
        $answersQry->equalTo("project",$projectId);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->limit($displayLimit);
        $answersQry->skip($page * $displayLimit);
        $answersQry->ascending("occurrenceDate");
 
        $anwsers = $answersQry->find();
        $anwsersData = array_merge($anwsers,$anwsersData); 

        if(!empty($anwsers))
        {
            $page++;
            $anwsersData = $this->getProjectAnwers($projectId,$page,$anwsersData);
        }  
        
        return $anwsersData;
     
    }

    public function updateSubmissionStatus(Request $request, $hospitalSlug ,$projectSlug, $responseId)
    {
       
        try{
            $data = $request->all();  
            $reviewStatus = $data['status'];

            $responseObj = new ParseQuery("Response");
            $response = $responseObj->get($responseId);

            $response->set('reviewed',$reviewStatus);
            $response->save(); 

            $json_resp = array(
              'code' => 'success' , 
              'message' => 'successfully updated'
            );
             $status_code = 200; 

        }
        catch (Exception $ex) {

            $json_resp = array(
              'code' => 'Failed' , 
              'message' => 'Error in update'
            );
            $status_code = 404;        
        }

        return response()->json( $json_resp, $status_code); 
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
