<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\User;
use Chrisbjr\ApiGuard\Models\ApiKey;
use App\Hospital;
use App\Projects;
use App\PatientMedication;
use App\PatientClinicVisit;
use \Session;
use App\Http\Controllers\Project\ProjectController;
use \Input;

class PatientController extends Controller
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
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');

        $startDateYmd = date('Y-m-d', strtotime($startDate));
        $endDateYmd = date('Y-m-d', strtotime($endDate.'+1 day'));

        $patientsStatus ='';
        if(isset($inputs['patients']))
        {
            $patientsStatus = $inputs['patients'];
            $patients = User::where('type','patient')->where('account_status',$patientsStatus)->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->where('created_at','>=',$startDateYmd)->where('created_at','<=',$endDateYmd)->orderBy('created_at')->get()->toArray();
        }
        else
        {
            $patients = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->where('created_at','>=',$startDateYmd)->where('created_at','<=',$endDateYmd)->orderBy('created_at')->get()->toArray();
        }
        
         
        $activepatients = [];
        $patientReferenceCode = [];
        foreach ($patients as  $patient) {
            
            if($patient['account_status']=='active')
                $activepatients[]= $patient['reference_code'];
            
            $patientReferenceCode[] = $patient['reference_code'];
        }
        

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                     );

        $patientResponses = $this->patientsSummary($patientReferenceCode ,$startDateObj,$endDateObj);

        $patientsSummary = $patientResponses['patientResponses'];
        $completed = $patientResponses['completed']; 
        $late = $patientResponses['late']; 
        $missed = $patientResponses['missed']; 
        $completedCount = $patientResponses['completedCount'];
        $lateCount = $patientResponses['lateCount'];
        $missedCount = $patientResponses['missedCount'];
         
      

        return view('project.patients.list')->with('hospital', $hospital)
                                          ->with('logoUrl', $logoUrl)
                                          ->with('project', $project)
                                          ->with('active_menu', 'patients')
                                          ->with('activepatients', count($activepatients))
                                          ->with('patients', $patients)
                                          ->with('completed', $completed)
                                          ->with('late', $late)
                                          ->with('missed', $missed)
                                          ->with('completedCount', $completedCount)
                                          ->with('lateCount', $lateCount)
                                          ->with('missedCount', $missedCount)
                                          ->with('endDate', $endDate)
                                          ->with('startDate', $startDate)
                                          ->with('patientsStatus', $patientsStatus)
                                          ->with('patientsSummary', $patientsSummary);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        return view('project.patients.add')->with('active_menu', 'patients')
                                            ->with('hospital', $hospital)
                                            ->with('project', $project)
                                            ->with('logoUrl', $logoUrl);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);
        $hospital = $hospitalProjectData['hospital'];
        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $referenceCode = $request->input('reference_code');
        $hospital = $hospital['id'];//$request->input('hospital');
        $project = $projectId;
        $weight = $request->input('weight');
        $height = $request->input('height');
        $age = $request->input('age');
        $is_smoker = $request->input('is_smoker');
        $smoke_per_week = $request->input('smoke_per_week');
        $is_alcoholic = $request->input('is_alcoholic');
        $units_per_week = $request->input('units_per_week');

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
        $user->patient_weight = $weight;
        $user->age = $age;
        $user->patient_height = $height;
        $user->patient_is_smoker = $is_smoker;
        $user->patient_smoker_per_week = $smoke_per_week;
        $user->patient_is_alcoholic = $is_alcoholic;
        $user->patient_alcohol_units_per_week = $units_per_week;
        $user->save();
        $userId = $user->id;

        $medications = $request->input('medications');
        $patientMedication=[];

        if(!empty($medications))
        {
            foreach ($medications as   $medication) {
                if($medication == '')
                    continue;
                $patientMedication[]= new PatientMedication(['medication' => $medication]);
            }
        }

        $user->medications()->saveMany($patientMedication);

        $visitDate = $request->input('visit_date');
        $notes = $request->input('note');
        $patientVisits=[];

        if(!empty($visitDate))
        {
            foreach ($visitDate as $key=>  $visitDate) {
                if($visitDate == '')
                    continue;

                $visitDate = date('Y-m-d H:i:s' , strtotime($visitDate));
                $note = $notes[$key];
                $patientVisits[]= new PatientClinicVisit(['date_visited' => $visitDate,'note' => $note]);
            }
        }
        $user->clinicVisit()->saveMany($patientVisits);

        $apiKey                = new ApiKey;
        $apiKey->user_id       = $user->id;
        $apiKey->key           = $apiKey->generateKey();
        $apiKey->save();

 
        return redirect(url($hospitalSlug .'/'. $projectSlug .'/patients/' . $userId.'/edit')); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($hospitalSlug,$projectSlug , $patientId)
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

        $patient = User::find($patientId);

        // // get completed count
        // $responseQry = new ParseQuery("Response");
        // $responseQry->equalTo("status","completed");
        // $responseQry->equalTo("patient",$patient['reference_code']);
        // $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        // $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        // $responseRate['completedCount'] = $responseQry->count();

    
        //  // get missed count
        // $responseQry = new ParseQuery("Response");
        // $responseQry->equalTo("status","missed");
        // $responseQry->equalTo("patient",$patient['reference_code']);
        // $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        // $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        // $responseRate['missedCount'] = $responseQry->count();

        // $responseQry = new ParseQuery("Response");
        // $responseQry->equalTo("status","late");
        // $responseQry->equalTo("patient",$patient['reference_code']);
        // $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        // $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        // $responseRate['lateCount'] = $responseQry->count();

        $patients[] = $patient['reference_code'];
        $responseStatus = ["completed","late","missed"];
        $completedResponses = $missedResponses = $lateResponses = $patientSubmissions = $responseArr = [];
        $patientResponses = $this->getPatientsResponseByDate($patients,0,[],$startDateObj,$endDateObj,$responseStatus);
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

        $baselineAnwers = $this->getPatientBaseLine($patient['reference_code']);

        //get patient answers
        $patientAnswers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);
        
      
        //flags chart (total,red,amber,green)  
        $flagsCount = $this->patientFlagsCount($patientSubmissions,$baselineAnwers);

        //health chart
        $healthChart = $this->healthChartData($patientAnswers);
        $submissionFlags = $healthChart['submissionFlags'];
        $flagsQuestions = $healthChart['questionLabel'];
       

        //patient submissions
        $projectController = new ProjectController();
        $lastFiveSubmissions = array_slice($patientSubmissions, 0, 5, true);
        $submissionsSummary = $projectController->getSubmissionsSummary($lastFiveSubmissions); 

        //question chart
        $questionsChartData = $this->getQuestionChartData($patientAnswers);

        //patient submission flags
        $patientFlags =  $this->getsubmissionFlags($patientAnswers); 

        $questionLabels = $questionsChartData['questionLabels'];
        $questionChartData = $questionsChartData['chartData'];
        //$questionBaseLine = $questionsChartData['questionBaseLine'];
 
        return view('project.patients.show')->with('active_menu', 'patients')
                                        ->with('active_tab', 'summary')
                                        ->with('tab', '01')
                                        ->with('responseRate', $responseRate)
                                        ->with('submissionsSummary', $submissionsSummary)
                                        ->with('flagsCount', $flagsCount)
                                        ->with('hospital', $hospital)
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('questionChartData', $questionChartData)
                                        ->with('questionLabels', $questionLabels)
                                        //->with('questionBaseLine', $questionBaseLine)
                                        ->with('flagsQuestions', $flagsQuestions)
                                        ->with('responseArr', $responseArr)
                                        ->with('submissionFlags', $submissionFlags)
                                        ->with('patientFlags', $patientFlags)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('project', $project);
    }

    public function getSubmissionChart($patientAnswers,$missedResponses)
    {
        $baseLine = [];
        $patientResponses = [];
        $responseByDate = [];

        foreach ($patientAnswers as $answer)
        {
            $responseId = $answer->get("response")->getObjectId();
            $responseStatus = $answer->get("response")->get("status");
            $score = $answer->get("score");
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $occurrenceDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y');
            $occurrenceDate = strtotime($occurrenceDate);

            if($questionType!='single-choice')
                continue;

            if($responseStatus=="base_line")
            {  
                $baseLine[$responseId][] = $score;
                continue;
            }

            if($responseStatus!='completed')
                continue;

            

 
            $patientResponses[$occurrenceDate][$responseId] = $responseId;
            $responseByDate[$responseId][] = $score;
            

        }
        
        // foreach ($missedResponses as   $missedResponse) {
        //     $responseId = $missedResponse->getObjectId();
        //     $occurrenceDate = $missedResponse->get("occurrenceDate")->format('d-m-Y');
        //     $occurrenceDate = strtotime($occurrenceDate);
        //     $patientResponses[$occurrenceDate][$responseId] = $responseId;
        //     $responseByDate[$responseId][] = 0;
        // }
         
 
        $chartData = [];
        ksort($patientResponses);
        
        $i=0;
        foreach($patientResponses as $date => $responses)
        { 
            $date = date('d-m-Y',$date);
            

          foreach ($responses as $responseId) {
            $patientResponseScore = array_sum($responseByDate[$responseId]);
            $chartData[$i]['date'] = $date;
            $chartData[$i]['value'] = $patientResponseScore;
            $i++;
          }
                          
            
        }
         
            
        $data['baseLine']= (!empty($baseLine)) ? array_sum(current($baseLine)) :0;
        $data['chartData']=$chartData;
    
        return $data;
    }

    public function getPatientBaseLine($patient)
    {
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $patient); 
        $responseQry->equalTo("status", 'base_line'); 
        $response = $responseQry->first();  

        $answers =[];
        if(!empty($response))
        {
            $answersQry = new ParseQuery("Answer");
            $answersQry->equalTo("response",$response);
            $answersQry->exists("score");
            $answersQry->includeKey("question");
            $answersQry->includeKey("response");
            $answersQry->includeKey("option");
            $answers = $answersQry->find();
        }
        

        return $answers;
    }

    public function getAllPatientBaseLine($patient)
    {
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $patient); 
        $responseQry->equalTo("status", 'base_line'); 
        $responses = $responseQry->find();  

        $baseLineAnswers =[];

        
        foreach ($responses as $key => $response) {
            $responseId = $response->getObjectId();


            $answersQry = new ParseQuery("Answer");
            $answersQry->equalTo("response",$response);
            $answersQry->exists("score");
            $answersQry->includeKey("question");
            $answersQry->includeKey("response");
            $answersQry->includeKey("option");
            $answers = $answersQry->find();

            $baseLineAnswers[$responseId]= $answers;

        }

        
 
        return $baseLineAnswers;
    }

 
    public function patientFlagsCount($patientResponses,$baselineAnwers)
    {

        $redFlagsByDate = [];
        $amberFlagsByDate = [];
        $greenFlagsByDate = [];
        $totalFlagsByDate = [];
        $baseLineByDate = [];
        $baseLineData = [];
       
        foreach ($patientResponses as $response) {
            $patient = $response->get("patient");
            $baseLineFlag = $response->get("baseLineFlag");
            $previousFlag = $response->get("previousFlag");
            $score = $response->get("score");
            $totalScore = $response->get("totalScore");
            $comparedToBaseLine = $response->get("comparedToBaseLine");


            $baseLineTotalRedFlags = $response->get("baseLineTotalRedFlags");
            $baseLineTotalAmberFlags = $response->get("baseLineTotalAmberFlags");
            $baseLineTotalGreenFlags = $response->get("baseLineTotalGreenFlags");
            $previousTotalRedFlags = $response->get("previousTotalRedFlags");
            $previousTotalAmberFlags = $response->get("previousTotalAmberFlags");
            $previousTotalGreenFlags = $response->get("previousTotalGreenFlags");

            $occurrenceDate = $response->get("occurrenceDate")->format('d-m-Y h:i:s');
            $occurrenceDate = strtotime($occurrenceDate);
             
           
            $redFlagsByDate[$occurrenceDate]['baseLine']=$baseLineTotalRedFlags;
            $amberFlagsByDate[$occurrenceDate]['baseLine']=$baseLineTotalAmberFlags;
            $greenFlagsByDate[$occurrenceDate]['baseLine']=$baseLineTotalGreenFlags;
            $redFlagsByDate[$occurrenceDate]['previous']=$previousTotalRedFlags;
            $amberFlagsByDate[$occurrenceDate]['previous']=$previousTotalAmberFlags;
            $greenFlagsByDate[$occurrenceDate]['previous']=$previousTotalGreenFlags;

            $totalFlagsByDate[$occurrenceDate] = $totalScore;
            $baseLineByDate[$occurrenceDate] = $totalScore + $comparedToBaseLine;
            
        }

        
        foreach($baselineAnwers as $answer)
        {
            $responseId = $answer->get("response")->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $score = $answer->get("score");
            $baseLineData[$questionId] = $score;
            
        }

        $redFlagData = [];
        $amberFlagData = [];
        $greenFlagData = [];
        $totalFlagData = [];

        // $baslineFlagData = [];
        // $previousFlagData = [];

        ksort($redFlagsByDate);
        $i=0;
        foreach($redFlagsByDate as $date => $value)
        { 
            $redFlagData[$i]["Date"] = date('d M',$date);
            $redFlagData[$i]["Baseline"] = $value['baseLine'];
            $redFlagData[$i]["Previous"] = $value['previous'] ;
 
            $i++;
        }

        ksort($amberFlagsByDate);
        $i=0;
        foreach($amberFlagsByDate as $date => $value)
        { 
            $amberFlagData[$i]["Date"] =  date('d M',$date);
            $amberFlagData[$i]["Baseline"] = $value['baseLine'];
            $amberFlagData[$i]["Previous"] = $value['previous'] ;
 
            $i++;
        }

        ksort($greenFlagsByDate);
        $i=0;
        foreach($greenFlagsByDate as $date => $value)
        { 
            $greenFlagData[$i]["Date"] =  date('d M',$date);
            $greenFlagData[$i]["Baseline"] = $value['baseLine'];
            $greenFlagData[$i]["Previous"] = $value['previous'] ;
 
            $i++;
        }


        ksort($totalFlagsByDate);
        $i=0;
        foreach($totalFlagsByDate as $date => $value)
        { 
            $baseLineScore =  $baseLineByDate[$date];
            $totalFlagData[$i]["Date"] =  date('d M',$date);
            $totalFlagData[$i]["score"] = $value;
            $totalFlagData[$i]["baseLine"] = $baseLineScore;
 
            $i++;
        }
       
     
        $data['redFlags'] = json_encode($redFlagData);
        $data['amberFlags'] = json_encode($amberFlagData);
        $data['greenFlags'] = json_encode($greenFlagData); 
        $data['totalFlags'] = json_encode($totalFlagData);
        $data['baslineScore'] = array_sum($baseLineData);
        
        return $data;
    }

    public function getsubmissionFlags($projectAnwers,$filterType='')
    {
        $answersList = [];
        $submissionFlags = [];
        $responseFlags = [];
        $patientsallFlags=[];
        $patientsFlags = [];
        $patientsFlags['red'] = [];
        $patientsFlags['amber'] = [];
        $patientsFlags['green'] = [];
        $questionsTypes = ['single-choice','input']; 
 
        foreach ($projectAnwers as $answer)
        {
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");

            $responseId = $answer->get("response")->getObjectId();
            $answerId = $answer->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $questionLabel = $answer->get("question")->get("title");
            $patient = $answer->get("patient");

            $responseBaseLineFlag = $answer->get("response")->get("baseLineFlag");
            $responsePreviousFlag = $answer->get("response")->get("previousFlag");

            $responseStatus = $answer->get("response")->get("status");

             $sequenceNumber = $answer->get("response")->get("sequenceNumber");
             $occurrenceDate = $answer->get("response")->get("occurrenceDate")->format('d M');

             if($responseStatus=='base_line' || $responseStatus=='missed' || $responseStatus=='started')
                continue;

             if($questionType!='single-choice')
                continue;

            if(!isset($answersList[$answerId]))                         //avoid duplication
                $answersList[$answerId] = ['base'=>$baseLineFlag,'prev'=>$previousFlag];
            else
                continue;

            if(!isset($responseFlags[$responseId]))
            {
                $responseFlags[$responseId]=$responseId;

                if($filterType=='baseline' || $filterType=='')
                {
                    $patientsallFlags[]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line total score set for questionnaire', 'flag'=>$responseBaseLineFlag, 'date'=>$occurrenceDate];

                    $patientsFlags[$responseBaseLineFlag][]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line total score set for questionnaire', 'flag'=>$responseBaseLineFlag, 'date'=>$occurrenceDate];
                }
                
                if($filterType=='previous' || $filterType=='')
                { 
                    $patientsallFlags[]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous total score questionnaire', 'flag'=>$responsePreviousFlag, 'date'=>$occurrenceDate];

                    $patientsFlags[$responsePreviousFlag][]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous total score questionnaire', 'flag'=>$responsePreviousFlag, 'date'=>$occurrenceDate];
                }
            }

            if($filterType=='baseline' || $filterType=='')
            {
                $patientsallFlags[]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line score of '.$questionLabel, 'flag'=>$baseLineFlag, 'date'=>$occurrenceDate];

                $patientsFlags[$baseLineFlag][]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line score of '.$questionLabel, 'flag'=>$baseLineFlag, 'date'=>$occurrenceDate];
            }  
                
 
            if($filterType=='previous' || $filterType=='')
            {
                $patientsallFlags[]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous score of '.$questionLabel, 'flag'=>$previousFlag, 'date'=>$occurrenceDate, 'answerId'=>$answerId];

                $patientsFlags[$previousFlag][]= ['patient'=>$patient,'sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous score of '.$questionLabel, 'flag'=>$previousFlag, 'date'=>$occurrenceDate];
            } 
                

            
        }
        //dd($patientsallFlags); 
        $submissionFlags['all'] = $patientsallFlags;
        $submissionFlags['flags'] =$patientsFlags;



        return $submissionFlags;
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($hospitalSlug,$projectSlug,$patientId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];

        $patient = User::find($patientId);
        $patientStatus = $patient->account_status;

        $disabled = '';
        if($patientStatus=='active')
            $disabled = 'disabled';

        $patientMedications = $patient->medications()->get()->toArray();
        $patientvisits = $patient->clinicVisit()->get()->toArray();
        
        return view('project.patients.edit')->with('active_menu', 'patients')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient->toArray())
                                        ->with('disabled', $disabled)
                                        ->with('patientvisits', $patientvisits)
                                        ->with('patientMedications', $patientMedications)
                                        ->with('project', $project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$hospitalSlug,$projectSlug, $id)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $referenceCode = $request->input('reference_code');
        $hospital = $hospital['id'];//$request->input('hospital');
        $project = $projectId;
        $weight = $request->input('weight');
        $height = $request->input('height');
        $age = $request->input('age');
        $is_smoker = $request->input('is_smoker');
        $smoke_per_week = $request->input('smoke_per_week');
        $is_alcoholic = $request->input('is_alcoholic');
        $units_per_week = $request->input('units_per_week');
        
        $user = User::find($id);
        if($user->account_status=='created')
        {
           $user->reference_code = $referenceCode;
           $user->project_id = $project; 
        }
        
        $user->patient_weight = $weight;
        $user->age = $age;
        $user->patient_height = $height;
        $user->patient_is_smoker = $is_smoker;
        $user->patient_smoker_per_week = $smoke_per_week;
        $user->patient_is_alcoholic = $is_alcoholic;
        $user->patient_alcohol_units_per_week = $units_per_week;
        $user->save();

        $medications = $request->input('medications');
        $patientMedication=[];

        if(!empty($medications))
        {
            $user->medications()->delete();
            foreach ($medications as   $medication) {
                if($medication == '')
                    continue;

                $patientMedication[]= new PatientMedication(['medication' => $medication]);
            }
        }

        $user->medications()->saveMany($patientMedication);

        $visitDate = $request->input('visit_date');
        $notes = $request->input('note');
        $patientVisits=[];

        if(!empty($visitDate))
        {
            $user->clinicVisit()->delete();
            foreach ($visitDate as $key=>  $visitDate) {
                if($visitDate == '')
                    continue;
                
                $visitDate = date('Y-m-d H:i:s' , strtotime($visitDate));
                $note = $notes[$key];
                $patientVisits[]= new PatientClinicVisit(['date_visited' => $visitDate,'note' => $note]);
            }
        }
        $user->clinicVisit()->saveMany($patientVisits);


        return redirect(url($hospitalSlug .'/'. $projectSlug .'/patients/' . $userId.'/edit')); 
    }

    public function getPatientSubmission($hospitalSlug,$projectSlug ,$patientId)
    { 
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $submissionStatus = '';

        $patient = User::find($patientId)->toArray();

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

        
        // $responseStatus = ["completed"];
        // $patientAnwers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);

        $patients[] = $patient['reference_code'];
        
        if(isset($inputs['submissionStatus']))
        {
            $responseStatus = [$inputs['submissionStatus']];
            $submissionStatus = $inputs['submissionStatus'];
        }
        else
        {
            $responseStatus = ["completed","late","missed"];
        }

        $patientResponses = $this->getPatientsResponseByDate($patients,0,[],$startDateObj,$endDateObj,$responseStatus); 
        //dd($patientResponses);
        $projectController = new ProjectController();
        $submissionsSummary = $projectController->getSubmissionsSummary($patientResponses); 

        return view('project.patients.submissions')->with('active_menu', 'patients')
                                                ->with('active_tab', 'submissions')
                                                ->with('tab', '02')
                                                ->with('patient', $patient)
                                                ->with('hospital', $hospital)
                                                 ->with('project', $project)
                                                 ->with('logoUrl', $logoUrl)
                                                 ->with('endDate', $endDate)
                                                 ->with('startDate', $startDate)
                                                 ->with('submissionStatus', $submissionStatus)
                                                 ->with('submissionsSummary', $submissionsSummary);
    }

    public function getPatientFlags($hospitalSlug,$projectSlug ,$patientId)
    { 
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $patient = User::find($patientId)->toArray();

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

        $filterType = (isset($inputs['type']))?$inputs['type']:'';

        $responseStatus = ["completed","late"];
        $patientAnwers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);

        $submissionFlags =  $this->getsubmissionFlags($patientAnwers,$filterType); 

        return view('project.patients.flags')->with('active_menu', 'patients')
                                                ->with('active_tab', 'flags')
                                                ->with('tab', '03')
                                                ->with('patient', $patient)
                                                ->with('hospital', $hospital)
                                                ->with('project', $project)
                                                ->with('logoUrl', $logoUrl)
                                                ->with('endDate', $endDate)
                                                ->with('startDate', $startDate)
                                                ->with('filterType', $filterType)
                                                ->with('submissionFlags', $submissionFlags);
    }

    public function getpatientBaseLines($hospitalSlug ,$projectSlug ,$patientId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];
        
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $referenceCode); 
        $responseQry->equalTo("status", 'base_line'); 
        $responseQry->ascending("createdAt");
        $responses = $responseQry->find();

        $baseLines = [];

        foreach ($responses as  $response) {
            $responseId = $response->getObjectId();
            $sequenceNumber = $response->get('sequenceNumber');
            $date = $response->getCreatedAt()->format('d-m-Y');
            $baseLines[$responseId] = ['sequenceNumber'=>$sequenceNumber,'date'=>$date];
        }

        $questionnaireQry = new ParseQuery("Questionnaire");
        $questionnaireQry->equalTo("project", $projectId);
        $isQuestionnaireSet = $questionnaireQry->count();  
      

        return view('project.patients.baseline-list')->with('active_menu', 'patients')
                                                ->with('active_tab', 'base_line')
                                                ->with('tab', '03')                            
                                                ->with('hospital', $hospital)
                                                ->with('project', $project) 
                                                ->with('patient', $patient) 
                                                ->with('isQuestionnaireSet', $isQuestionnaireSet) 
                                                ->with('baseLines', $baseLines); 
    }

    
    public function showpatientBaseLineScore($hospitalSlug ,$projectSlug,$patientId,$responseId)
    {
 
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];

        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];
        $projectId = $patient['project_id'];
        $projectId = intval ($projectId);
        $projectName = Projects::find($projectId)->name; 

        $baseLineData = $this->getBaseLineData($projectId,$referenceCode,$responseId,true);
        $questionnaireName = $baseLineData['questionnaireName']; 
        $questionsList = $baseLineData['questionsList']; 
        $optionsList = $baseLineData['optionsList']; 
        $answersList = $baseLineData['answersList']; 
         
        
        return view('project.patients.baselinescore')->with('active_menu', 'patients')
                                        ->with('active_tab', 'base_line')
                                        ->with('tab', '03')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('projectName', $projectName)
                                        ->with('questionnaire', $questionnaireName)
                                        ->with('questionsList', $questionsList)
                                        ->with('optionsList', $optionsList)
                                        ->with('answersList', $answersList);
    }

    public function patientsSummary($patients,$startDate,$endDate)
    {
        $scheduleQry = new ParseQuery("Schedule");
        $scheduleQry->containedIn("patient",$patients);
        $schedules = $scheduleQry->find();

        $patientNextOccurrence = [];
        $patientResponses = [];
        $patientData = [];

        $completedResponses = [];
        $lateResponses = [];
        $missedResponses = [];

         foreach ($patients as $patient) {
            $missedCount = 0;
            $responseQry = new ParseQuery("Response");
            $responseQry->equalTo("patient", $patient); 
            $responseQry->equalTo("status", 'missed'); 
            $missedCount = $responseQry->count();

            //
            $patientResponses[$patient]['lastSubmission'] = '-' ;
            $patientResponses[$patient]['nextSubmission'] = '-';
            $patientResponses[$patient]['missed'] =[];
            $patientResponses[$patient]['late'] =[];
            $patientResponses[$patient]['completed'] =[];

            $patientResponses[$patient]['baseLineFlag']['red'] =0;
            $patientResponses[$patient]['baseLineFlag']['green'] =0;
            $patientResponses[$patient]['baseLineFlag']['amber'] =0;

            $patientResponses[$patient]['previousFlag']['red'] =0;
            $patientResponses[$patient]['previousFlag']['green'] =0;
            $patientResponses[$patient]['previousFlag']['amber'] =0;

            $patientResponses[$patient]['missed']=$missedCount;
            $missedResponses[] = $missedCount;
        }

        foreach($schedules as $schedule)
        {
            $patientId = $schedule->get("patient");
            $nextOccurrence = $schedule->get("nextOccurrence")->format('dS M');
            $patientResponses[$patientId]['nextSubmission'] = $nextOccurrence;

        }

        $responseStatus = ["completed","late"]; 
        $responses = $this->getPatientsResponseByDate($patients,0,[] ,$startDate,$endDate,$responseStatus);  
         
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

            if($status=='late')
            {
                $patientResponses[$patient]['late'][]=$responseId;
                $lateResponses[]=$response;
                
            }
            elseif($status=='completed')
            {
                $patientResponses[$patient]['completed'][]=$responseId;
                $completedResponses[]=$response;
                
            }
        
   
            $baseLineTotalRedFlags = $response->get("baseLineTotalRedFlags");
            $baseLineTotalAmberFlags = $response->get("baseLineTotalAmberFlags");
            $baseLineTotalGreenFlags = $response->get("baseLineTotalGreenFlags");
            $previousTotalRedFlags = $response->get("previousTotalRedFlags");
            $previousTotalAmberFlags = $response->get("previousTotalAmberFlags");
            $previousTotalGreenFlags = $response->get("previousTotalGreenFlags");
 
            $patientResponses[$patient]['baseLineFlag']['red'] +=$baseLineTotalRedFlags;
            $patientResponses[$patient]['baseLineFlag']['amber'] +=$baseLineTotalAmberFlags;
            $patientResponses[$patient]['baseLineFlag']['green'] +=$baseLineTotalGreenFlags;

            $patientResponses[$patient]['previousFlag']['red'] +=$previousTotalRedFlags;
            $patientResponses[$patient]['previousFlag']['amber'] +=$previousTotalAmberFlags;
            $patientResponses[$patient]['previousFlag']['green'] +=$previousTotalGreenFlags;

 
        }

        
       
        $totalResponses = count($responses) + array_sum($missedResponses);

        $completed = ($totalResponses) ? (count($completedResponses)/$totalResponses) * 100 :0;
        $completed =  round($completed,2);

        $late = ($totalResponses) ? (count($lateResponses)/$totalResponses) * 100 :0;
        $late =  round($late,2);

        $missed = ($totalResponses) ? (array_sum($missedResponses)/$totalResponses) * 100 :0;
        $missed =  round($missed,2);
         
 
        $data['patientResponses']=$patientResponses;
        $data['completed']=$completed; 
        $data['late']=$late; 
        $data['missed']=$missed; 
        $data['completedCount']=count($completedResponses);
        $data['lateCount']=count($lateResponses);
        $data['missedCount']=array_sum($missedResponses);
        $data['totalResponses']=$totalResponses;
         
        return $data;
        
    }

    public function getPatientsResponseByDate($patients,$page=0,$responseData,$startDate,$endDate,$status)  
    {
        $displayLimit = 90; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",$status);  //["completed","late","missed"]
        $responseQry->containedIn("patient",$patients);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->skip($page * $displayLimit);
        $responseQry->limit($displayLimit);
        $responseQry->descending("occurrenceDate");
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 

        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getPatientsResponseByDate($patients,$page,$responseData,$startDate,$endDate,$status);  
        }  
        
        return $responseData;
     
    }


    public function getPatientsResponses($patients,$page=0,$responseData,$status)  
    {
        $displayLimit = 90; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",$status);  //["completed","late","missed"]
        $responseQry->containedIn("patient",$patients);
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responseQry->descending("occurrenceDate");
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 

        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getPatientsResponses($patients,$page,$responseData,$status);  
        }  
        
        return $responseData;
     
    }

    public function getPatientAnwers($patient,$projectId,$page=0,$anwsersData)
    {
        $displayLimit = 90; 

        $answersQry = new ParseQuery("Answer");
        //$answersQry->equalTo("project",$projectId);
        $answersQry->equalTo("patient",$patient);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->includeKey("option");
        $answersQry->limit($displayLimit);
        $answersQry->skip($page * $displayLimit);
        $answersQry->ascending("occurrenceDate");
 
        $anwsers = $answersQry->find();
        $anwsersData = array_merge($anwsers,$anwsersData); 

        if(!empty($anwsers))
        {
            $page++;
            $anwsersData = $this->getPatientAnwers($patient,$projectId,$page,$anwsersData);
        }  
        
        return $anwsersData;
     
    }


    public function getPatientAnwersByDate($patient,$projectId,$page=0,$anwsersData,$startDate,$endDate)
    {
        $displayLimit = 90; 

        $answersQry = new ParseQuery("Answer");
        //$answersQry->equalTo("project",$projectId);
        $answersQry->equalTo("patient",$patient);
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->includeKey("option");
        $answersQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $answersQry->lessThanOrEqualTo("occurrenceDate",$endDate);
        $answersQry->limit($displayLimit);
        $answersQry->skip($page * $displayLimit);
        $answersQry->descending("occurrenceDate");
 
        $anwsers = $answersQry->find();
        $anwsersData = array_merge($anwsers,$anwsersData); 

        if(!empty($anwsers))
        {
            $page++;
            $anwsersData = $this->getPatientAnwers($patient,$projectId,$page,$anwsersData,$startDate,$endDate);
        }  
        
        return $anwsersData;
     
    }

    public function getBaseLineData($projectId,$referenceCode,$responseId,$flag)
    {
        $questionnaireQry = new ParseQuery("Questionnaire");
        $questionnaireQry->equalTo("project", $projectId);
        $questionnaire = $questionnaireQry->first();  

        $questions =[];
        $options =[];
        $questionnaireName = '';
        $questionnaireId = '';

        if(!empty($questionnaire))
        {
            $questionnaireName = $questionnaire->get('name');
            $questionnaireId = $questionnaire->getObjectId();

            $questionQry = new ParseQuery("Questions");
            $questionQry->equalTo("questionnaire", $questionnaire);
            $questions = $questionQry->find(); 

            $optionsQry = new ParseQuery("Options");
            $optionsQry->containedIn("question", $questions);
            $options = $optionsQry->find(); 

            $responseQry = new ParseQuery("Response");
            if($flag)
                $responseQry->equalTo("objectId", $responseId); 
            $responseQry->equalTo("patient", $referenceCode); 
            $responseQry->equalTo("status", 'base_line'); 
            $responseQry->descending("createdAt");
            $response = $responseQry->first();
        }
         
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

     public function getpatientBaseLineScore($hospitalSlug ,$projectSlug ,$patientId)
    {
 
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);


        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];



        $baseLineData = $this->getBaseLineData($projectId,$referenceCode,'',false);
        $questionnaireName = $baseLineData['questionnaireName']; 
        $questionnaireId = $baseLineData['questionnaireId']; 
        $questionsList = $baseLineData['questionsList'];  
        $optionsList = $baseLineData['optionsList']; 
        $answersList = $baseLineData['answersList']; 
        //$baseLineResponseId = $baseLineData['baseLineResponseId'];  
        
        return view('project.patients.baselinescore-edit')->with('active_menu', 'patients')
                                        ->with('active_tab', 'base_line')
                                        ->with('tab', '03')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        
                                        ->with('questionnaireId', $questionnaireId)
                                        ->with('questionnaire', $questionnaireName)
                                        ->with('questionsList', $questionsList)
                                        ->with('optionsList', $optionsList)
                                        ->with('answersList', $answersList);
    }

    public function setPatientBaseLineScore(Request $request, $hospitalSlug ,$projectSlug ,$id)
    {
        
        // $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        // $hospital = $hospitalProjectData['hospital'];
        // $project = $hospitalProjectData['project'];
        // $projectId = intval($project['id']);

        $baseLineAnswers = $request->all(); //dd($baseLineAnswers);
        $questions = $baseLineAnswers['question'];
        $questionType = $baseLineAnswers['questionType'];
        // $baseLineResponseId = $baseLineAnswers['baseLineResponseId'];
        $questionnaireId = $baseLineAnswers['questionnaireId'];
        $patientId = $baseLineAnswers['patientId'];

        $patient = User::find($patientId)->toArray();
        $referenceCode = $patient['reference_code'];
        $projectId = $patient['project_id'];
        $projectId = intval ($projectId);

        // if($baseLineResponseId =='')
        // {
                // }
        // else
        // {
        //     $responseObj = new ParseQuery("Response");
        //     $response = $responseObj->get($baseLineResponseId);

        //     $answers = new ParseQuery("Answer");
        //     $answers->equalTo("response", $response);
        //     $answersObjs = $answers->find();
        //     // ParseObject::destroyAll($answersObjs);
        //     foreach ($answersObjs as $answer) {
        //         $answer->destroy();
        //     }
        // }

        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("patient", $referenceCode); 
        $responseQry->equalTo("status", 'base_line'); 
        $responseQry->descending("createdAt");
        $responses = $responseQry->first();

        $sequenceNumber = (empty($responses))? 1 :($responses->get('sequenceNumber') + 1);


        $questionnaireObj = new ParseQuery("Questionnaire");
        $questionnaire = $questionnaireObj->get($questionnaireId);

        $date = new \DateTime();
        //add
        $response = new ParseObject("Response");
        $response->set("questionnaire", $questionnaire);
        $response->set("patient", $referenceCode);
        $response->set("sequenceNumber", $sequenceNumber);
        $response->set("project", $projectId);
        $response->set("occurrenceDate", $date);
        $response->set("status", 'base_line');
        $response->save();
        $responseId = $response->getObjectId();



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
        

        return redirect(url($hospitalSlug .'/'.$projectSlug. '/patients/' . $id . '/base-line-score/'.$responseId)); 
         
    }


    public function getPatientReports($hospitalSlug ,$projectSlug ,$patientId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $patient = User::find($patientId)->toArray();

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


        $patients[] = $patient['reference_code'];
        $responseArr=[];
        $patientSubmissions=[];
        
        $responseStatus = ["completed","late","missed"]; 
        $responses = $this->getPatientsResponseByDate($patients,0,[],$startDateObj,$endDateObj,$responseStatus);
        foreach ($responses as  $response) {
            $responseId = $response->getObjectId();
            $responseArr[$responseId]['DATE'] = $response->get("occurrenceDate")->format('d M');
            $responseArr[$responseId]['SUBMISSIONNO'] = $response->get("sequenceNumber");
            
        }

        $answers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);

        $healthChart = $this->healthChartData($answers);
        $submissionArr = $healthChart['submissionFlags'];
        $questionArr = $healthChart['questionLabel'];

        // $baselineAnwers = $this->getPatientBaseLine($patient['reference_code']);
        $allBaselineAnwers = $this->getAllPatientBaseLine($patient['reference_code']);
        
        $questionsChartData = $this->getQuestionChartData($answers);

        $questionLabels = $questionsChartData['questionLabels'];
        $questionChartData = $questionsChartData['chartData'];
        // $questionBaseLine = $questionsChartData['questionBaseLine'];

        $patientSubmissionChart = $this->getPatientSubmissionChart($answers,$allBaselineAnwers);
        $submissionChart = $patientSubmissionChart['submissionChart'] ;
        $submissionNumbers = $patientSubmissionChart['submissions'] ;
        $firstSubmission = (!empty($submissionNumbers)) ? current($submissionNumbers) :'';
       
     
        return view('project.patients.reports')->with('active_menu', 'patients')
                                        ->with('active_tab', 'reports')
                                        ->with('tab', '04')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('responseArr', $responseArr)
                                        ->with('questionArr', $questionArr)
                                        // ->with('questionBaseLine', $questionBaseLine)
                                        ->with('submissionArr', $submissionArr)                    
                                        ->with('questionLabels', $questionLabels)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('firstSubmission', $firstSubmission)
                                        ->with('submissionChart', $submissionChart)
                                        ->with('submissionNumbers', $submissionNumbers)
                                        ->with('questionChartData', $questionChartData); 
    }

    public function healthChartData($answers)
    {
        $questionArr =[];
        $submissionArr=[];
        foreach ($answers as   $answer) {
            $responseStatus = $answer->get("response")->get("status");
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $questionTitle = $answer->get("question")->get("title");
            $responseId = $answer->get("response")->getObjectId();

            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y h:i:s');
            $answerDate = strtotime($answerDate);

            
            if($responseStatus=='missed' || $responseStatus=='started'|| $responseStatus=='base_line')
                continue;


           if ($questionType=='single-choice')  
            { 
               $submissionArr[$responseId][$questionId]['baslineFlag'] = $baseLineFlag ;
               $submissionArr[$responseId][$questionId]['previousFlag'] = $previousFlag ;

               $questionArr[$questionId]= $questionTitle;
            } 
            
            
             
        }

        $data['questionLabel']=$questionArr;
        $data['submissionFlags']=$submissionArr;

        return $data;
 
    }

    public function getQuestionChartData($patientAnswers)
    {
        $questionLabels = [];
        $baseLineArr= []; 
        $chartData = [];
        $inputScores = [];
        $allScore =[];
        $submissionArr =[];
        $singleChoiceQuestion = [];

        foreach ($patientAnswers as   $answer) {
            $responseStatus = $answer->get("response")->get("status");
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $questionTitle = $answer->get("question")->get("title");
            $responseId = $answer->get("response")->getObjectId();
            $optionScore = ($questionType=='multi-choice' || $questionType=='single-choice') ? $answer->get("option")->get("score"):0;
            $optionValue = $answer->get("value");
            $comparedToBaseLine = $answer->get("comparedToBaseLine");
            $baseLineScore = $optionScore + $comparedToBaseLine;

            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y h:i:s');
            $answerDate = strtotime($answerDate);
            $questionLabel =  ucfirst(strtolower($questionTitle));

            
            if($responseStatus=='missed' || $responseStatus=='started' || $responseStatus=='base_line')
                continue;

            if($questionType=='descriptive')
                continue;
           
            if($questionType=='input')
            { 
                $inputBaseQuestionId = $questionId;
                

                $questionLabels[$questionId] = $questionLabel;
                $allScore[$questionId][] = $optionValue;

                // if($responseStatus=="base_line")
                //     $baseLineArr[$questionId] =$optionValue;
                // else
                $baseLineArr[$questionId][$answerDate] =$baseLineScore;
                $inputScores[$questionId][$answerDate] = $optionValue ;

                continue;
            }
            elseif ($questionType=='multi-choice') {        //if multichoise sum up scores

                continue;
 
            } 
            elseif ($questionType=='single-choice')  
            {
                $questionLabels[$questionId] = $questionLabel;
                $singleChoiceQuestion[$questionId] = $questionLabel;

                // if($responseStatus=="base_line")
                //    $baseLineArr[$questionId] =$optionScore;
                // else
                // {
               $baseLineArr[$questionId][$answerDate] =$baseLineScore;
               $inputScores[$questionId][$answerDate] = $optionScore ;
               $submissionArr[$responseId][$questionId]['baslineFlag'] = $baseLineFlag ;
               $submissionArr[$responseId][$questionId]['previousFlag'] = $previousFlag ;
                // }

             } 
            
        }
        
        
        foreach ($inputScores as $questionId => $data) {
            ksort($data);
            $i=0;
            foreach($data as $date => $value)
            { 
                $baslineScore = $baseLineArr[$questionId][$date];
                $chartData[$questionId][$i]['Date'] = date('d M',$date);
                $chartData[$questionId][$i]['score'] = intval($value);
                $chartData[$questionId][$i]['baseLine'] = intval($baslineScore);
               
                $i++;
            }
            
            
        }
        
        // $questiondata['questionBaseLine']=$baseLineArr;
        $questiondata['chartData']=$chartData;
        $questiondata['questionLabels']=$questionLabels;
        $questiondata['submissions']=$submissionArr;
        $questiondata['singleChoiceQuestion']=$singleChoiceQuestion;
        
       
        return $questiondata;
    }

    public function getPatientSubmissionChart($patientAnwers,$allBaselineAnwers)
    {
         
        $chartData =[];
        $submissions =[];
        $responseIds =[];

        foreach($patientAnwers as $answer)
        {  
           $question =  $answer->get("question");
           $questionId =  $question->getObjectId();
           $questionType =  $question->get("type");
           $responseId = $answer->get("response")->getObjectId();
           $sequenceNumber = $answer->get("response")->get("sequenceNumber");
           

           $responseStatus = $answer->get("response")->get("status");
           if($responseStatus=='missed' || $responseStatus=='started' || $responseStatus=='base_line')
                continue;

           $baseLine = $answer->get("response")->get("baseLine")->getObjectId();
           $preSubmissionId = (!is_null($answer->get("response")->get("previousSubmission")))?$answer->get("response")->get("previousSubmission")->getObjectId():'';

            $submissions[$sequenceNumber] = $responseId;
            $responseIds[$responseId]['BaseLine'] = $baseLine;
            $responseIds[$responseId]['Previous'] = $preSubmissionId;

            if($questionType == 'single-choice')
                $chartData[$responseId][$answer->get("question")->getObjectId()] =['question'=>$answer->get("question")->get("title"),'score'=>$answer->get("score")];
            elseif($questionType == 'input')
                $chartData[$responseId][$answer->get("question")->getObjectId()] =['question'=>$answer->get("question")->get("title"),'score'=>$answer->get("value")];
           
        }
        //baseline
        $allBaseChartData = $this->getBaseLineChartData($allBaselineAnwers);

        ksort($submissions);
         
        // echo '<pre>';
        // print_r($submissions);
        // echo '</pre>';
        $submissionChart = [];
        $previousRecord = [];

        $i=0;
        foreach ($submissions as $sequenceNumber => $responseId) {
            if(!isset($chartData[$responseId]))
            {
                $submissionChart[$responseId]=[];
                continue;
            }

            $currentChartData = $chartData[$responseId];
            $previousResponseId = $responseIds[$responseId]['Previous'];
            $previousChartData = (isset($chartData[$previousResponseId]))?$chartData[$previousResponseId]:[];
            $baseLineId = $responseIds[$responseId]['BaseLine'];
            // $submissionChart [$responseId] = $previousResponseId;

            $baseChartData = $allBaseChartData[$baseLineId];

            foreach ($baseChartData as $questionId => $data) {
                $currentScore = (isset($currentChartData[$questionId]['score']))?$currentChartData[$questionId]['score']:0;
                $baseScore = $data['score'];
                $previousScore = (isset($previousChartData[$questionId]['score']))?$previousChartData[$questionId]['score']:0;
                $question = $data['question'];
                $submissionChart[$responseId][] =["question"=> $question,"base"=> $baseScore,"prev"=> $previousScore,"current"=> $currentScore];
                 
            }
             $i++;
        }
       

       $result['submissionChart'] = $submissionChart;
       $result['submissions'] = $submissions;

       return $result;

    }

    public function getBaseLineChartData($baselineAnwers)
    {
        $chartData =[];
        
        foreach($baselineAnwers as $responseId=> $answers)
        { 
            foreach($answers as  $answer)
            {  
               $question =  $answer->get("question");
               $questionId =  $question->getObjectId();
               $questionType =  $question->get("type");
               $responseId = $answer->get("response")->getObjectId();
               $sequenceNumber = $answer->get("response")->get("sequenceNumber");
               $baseLine = $answer->get("response")->get("baseLine");

               $responseStatus = $answer->get("response")->get("status");
               // if($responseStatus=='missed' || $responseStatus=='started')
               //      continue;
                $submissions[$sequenceNumber] = $responseId;
                $responseBaseLine[$responseId] = $baseLine;

                if($questionType == 'single-choice')
                    $chartData[$responseId][$answer->get("question")->getObjectId()] =['question'=>$answer->get("question")->get("title"),'score'=>$answer->get("score")];
                elseif($questionType == 'input')
                    $chartData[$responseId][$answer->get("question")->getObjectId()] =['question'=>$answer->get("question")->get("title"),'score'=>$answer->get("value")];
            }
           
        }

        return $chartData;
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
