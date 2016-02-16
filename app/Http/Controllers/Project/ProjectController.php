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

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('-1 months'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                     );

        $startDateYmd = date('Y-m-d', strtotime($startDate));
        $endDateYmd = date('Y-m-d', strtotime($endDate.'+1 day'));

        $patients = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->where('created_at','>=',$startDateYmd)->where('created_at','<=',$endDateYmd)->orderBy('created_at')->get()->toArray();

        //dd($patients);
        $activepatients = [];
        $patientReferenceCode = [];
        foreach ($patients as  $patient) {
            
            if($patient['account_status']=='active')
                $activepatients[]= $patient['reference_code'];
            
            $patientReferenceCode[] = $patient['reference_code'];
        }


        $responseStatus = ["completed","late","missed"];
        $projectResponses = $this->getProjectResponsesByDate($projectId,0,[],$startDateObj,$endDateObj,$responseStatus); 
        // //$projectAnwers = $this->getProjectAnwersByDate($projectId,0,[],$startDateObj,$endDateObj);

         $responseCount = $this->getProjectResponseCounts($projectResponses);
        // //red flags,amber flags ,unreviwed submission , submission
         $projectFlagsChart = $this->projectFlagsChart($projectResponses); 

         //patient submissions
        $lastFiveSubmissions = array_slice($responseCount['patientSubmissions'], 0, 5, true);
        $submissionsSummary = $this->getSubmissionsSummary($lastFiveSubmissions); 

        $fivepatient = array_slice($patientReferenceCode, 0, 5, true);
        $patientController = new PatientController();
        $patientResponses = $patientController->patientsSummary($fivepatient ,$startDateObj,$endDateObj); 
 


        return view('project.dashbord')->with('active_menu', 'dashbord')
                                        ->with('responseCount', $responseCount) 
                                        ->with('activepatients', count($activepatients))
                                        ->with('allpatientscount', count($patientReferenceCode))
                                        ->with('responseCount', $responseCount)
                                        ->with('submissionsSummary', $submissionsSummary)
                                        ->with('patientResponses', $patientResponses['patientResponses'])
                                        ->with('projectFlagsChart', $projectFlagsChart)
                                        ->with('project', $project)
                                        ->with('patients', $patients)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl);

    }

    public function getProjectAnwersByDate($projectId,$page=0,$anwsersData,$startDate,$endDate)
    {
        $displayLimit = 90; 

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
        $displayLimit = 90; 

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

    public function getProjectResponseCounts($projectResponses)
    {
        $completedResponses =[];
        $lateResponses=[];
        $patientSubmissions = [];
        $missedResponses=[];
        $responseRate = [];
        $unreviewed = [];
        $redFlags = [];
        $amberFlags = [];
        foreach ($projectResponses as  $response) {
            $responseId = $response->getObjectId();
            $responseStatus = $response->get("status");
            $reviewed = $response->get("reviewed");
            
            $baseLineTotalRedFlags = $response->get("baseLineTotalRedFlags");
            $baseLineTotalAmberFlags = $response->get("baseLineTotalAmberFlags");
            $baseLineTotalGreenFlags = $response->get("baseLineTotalGreenFlags");
            $previousTotalRedFlags = $response->get("previousTotalRedFlags");
            $previousTotalAmberFlags = $response->get("previousTotalAmberFlags");
            $previousTotalGreenFlags = $response->get("previousTotalGreenFlags");


            if ($responseStatus=='completed') {
                $completedResponses[]= $responseId;
                $patientSubmissions[]=$response;
            }
            elseif ($responseStatus=='late') {
                $lateResponses[]= $responseId;
                $patientSubmissions[]=$response;
            }
            elseif ($responseStatus=='missed') {
                $missedResponses[]= $responseId;
            }

            if($reviewed=='unreviewed')
            {
                $unreviewed []= $responseId;
            }
            if ($responseStatus=='completed' || $responseStatus=='late') {
                 
                    $redFlags['baseLine'][]=$baseLineTotalRedFlags;
                 
                    $amberFlags['baseLine'][]=$baseLineTotalAmberFlags;
                 
                    $redFlags['previous'][]=$previousTotalRedFlags;
                 
                    $amberFlags['previous'][]=$previousTotalAmberFlags;
                 
            }
        }

        $totalResponses = count($projectResponses);
        $data['completedCount'] = count($completedResponses);
        $data['missedCount'] = count($missedResponses);
        $data['lateCount'] = count($lateResponses);

        $completed = ($totalResponses) ? (count($completedResponses)/$totalResponses) * 100 :0;
        $data['completed'] =  round($completed,2);

        $missed = ($totalResponses) ? (count($missedResponses)/$totalResponses) * 100 :0;
        $data['missed'] =  round($missed,2);

        $late = ($totalResponses) ? (count($lateResponses)/$totalResponses) * 100 :0;
        $data['late'] =  round($late,2);

        $data['redBaseLine'] = (isset($redFlags['baseLine']))?array_sum($redFlags['baseLine']):0;
        $data['redPrevious'] = (isset($redFlags['previous']))?array_sum($redFlags['previous']):0;
        $data['amberBaseLine'] = (isset($amberFlags['baseLine']))?array_sum($amberFlags['baseLine']):0;
        $data['amberPrevious'] = (isset($amberFlags['previous']))?array_sum($amberFlags['previous']):0;
        $data['unreviewedSubmission'] = count($unreviewed);
        $data['patientSubmissions'] = $patientSubmissions;
        
 

        
         
        return $data;
    }

    public function projectFlagsChart($patientResponses)
    {

        $redFlagsByDate = [];
        $amberFlagsByDate = [];
        // $greenFlagsByDate = [];
        $unreviewedSubmissionByDate = [];
        $missedByDate = [];
        $completedByDate = [];
        $lateByDate = [];
       
        foreach ($patientResponses as $response) {
            $responseId = $response->getObjectId();
            
            $reviewed = $response->get("reviewed");

            $occurrenceDate = $response->get("occurrenceDate")->format('d-m-Y');
            $occurrenceDate = strtotime($occurrenceDate);


            $responseStatus = $response->get("status");

            if ($responseStatus=='completed') {
                $completedByDate[$occurrenceDate][]= $responseId;
            }
            elseif ($responseStatus=='late') {
                $missedByDate[$occurrenceDate][]= $responseId;
            }
            elseif ($responseStatus=='missed') {
                $missedByDate[$occurrenceDate][]= $responseId;
            }

            if($reviewed=='unreviewed')
            {
                $unreviewedSubmissionByDate[$occurrenceDate][]= $responseId;
            }


            $baseLineTotalRedFlags = $response->get("baseLineTotalRedFlags");
            $baseLineTotalAmberFlags = $response->get("baseLineTotalAmberFlags");
            $baseLineTotalGreenFlags = $response->get("baseLineTotalGreenFlags");
            $previousTotalRedFlags = $response->get("previousTotalRedFlags");
            $previousTotalAmberFlags = $response->get("previousTotalAmberFlags");
            $previousTotalGreenFlags = $response->get("previousTotalGreenFlags");

            
             
           if ($responseStatus=='completed' || $responseStatus=='late') {
                $redFlagsByDate[$occurrenceDate]['baseLine'][]=$baseLineTotalRedFlags;
                $amberFlagsByDate[$occurrenceDate]['baseLine'][]=$baseLineTotalAmberFlags;
                $greenFlagsByDate[$occurrenceDate]['baseLine'][]=$baseLineTotalGreenFlags;
                $redFlagsByDate[$occurrenceDate]['previous'][]=$previousTotalRedFlags;
                $amberFlagsByDate[$occurrenceDate]['previous'][]=$previousTotalAmberFlags;
                $greenFlagsByDate[$occurrenceDate]['previous'][]=$previousTotalGreenFlags;
            }
            //$totalFlagsByDate[$occurrenceDate] = $totalScore;
            // $baseLineByDate[$occurrenceDate] = $totalScore + $comparedToBaseLine;
            
        }

        
        $redFlagData = [];
        $amberFlagData = [];
        $greenFlagData = [];
        $unreviewedData = [];

        // $baslineFlagData = [];
        // $previousFlagData = [];

        ksort($redFlagsByDate);
        $i=0;
        foreach($redFlagsByDate as $date => $value)
        { 
            $redFlagData[$i]["Date"] = date('d M',$date);
            $redFlagData[$i]["Baseline"] = array_sum($value['baseLine']);
            $redFlagData[$i]["Previous"] = array_sum($value['previous']) ;
 
            $i++;
        }

        ksort($amberFlagsByDate);
        $i=0;
        foreach($amberFlagsByDate as $date => $value)
        { 
            $amberFlagData[$i]["Date"] =  date('d M',$date);
            $amberFlagData[$i]["Baseline"] = array_sum($value['baseLine']);
            $amberFlagData[$i]["Previous"] = array_sum($value['previous']) ;
 
            $i++;
        }

        // ksort($greenFlagsByDate);
        // $i=0;
        // foreach($greenFlagsByDate as $date => $value)
        // { 
        //     $greenFlagData[$i]["Date"] =  date('d M',$date);
        //     $greenFlagData[$i]["Baseline"] = $value['baseLine'];
        //     $greenFlagData[$i]["Previous"] = $value['previous'] ;
 
        //     $i++;
        // }


        ksort($unreviewedSubmissionByDate);
        $i=0;
        foreach($unreviewedSubmissionByDate as $date => $value)
        { 
            $unreviewedData[$i]["Date"] =  date('d M',$date);
            $unreviewedData[$i]["score"] = count($value);
 
            $i++;
        }
       
     
        $data['redFlags'] = json_encode($redFlagData);
        $data['amberFlags'] = json_encode($amberFlagData);
        $data['greenFlags'] = json_encode($greenFlagData); 
        $data['unreviewedSubmission'] = json_encode($unreviewedData);
        
        
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
        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('-1 months'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');

        $startDateYmd = date('Y-m-d', strtotime($startDate));
        $endDateYmd = date('Y-m-d', strtotime($endDate));

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                     );

        $referenceCode = (isset($inputs['referenceCode']))?$inputs['referenceCode']:0;
        $allPatients = User::where('type','patient')->lists('reference_code')->toArray();

        if(!$referenceCode)
        {
            $referenceCode = current($allPatients);
        }

        $patients[] = $referenceCode;
        $responseArr=[];
        
        $patientController = new PatientController();
        $responseByDate = [];
        $responseStatus = ["completed","late","missed"];
        $completedResponses = $missedResponses = $lateResponses = $patientSubmissions = $responseArr = [];
        $patientResponses = $patientController->getPatientsResponseByDate($patients,0,[],$startDateObj,$endDateObj,$responseStatus);
        foreach ($patientResponses as  $response) {
            $responseId = $response->getObjectId();
            $responseStatus = $response->get("status");

            $responseArr[$responseId]['DATE'] = $response->get("occurrenceDate")->format('d M');
            $responseArr[$responseId]['SUBMISSIONNO'] = $response->get("sequenceNumber");

            if ($responseStatus=='completed') {
                $completedResponses[]= $response;
                $patientSubmissions[] = $response;
            }
            elseif ($responseStatus=='late') {
                $lateResponses[]= $response;
                $patientSubmissions[] = $response;
            }
            elseif ($responseStatus=='missed') {
                $missedResponses[]= $response;
            }

            $occurrenceDate = $response->get("occurrenceDate")->format('d-m-Y h:i:s');
            $occurrenceDate = strtotime($occurrenceDate);
            $responseByDate[$occurrenceDate] = $responseId;
        } 

        ksort($responseByDate);
        $patientSubmissionsByDate = [];
        foreach ($responseByDate as $date => $responseId) {
            $patientSubmissionsByDate[$responseId] = $responseArr[$responseId];
        }

        $totalResponses = count($patientResponses);
        $responseRate['completedCount'] = count($completedResponses);
        $responseRate['missedCount'] = count($missedResponses);
        $responseRate['lateCount'] = count($lateResponses);

        $completed = ($totalResponses) ? (count($completedResponses)/$totalResponses) * 100 :0;
        $responseRate['completed'] =  round($completed,2);

        $missed = ($totalResponses) ? (count($missedResponses)/$totalResponses) * 100 :0;
        $responseRate['missed'] =  round($missed,2);

        $late = ($totalResponses) ? (count($lateResponses)/$totalResponses) * 100 :0;
        $responseRate['late'] =  round($late,2);  

        $baselineAnwers = $patientController->getPatientBaseLine($patient['reference_code']);
        $allBaselineAnwers = $patientController->getAllPatientBaseLine($patient['reference_code']);

        //get patient answers
        $patientAnswers = $patientController->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);
        

         //flags chart (total,red,amber,green)  
        $flagsCount = $patientController->patientFlagsCount($patientSubmissions,$baselineAnwers);    

        //health chart
        $healthChart = $this->healthChartData($patientAnswers);
        $submissionFlags = $healthChart['submissionFlags'];
        $flagsQuestions = $healthChart['questionLabel'];

        //question chart
        $questionsChartData = $this->getQuestionChartData($patientAnswers);
        $questionLabels = $questionsChartData['questionLabels'];
        $questionChartData = $questionsChartData['chartData'];

        $patientSubmissionChart = $patientController->getPatientSubmissionChart($patientAnswers,$allBaselineAnwers);


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
