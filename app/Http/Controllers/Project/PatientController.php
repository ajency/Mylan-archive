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
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));

        $startDateYmd = date('Y-m-d', strtotime($startDate));
        $endDateYmd = date('Y-m-d', strtotime($endDate));

        $patients = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->where('created_at','>=',$startDateYmd)->where('created_at','<=',$endDateYmd)->orderBy('created_at')->get()->toArray();
         
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
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        $patientResponses = $this->patientSummary($patientReferenceCode ,$startDateObj,$endDateObj);

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
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));

        $startDateYmd = date('Y-m-d', strtotime($startDate));
        $endDateYmd = date('Y-m-d', strtotime($endDate));

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );

        $patient = User::find($patientId);

        // get completed count
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","completed");
        $responseQry->equalTo("patient",$patient['reference_code']);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        $responseRate['completedCount'] = $responseQry->count();

    
         // get missed count
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","missed");
        $responseQry->equalTo("patient",$patient['reference_code']);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        $responseRate['missedCount'] = $responseQry->count();

        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("status","late");
        $responseQry->equalTo("patient",$patient['reference_code']);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDateObj);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDateObj);
        $responseRate['lateCount'] = $responseQry->count();
 
     
        
        

        $totalResponses = ($responseRate['completedCount'] + $responseRate['missedCount'] + $responseRate['lateCount'] );

        $completed = ($totalResponses) ? ($responseRate['completedCount']/$totalResponses) * 100 :0;
        $responseRate['completed'] =  round($completed);

        $missed = ($totalResponses) ? ($responseRate['missedCount']/$totalResponses) * 100 :0;
        $responseRate['missed'] =  round($missed);

        $late = ($totalResponses) ? ($responseRate['lateCount']/$totalResponses) * 100 :0;
        $responseRate['late'] =  round($late);

        $baselineAnwers = $this->getPatientBaseLine($patient['reference_code']);

        //get patient answers
        $patientAnswers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);
        
       
        //flags chart (total,red,amber,green)  
        $flagsCount = $this->patientFlagsCount($patientAnswers,$baselineAnwers);

        // $submissionChart =  $this->getSubmissionChart($patientAnswers,$missedResponses);
       

        //patient submissions
        $projectController = new ProjectController();
        $submissionsSummary = $projectController->getSubmissionsSummary($patientAnswers); 

        //question chart
        $questionsChartData = $this->getQuestionChartData($patientAnswers,$baselineAnwers);

        //red flags
        $openRedFlags = $this->getOpenRedFlags($patientAnswers);

        $questionLabels = $questionsChartData['questionLabels'];
        $questionChartData = $questionsChartData['chartData'];
        $questionBaseLine = $questionsChartData['questionBaseLine'];
 
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
                                        ->with('questionBaseLine', $questionBaseLine)
                                        ->with('openRedFlags', $openRedFlags)
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

        $answersQry = new ParseQuery("Answer");
        $answersQry->equalTo("response",$response);
        $answersQry->exists("score");
        $answersQry->includeKey("question");
        $answersQry->includeKey("response");
        $answersQry->includeKey("option");
        $answers = $answersQry->find();

        return $answers;
    }

 
    public function patientFlagsCount($projectAnwers,$baselineAnwers)
    {

        $redFlagsByDate = [];
        $amberFlagsByDate = [];
        $greenFlagsByDate = [];
        $totalFlagsByDate = [];
        $baseLineData = [];
       
        foreach ($projectAnwers as $answer)
        {
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $responseId = $answer->get("response")->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y h:i:s');
            $answerDate = strtotime($answerDate);
            $score = $answer->get("score");
            $responseStatus = $answer->get("response")->get("status");
            $patient = $answer->get("patient");

            if($questionType!='single-choice')
                continue;

             

            if($responseStatus!='completed')
                continue;

            if(!isset($redFlagsByDate[$answerDate]))
            {
                $redFlagsByDate[$answerDate]['baseLine']=[];
                $amberFlagsByDate[$answerDate]['baseLine']=[];
                $greenFlagsByDate[$answerDate]['baseLine']=[];
                $redFlagsByDate[$answerDate]['previous']=[];
                $amberFlagsByDate[$answerDate]['previous']=[];
                $greenFlagsByDate[$answerDate]['previous']=[];

            }

            $totalFlagsByDate[$answerDate][] = $score;

            if($baseLineFlag=='red')
            {
              $redFlagsByDate[$answerDate]['baseLine'][] = $responseId;
            }

            if($baseLineFlag=='amber')
            {
                $amberFlagsByDate[$answerDate]['baseLine'][] = $responseId;

            }

            if($baseLineFlag=='green')
            {
                $greenFlagsByDate[$answerDate]['baseLine'][] = $responseId;

            }

            if($previousFlag =='red') 
            {
                $redFlagsByDate[$answerDate]['previous'][] = $responseId;
            }

            if($previousFlag =='amber') 
            {
                $amberFlagsByDate[$answerDate]['previous'][] = $responseId;
            }

            if($previousFlag =='green') 
            {
                $greenFlagsByDate[$answerDate]['previous'][] = $responseId;
            }

            
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
            $redFlagData[$i]["Baseline"] = count($value['baseLine']);
            $redFlagData[$i]["Previous"] = count($value['previous']) ;
 
            $i++;
        }

        ksort($amberFlagsByDate);
        $i=0;
        foreach($amberFlagsByDate as $date => $value)
        { 
            $amberFlagData[$i]["Date"] =  date('d M',$date);
            $amberFlagData[$i]["Baseline"] = count($value['baseLine']);
            $amberFlagData[$i]["Previous"] = count($value['previous']) ;
 
            $i++;
        }

        ksort($greenFlagsByDate);
        $i=0;
        foreach($greenFlagsByDate as $date => $value)
        { 
            $greenFlagData[$i]["Date"] =  date('d M',$date);
            $greenFlagData[$i]["Baseline"] = count($value['baseLine']);
            $greenFlagData[$i]["Previous"] = count($value['previous']) ;
 
            $i++;
        }


        ksort($totalFlagsByDate);
        $i=0;
        foreach($totalFlagsByDate as $date => $value)
        { 
            $totalFlagData[$i]["Date"] =  date('d M',$date);
            $totalFlagData[$i]["score"] = array_sum($value);
 
            $i++;
        }
       
     
        $data['redFlags'] = json_encode($redFlagData);
        $data['amberFlags'] = json_encode($amberFlagData);
        $data['greenFlags'] = json_encode($greenFlagData); 
        $data['totalFlags'] = json_encode($totalFlagData);
        $data['baslineScore'] = array_sum($baseLineData);
        
        return $data;
    }

    public function getOpenRedFlags($projectAnwers)
    {

        $openRedFlags = [];
        $responseOpenRedFlags = [];
        $questionsTypes = ['single-choice','input']; 
 
        foreach ($projectAnwers as $answer)
        {
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $baseLineFlagStatus = $answer->get("baseLineFlagStatus");
            $previousFlagStatus = $answer->get("previousFlagStatus");
            $responseId = $answer->get("response")->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $questionLabel = $answer->get("question")->get("title");

            $responseBaseLineFlag = $answer->get("response")->get("baseLineFlag");
            $responsePreviousFlag = $answer->get("response")->get("previousFlag");
            $responseBaseLineFlagStatus = $answer->get("response")->get("baseLineFlagStatus");
            $responsePreviousFlagStatus = $answer->get("response")->get("previousFlagStatus");

             $sequenceNumber = $answer->get("response")->get("sequenceNumber");
             $occurrenceDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y');


            // if(!in_array($questionType, $questionsTypes))
            //     continue;

             if($questionType!='single-choice')
                continue;

            
            if($baseLineFlag=='red')
            {
              $openRedFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line score of '.$questionLabel, 'flag'=>$baseLineFlag, 'date'=>$occurrenceDate];
            }

 
            if($previousFlag =='red') 
            {
                $openRedFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous score of '.$questionLabel, 'flag'=>$previousFlag, 'date'=>$occurrenceDate];
            }

            if(!isset($responseOpenRedFlags[$responseId]))
            {
                $responseOpenRedFlags[$responseId]=$responseId;

                if($responseBaseLineFlag=='red')
                {
                  $openRedFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line total score set for questionnaire', 'flag'=>$responseBaseLineFlag, 'date'=>$occurrenceDate];
                }

     
                if($responsePreviousFlag =='red') 
                {
                    $openRedFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous total score questionnaire', 'flag'=>$responsePreviousFlag, 'date'=>$occurrenceDate];
                }

            }
            
        }



        return $openRedFlags;
    }

    public function getPatientsFlags($projectAnwers)
    {

        $patientsFlags = [];
        $responseOpenRedFlags = [];
        $questionsTypes = ['single-choice','input']; 
 
        foreach ($projectAnwers as $answer)
        {
            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $baseLineFlagStatus = $answer->get("baseLineFlagStatus");
            $previousFlagStatus = $answer->get("previousFlagStatus");
            $responseId = $answer->get("response")->getObjectId();
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $questionLabel = $answer->get("question")->get("title");

            $responseBaseLineFlag = $answer->get("response")->get("baseLineFlag");
            $responsePreviousFlag = $answer->get("response")->get("previousFlag");
            $responseBaseLineFlagStatus = $answer->get("response")->get("baseLineFlagStatus");
            $responsePreviousFlagStatus = $answer->get("response")->get("previousFlagStatus");

            $responseStatus = $answer->get("response")->get("status");

             $sequenceNumber = $answer->get("response")->get("sequenceNumber");
             $occurrenceDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y');

             if($responseStatus=='base_line')
                continue;

             if($questionType!='single-choice')
                continue;

         
                
            $patientsFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line score of '.$questionLabel, 'flag'=>$baseLineFlag, 'date'=>$occurrenceDate];
 
            $patientsFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous score of '.$questionLabel, 'flag'=>$previousFlag, 'date'=>$occurrenceDate];
            

            if(!isset($responseOpenRedFlags[$responseId]))
            {
                $responseOpenRedFlags[$responseId]=$responseId;

                $patientsFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to base line total score set for questionnaire', 'flag'=>$responseBaseLineFlag, 'date'=>$occurrenceDate];
                 
                $patientsFlags[]= ['sequenceNumber'=>$sequenceNumber,'reason'=>'Compared to previous total score questionnaire', 'flag'=>$responsePreviousFlag, 'date'=>$occurrenceDate];
            }
            
        }



        return $patientsFlags;
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

        $patient = User::find($patientId)->toArray();

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


        $responseStatus = ["completed"];
        $patientAnwers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);

        $projectController = new ProjectController();
        $submissionsSummary = $projectController->getSubmissionsSummary($patientAnwers); 

        return view('project.patients.submissions')->with('active_menu', 'patients')
                                                ->with('active_tab', 'submissions')
                                                ->with('tab', '02')
                                                ->with('patient', $patient)
                                                ->with('hospital', $hospital)
                                                 ->with('project', $project)
                                                 ->with('logoUrl', $logoUrl)
                                                 ->with('endDate', $endDate)
                                                 ->with('startDate', $startDate)
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
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );


        $responseStatus = ["completed"];
        $patientAnwers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);

        $patientFlags =  $this->getPatientsFlags($patientAnwers); 

        return view('project.patients.flags')->with('active_menu', 'patients')
                                                ->with('active_tab', 'flags')
                                                ->with('tab', '03')
                                                ->with('patient', $patient)
                                                ->with('hospital', $hospital)
                                                ->with('project', $project)
                                                ->with('logoUrl', $logoUrl)
                                                ->with('endDate', $endDate)
                                                ->with('startDate', $startDate)
                                                ->with('patientFlags', $patientFlags);
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

    public function patientSummary($patients,$startDate,$endDate)
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
            $patientResponses[$patientId]['nextSubmission'] = $nextOccurrence;
            $patientResponses[$patientId]['lastSubmission'] = '-' ;
            $patientResponses[$patientId]['missed'] =[];
            $patientResponses[$patientId]['late'] =[];
            $patientResponses[$patientId]['completed'] =[];
            

        }

        $responses = $this->getPatientsResponseByDate($patients,0,[] ,$startDate,$endDate);  
        $completedResponses = [];
        $lateResponses = [];
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

            if($status=='missed')
            {
                $patientResponses[$patient]['missed'][]=$responseId;
                $missedResponses[]=$responseId;
                 
            }
            elseif($status=='late')
            {
                $patientResponses[$patient]['late'][]=$responseId;
                $lateResponses[]=$response;
                
            }
            elseif($status=='completed')
            {
                $patientResponses[$patient]['completed'][]=$responseId;
                $completedResponses[]=$response;
                
            }
        }
         
        $answersQry = new ParseQuery("Answer");
        $answersQry->containedIn("response", $completedResponses);
        $answersQry->includeKey("response");
        $anwsers = $answersQry->find();
        
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
            }

        }
       
       

        $completed = (count($responses)) ? (count($completedResponses)/count($responses)) * 100 :0;
        $completed =  round($completed);

        $late = (count($responses)) ? (count($lateResponses)/count($responses)) * 100 :0;
        $late =  round($late);

        $missed = (count($responses)) ? (count($missedResponses)/count($responses)) * 100 :0;
        $missed =  round($missed);
         
 
        $data['patientResponses']=$patientResponses;
        $data['completed']=$completed; 
        $data['late']=$late; 
        $data['missed']=$missed; 
        $data['completedCount']=count($completedResponses);
        $data['lateCount']=count($lateResponses);
        $data['missedCount']=count($missedResponses);
 

        return $data;
        
    }

    public function getPatientsResponseByDate($patients,$page=0,$responseData,$startDate,$endDate)  
    {
        $displayLimit = 20; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",["completed","late","missed"]);
        $responseQry->containedIn("patient",$patients);
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
            $responseData = $this->getPatientsResponseByDate($patients,$page,$responseData,$startDate,$endDate);  
        }  
        
        return $responseData;
     
    }

    public function getPatientsResponses($patients,$projectId,$page=0,$responseData,$statusFlag=1)
    {
        $displayLimit = 20; 
 
        $responseQry = new ParseQuery("Response");
        if($statusFlag)
            $responseQry->containedIn("status",["completed","missed"]);
        elseif($statusFlag==2)
           $responseQry->containedIn("status",["completed"]); 
       elseif($statusFlag==2)
           $responseQry->containedIn("status",["missed"]); 

        $responseQry->containedIn("patient",$patients);
        //$responseQry->equalTo("project",$projectId);
        $responseQry->descending("occurrenceDate");
        $responseQry->limit($displayLimit);
        $responseQry->skip($page * $displayLimit);
        $responses = $responseQry->find();  
        $responseData = array_merge($responses,$responseData); 

        if(!empty($responses))
        {
            $page++;
            $responseData = $this->getPatientsResponses($patients,$projectId,$page,$responseData);
        }  
        
        return $responseData;
     
    }

    public function getPatientAnwers($patient,$projectId,$page=0,$anwsersData)
    {
        $displayLimit = 20; 

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
        $displayLimit = 20; 

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
        $answersQry->ascending("occurrenceDate");
 
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
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y', strtotime('+1 day'));

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate))
                     );


        $patients[] = $patient['reference_code'];
        $responseArr=[];
         
        $responses = $this->getPatientsResponseByDate($patients,$projectId,0,[],$startDateObj,$endDateObj);
        foreach ($responses as  $response) {
            $responseId = $response->getObjectId();
            $responseArr[$responseId] = $response->get("occurrenceDate")->format('d M');
        }

        $answers = $this->getPatientAnwersByDate($patient['reference_code'],$projectId,0,[],$startDateObj,$endDateObj);

        $baseLineArr = [];
        $submissionArr = [];
        $questionArr = [];
        $inputScores = [];
        $inputChartData = [];
        $inputLabels = [];
        $allScore = [];

        
        $inputBaseQuestionId = '';
        $inputLable = '';
        $inputBaseLineScore ='';

       
        foreach ($answers as   $answer) {
            $responseStatus = $answer->get("response")->get("status");
            $questionId = $answer->get("question")->getObjectId();
            $questionType = $answer->get("question")->get("type");
            $questionTitle = $answer->get("question")->get("title");
            $responseId = $answer->get("response")->getObjectId();
            $optionScore = ($questionType=='multi-choice' || $questionType=='single-choice') ? $answer->get("option")->get("score"):0;
            $optionValue = $answer->get("value");

            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y h:i:s');
            $answerDate = strtotime($answerDate);

            
            if($responseStatus=='missed' || $responseStatus=='started')
                continue;

            if($questionType=='descriptive')
                continue;
           
            if($questionType=='input')
            { 
                $inputBaseQuestionId = $questionId;
                $inputLable =  ucfirst(strtolower($questionTitle));

                $inputLabels[$questionId] = $inputLable;
                $allScore[$questionId][] = $optionValue;

                if($responseStatus=="base_line" && !isset($baseLineArr[$questionId]))
                    $baseLineArr[$questionId] =$optionValue;
                else
                    $inputScores[$questionId][$answerDate] = $optionValue ;

                continue;
            }
            elseif ($questionType=='multi-choice') {        //if multichoise sum up scores

                continue;
               // if($responseStatus=="base_line")
               //  {
               //      if(isset($baseLineArr[$questionId]))
               //          $baseLineArr[$questionId] += $optionScore;
               //      else
               //          $baseLineArr[$questionId] = $optionScore;
               //  }
               //  else
               //  {
               //      if(isset($submissionArr[$responseId][$questionId]))
               //          $submissionArr[$responseId][$questionId] += $optionScore;
               //      else
               //          $submissionArr[$responseId][$questionId] = $optionScore;
                   
               //  }
            } 
             elseif ($questionType=='single-choice')  
            {
                if($responseStatus=="base_line" && !isset($baseLineArr[$questionId]))
                   $baseLineArr[$questionId] =$optionScore;
                else
                {
                   $submissionArr[$responseId][$questionId]['baslineFlag'] = $baseLineFlag ;
                   $submissionArr[$responseId][$questionId]['previousFlag'] = $previousFlag ;
                }

             } 
            
            $questionArr[$questionId]= $questionTitle;
             
        }
        
        foreach ($inputScores as $questionId => $data) {
            ksort($data);
            $i=0;
            foreach($data as $date => $value)
            { 
                $inputChartData[$questionId][$i]['date'] = date('d M',$date);
                $inputChartData[$questionId][$i]['value'] = $value;
               // $inputChartData[$questionId][$i]['base_line'] = $baseLineArr[$questionId];
                $i++;
            }
            $inputChartData[$questionId] = $inputChartData[$questionId];
            
        }
     
        return view('project.patients.reports')->with('active_menu', 'patients')
                                        ->with('active_tab', 'reports')
                                        ->with('tab', '04')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('patient', $patient)
                                        ->with('responseArr', $responseArr)
                                        ->with('questionArr', $questionArr)
                                        ->with('baseLineArr', $baseLineArr)
                                        ->with('submissionArr', $submissionArr)
                                        ->with('inputBaseLineScore', $inputBaseLineScore)
                                        ->with('inputLabels', $inputLabels)
                                        ->with('allScore', $allScore)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('inputChartData', $inputChartData); 
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

            $baseLineFlag = $answer->get("baseLineFlag");
            $previousFlag = $answer->get("previousFlag");
            $answerDate = $answer->get("response")->get("occurrenceDate")->format('d-m-Y h:i:s');
            $answerDate = strtotime($answerDate);
            $questionLabel =  ucfirst(strtolower($questionTitle));

            
            if($responseStatus=='missed' || $responseStatus=='started')
                continue;

            if($questionType=='descriptive')
                continue;
           
            if($questionType=='input')
            { 
                $inputBaseQuestionId = $questionId;
                

                $questionLabels[$questionId] = $questionLabel;
                $allScore[$questionId][] = $optionValue;

                if($responseStatus=="base_line")
                    $baseLineArr[$questionId] =$optionValue;
                else
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

                if($responseStatus=="base_line")
                   $baseLineArr[$questionId] =$optionScore;
                else
                {
                   $inputScores[$questionId][$answerDate] = $optionScore ;
                   $submissionArr[$responseId][$questionId]['baslineFlag'] = $baseLineFlag ;
                   $submissionArr[$responseId][$questionId]['previousFlag'] = $previousFlag ;
                }

             } 
            
        }
        
        
        foreach ($inputScores as $questionId => $data) {
            ksort($data);
            $i=0;
            foreach($data as $date => $value)
            { 
                $chartData[$questionId][$i]['date'] = date('d M',$date);
                $chartData[$questionId][$i]['value'] = $value;
               
                $i++;
            }
            
            
        }
        
        $questiondata['questionBaseLine']=$baseLineArr;
        $questiondata['chartData']=$chartData;
        $questiondata['questionLabels']=$questionLabels;
        $questiondata['submissions']=$submissionArr;
        $questiondata['singleChoiceQuestion']=$singleChoiceQuestion;
        
       
        return $questiondata;
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
