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
use App\Http\Controllers\Hospital\HospitalController;
use App\UserAccess;
use \Input;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($hospitalSlug)
    {
        $inputs = Input::get(); 

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('-1 months'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));


        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $projectIds =[];
        if(\Auth::user()->type=='hospital_user' && \Auth::user()->project_access=='no')
        {
            $userId = \Auth::user()->id;
            $projectIds = UserAccess::where(['user_id'=>$userId,'object_type'=>'project'])->lists('object_id')->toArray(); 
            //$allProjects = Projects::where('hospital_id',$hospital['id'])->whereIn('id',$projectIds)->get()->toArray();  
        }
        else
        {
            $projectIds = Projects::where('hospital_id',$hospital['id'])->lists('id')->toArray();  
        }   
        
        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        //get missed response count
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","missed");
        $responseQry->containedIn("project",$projectIds);

        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        $missedResponses = $responseQry->count();  

        $responses = $this->getResponses($projectIds,0,[] ,$startDateObj,$endDateObj); 
        $completedSubmissionCount = count($responses);
        $openStatus = [];
        $closedStatus = [];
        $timeDifference = [];
        
        foreach ($responses as $key => $response) {
            $status = $response->get("status");
            $previousFlagStatus = $response->get("previousFlagStatus");
            $createdAt = $response->getCreatedAt()->format('Y-m-d H:i:s');
            $updatedAt = $response->getUpdatedAt()->format('Y-m-d H:i:s');
            $responseId = $response->getObjectId();

            if ($previousFlagStatus=='closed') {
                $diff = strtotime($updatedAt) - strtotime($createdAt);
                $timeDifference[] = $diff/3600;
                $closedStatus[] = $responseId;
            }
            elseif ($previousFlagStatus=='open') {
                $openStatus[] = $responseId;
            }
        }

        $totalSubmissions = $completedSubmissionCount + $missedResponses;

        $responseRate = ($completedSubmissionCount) ? ($completedSubmissionCount/$totalSubmissions) * 100 :0;
        $responseRate =  round($responseRate,2);

        $avgReviewTime = (count($timeDifference)) ? array_sum($timeDifference) / count($timeDifference) :0;

        $hospitalController = new HospitalController();
        $submissionFlags = $hospitalController->responseAnswerFlags($responses); 

        return view('hospital.submissions-list')->with('active_menu', 'submission')
                                                 ->with('hospital', $hospital)
                                                 ->with('logoUrl', $logoUrl)
                                                 ->with('endDate', $endDate)
                                                 ->with('startDate', $startDate)
                                                 ->with('avgReviewTime', $avgReviewTime)
                                                 ->with('responseRate', $responseRate)
                                                 ->with('completedSubmissionCount', $completedSubmissionCount)
                                                 ->with('missedResponses', $missedResponses)
                                                 ->with('openStatus', count($openStatus))
                                                 ->with('closedStatus', count($closedStatus))
                                                 ->with('totalSubmissions', $totalSubmissions)
                                                 ->with('submissionFlags', $submissionFlags);
    }

    public function getResponses($projectIds,$page=0,$responseData,$startDate,$endDate)
    {
        $displayLimit = 20; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",["completed"]);
        
        if(!empty($projectIds))
            $responseQry->containedIn("project",$projectIds);
        
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $responseQry->descending("occurrenceDate");
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 
         
        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getResponses($projectIds,$page,$responseData ,$startDate,$endDate);
        }  
        
        return $responseData;
     
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
    public function show($hospitalSlug ,$responseId)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $data =  $this->getSubmissionData($responseId);
        $questionnaire = $data['questionnaire'];
        $date = $data['date']; 
        $answersList = $data['answers'];
        $response = $data['response'];

        $referenceCode = $response->get("patient");
        $sequenceNumber = $response->get("sequenceNumber");

        $patient = User::where('reference_code',$referenceCode)->first()->toArray(); 

        $oldResponseQry = new ParseQuery("Response");
        $oldResponseQry->notEqualTo("objectId", $responseId);
        $oldResponseQry->equalTo("patient", $referenceCode);
        $oldResponseQry->equalTo("status", "completed");
        $oldResponseQry->lessThan("createdAt", $response->getCreatedAt()); 
        $oldResponseQry->descending("updatedAt");
        $oldResponse = $oldResponseQry->first();

        $baseLineResponseQry = new ParseQuery("Response");
        $baseLineResponseQry->equalTo("patient", $referenceCode);
        $baseLineResponseQry->equalTo("status", "base_line");
        $baseLineResponse = $baseLineResponseQry->first();


        $previousAnswersList =[];
        if(!empty($oldResponse))
        {
            $previousData =  $this->getSubmissionData($oldResponse->getObjectId());
            $previousAnswersList = $previousData['answers'];
        }

        $baseLineAnswersList =[];
        if(!empty($baseLineResponse))
        {
            $baseLineData =  $this->getSubmissionData($baseLineResponse->getObjectId());
            $baseLineAnswersList = $baseLineData['answers'];
        }

        return view('hospital.submissions-view')->with('active_menu', 'patients')
                                                ->with('active_tab', 'submissions')
                                                ->with('tab', '02')
                                                ->with('patient', $patient)
                                                ->with('referenceCode', $referenceCode)
                                                ->with('sequenceNumber', $sequenceNumber)
                                                ->with('hospital', $hospital)
                                                ->with('logoUrl', $logoUrl)
                                                ->with('questionnaire', $questionnaire)
                                                ->with('date', $date)
                                                ->with('answersList', $answersList)
                                                ->with('previousAnswersList', $previousAnswersList)
                                                ->with('baseLineAnswersList', $baseLineAnswersList);
    }

    public function getSubmissionData($responseId)
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

        $questionnaire = $response->get("questionnaire")->get("name");
        $date = $response->getUpdatedAt()->format('d-m-Y');
         
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
                $answersList[$questionId]= [ 'id' => $answers->getObjectId(),
                                        'questionId' => $answers->get("question")->getObjectId(),
                                        'question' => $answers->get("question")->get("question"), 
                                        'questionType' => $questionType, 
                                        'option' => $option,  
                                        'value' => $answers->get("value"),  
                                        'baseLineFlag' => $answers->get("baseLineFlag"),  
                                        'previousFlag' => $answers->get("previousFlag"),  
                                        'updatedAt' => $answers->getUpdatedAt()->format('d-m-Y'),    
                          ];
           }
           
        }

        $data = ['questionnaire'=>$questionnaire ,'date'=>$date , 'answers'=>$answersList, 'response'=>$response] ;
        return $data;
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
