<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;

use \Cache;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\Hospital;
use App\Projects;
use App\User;
use App\UserAccess;
use \Input;
use \Session;
use App\Http\Controllers\Project\PatientController;
use \Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         
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
      
        try
        {
          $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

          $hospital = $hospitalProjectData['hospital'];
          $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

          $project = $hospitalProjectData['project'];
          $projectId = intval($project['id']);

          $inputs = Input::get(); 


          $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('today - '.DATE_DIFFERENCE.' days'));
          $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');

          //date object
          $startDateObj = array(
                    "__type" => "Date",
                    "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                   );

          $endDateObj = array(
                        "__type" => "Date",
                        "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                       );

          $startDateYmd = date('Y-m-d', strtotime($startDate));
          $endDateYmd = date('Y-m-d', strtotime($endDate .'+1 day'));

          $patients = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->orderBy('created_at','desc')->get()->toArray();

          $patientByDate = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->where('created_at','<=',$endDateYmd)->orderBy('created_at','desc')->get()->toArray();

          //dd($patients);
          $activepatients = [];
          $patientReferenceCode = [];
          $cond = [];
          $sort = [];
          foreach ($patients as  $patient) {
              $patientReferenceCode[] = $patient['reference_code'];
          }

          foreach ($patientByDate as  $patient) {
              
              if($patient['account_status']=='active')
                  $activepatients[]= $patient['reference_code'];
          }

          $responseStatus = ["completed","late","missed"];

          // Cache::flush();
          // CACHE PROJECT RESPONSES
          $responseCacheKey = "projectResponses_".$projectId;
          $cacheDateKey = strtotime($startDate)."_".strtotime($endDate);

          //if cache data exist for project 
          if (Cache::has($responseCacheKey)) {
              $cacheProjectResponses =  Cache::get($responseCacheKey); 

              //if cache data exist for required date period 
              if(isset($cacheProjectResponses[$cacheDateKey]))
              {
                $responseCount = $cacheProjectResponses[$cacheDateKey]['responseCount'];
                $projectFlagsChart = $cacheProjectResponses[$cacheDateKey]['projectFlagsChart'];
                $submissionsSummary = $cacheProjectResponses[$cacheDateKey]['submissionsSummary'];

              }
              else
              {
                $projectResponses = $this->getProjectResponsesByDate($projectId,0,[],$startDateObj,$endDateObj,$responseStatus,$cond,$sort);
            
                $responseCount = $this->getProjectResponseCounts($projectResponses);
                $cacheProjectResponses[$cacheDateKey]['responseCount'] = $responseCount;
                
                //red flags,amber flags ,unreviwed submission , submission
                $projectFlagsChart = $this->projectFlagsChart($projectResponses);
                $cacheProjectResponses[$cacheDateKey]['projectFlagsChart'] = $projectFlagsChart;

                 //patient completed  and late submissions 
                $lastFiveSubmissions = array_slice($responseCount['patientSubmissions'], 0, 5, true);
                $submissionsSummary = $this->getSubmissionsSummary($lastFiveSubmissions); 
                $cacheProjectResponses[$cacheDateKey]['submissionsSummary'] = $submissionsSummary;

                Cache:: forever($responseCacheKey, $cacheProjectResponses);
              }

          }
          else
          {
            $projectResponses = $this->getProjectResponsesByDate($projectId,0,[],$startDateObj,$endDateObj,$responseStatus,$cond,$sort);
            
            $responseCount = $this->getProjectResponseCounts($projectResponses);
            $cacheProjectResponses[$cacheDateKey]['responseCount'] = $responseCount;
            
            //red flags,amber flags ,unreviwed submission , submission
            $projectFlagsChart = $this->projectFlagsChart($projectResponses);
            $cacheProjectResponses[$cacheDateKey]['projectFlagsChart'] = $projectFlagsChart;

             //patient completed  and late submissions 
            $lastFiveSubmissions = array_slice($responseCount['patientSubmissions'], 0, 5, true);
            $submissionsSummary = $this->getSubmissionsSummary($lastFiveSubmissions); 
            $cacheProjectResponses[$cacheDateKey]['submissionsSummary'] = $submissionsSummary;

            //store cache data
            Cache:: forever($responseCacheKey, $cacheProjectResponses); 
          } 
          
          
          // CACHE PATIENT SUMMARY
          $patientsSummaryCacheKey = "patientsSummary_".$projectId;
          if (Cache::has($patientsSummaryCacheKey)) {
              $cachePatientsSummary =  Cache::get($patientsSummaryCacheKey); 
              if(isset($cachePatientsSummary[$cacheDateKey]))
              {
                $patientsSummary = $cachePatientsSummary[$cacheDateKey];
              }
              else
              {
                $patientController = new PatientController();
                $patientsSummary = $patientController->patientsSummary($patientReferenceCode ,$startDateObj,$endDateObj,[],["desc" =>"completed"]);
                $cachePatientsSummary[$cacheDateKey] = $patientsSummary;
                Cache:: forever($patientsSummaryCacheKey, $cachePatientsSummary);
              }

          }
          else
          {
            $patientController = new PatientController();
            $patientsSummary = $patientController->patientsSummary($patientReferenceCode ,$startDateObj,$endDateObj,[],["desc" =>"completed"]);
            $cachePatientsSummary[$cacheDateKey] = $patientsSummary;
            Cache:: forever($patientsSummaryCacheKey, $cachePatientsSummary); 
          } 

          $patientResponses = $patientsSummary['patientResponses'];
          $patientSortedData = $patientsSummary['patientSortedData'];
          $totalSubmissionCount = $responseCount['totalSubmissionCount'];
   
          $patientSortedData = array_slice($patientSortedData, 0, 5, true);

                              
          // CACHE PATIENT ALERTS AND NOTIFICATION
          $patientsAlertsCacheKey = "patientsAlerts_".$projectId;
          if (Cache::has($patientsAlertsCacheKey)) {

            $cachePatientsAlerts =  Cache::get($patientsAlertsCacheKey); 

            $projectAlerts = $cachePatientsAlerts['ALERTS'];
            $submissionNotifications = $cachePatientsAlerts['NOTIFICATIONS']; 

          }
          else
          {
            
            $cond=['cleared'=>false];
            $projectAlerts = $this->getProjectAlerts($projectId,4,0,[],$cond);
            $subCond=['referenceType'=>"Response"];
            $submissionNotifications = $this->getProjectAlerts($projectId,5,0,[],$subCond); 

            $cachePatientsAlerts['ALERTS'] = $projectAlerts;
            $cachePatientsAlerts['NOTIFICATIONS'] = $submissionNotifications;
            Cache:: forever($patientsAlertsCacheKey, $cachePatientsAlerts); 
          } 

        } 
        catch (\Exception $e) {

          Log::error($e->getMessage());
          abort(404);  

        }
         
        return view('project.dashbord')->with('active_menu', 'dashbord')
                                        ->with('totalSubmissionCount', $totalSubmissionCount) 
                                        ->with('responseCount', $responseCount) 
                                        ->with('activepatients', count($activepatients))
                                        ->with('allpatientscount', count($patientByDate))              
                                        ->with('submissionsSummary', $submissionsSummary)
                                        ->with('patientSortedData', $patientSortedData)
                                        ->with('patientResponses', $patientResponses)
                                        ->with('patientMiniGraphData', $patientsSummary['patientMiniGraphData'])
                                        ->with('projectFlagsChart', $projectFlagsChart)
                                        ->with('project', $project)
                                        ->with('patients', $patients)
                                        ->with('projectAlerts', $projectAlerts)
                                        ->with('submissionNotifications', $submissionNotifications)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl);

    }

    public function getProjectAlerts($projectId,$limit,$page=0,$dateCond=[],$cond=[],$refCond=[])
    {
        $alertQry = new ParseQuery("Alerts");
        $alertQry->equalTo("project",$projectId);
        if(!empty($cond))
        {
            foreach ($cond as $key => $value) {
                $alertQry->equalTo($key,$value);
            }
        }
        $alertCount = $alertQry->count();

        $alertQry = new ParseQuery("Alerts");
        $alertQry->equalTo("project",$projectId);
        if($limit!='')
        {
            $alertQry->limit($limit);
            $alertQry->skip($page * $limit); 
        }
        

        if(!empty($dateCond))
        {
            $alertQry->greaterThanOrEqualTo("createdAt",$dateCond['startDate']);
            $alertQry->lessThanOrEqualTo("createdAt",$dateCond['endDate']);
        }

        if(!empty($cond))
        {
            foreach ($cond as $key => $value) {
                $alertQry->equalTo($key,$value);
            }
        }
        $alertQry->descending("createdAt","cleared");
        $alerts = $alertQry->find();

        $alertMsg = [];
        
        $alertConfig = $this->alertMessageConfig();
        $alertTypes = $alertConfig['alertTypes'];
        $alertClases = $alertConfig['alertClases'];
        $responseflagColumns = $alertConfig['responseflagColumns'];

        $responseObject = [];

        foreach ($alerts as $alert) {
            $alertType = $alert->get("alertType"); 
            $referenceType = $alert->get("referenceType");
            $patient = $alert->get("patient");
            $referenceId = $alert->get("referenceId");
            
            if(isset($alertTypes[$alertType]))
            {

                $alertClass = (isset($alertClases[$alertType])) ? $alertClases[$alertType]:"";
                $alertContent = (isset($alertTypes[$alertType])) ? $alertTypes[$alertType]:"";
                
                if($referenceType == "Response")
                {
                  $alertMsgData = $this->getResponseAlertMsg($referenceId,$responseObject,$alertType,$alertClass,$alertContent,$responseflagColumns);
                  $alertMsg[] = $alertMsgData['alertMsg'];
                  $responseObject = $alertMsgData['responseObject'];
                }
                elseif($referenceType == "patient")
                {
                  $alertMsg[] = $this->getPatientAlertMsg($patient,$alertClass,$alertContent);
                }
                

            }
           
        }
 
        $data['alertMsg']=$alertMsg;
        $data['alertCount']=$alertCount;

        return $data;
    }

    public function getResponseAlertMsg($referenceId,$responseObject,$alertType,$alertClass,$alertContent,$responseflagColumns)
    {
        $responseQry = new ParseQuery("Response");
        $responseQry->equalTo("objectId", $referenceId); 
        $response = $responseQry->first();
        $responseObject[$referenceId] = $response;
       
        if(!empty($response))
        {
            $responseFlagColumn ="";
            foreach ($responseflagColumns as $key => $values) {

                if(in_array($alertType, $values))
                {
                    $responseFlagColumn =   $key;
                    break;
                }
             
            }

            $url = "submissions/".$referenceId;
            $patient = $response->get("patient");
            $sequenceNumber = $response->get("sequenceNumber");
            $reviewStatus = $response->get("reviewed");
            $reviewNote = $response->get("reviewNote");

            $responseFlagType = $response->get($responseFlagColumn);
            $occurrenceDate = $response->get("occurrenceDate")->format('dS M');
            $message = sprintf($alertContent, $responseFlagType,$sequenceNumber );

            $responseId = $response->getObjectId();
            $alertMsg = ['patient'=>$patient,'referenceId'=>$responseId,'occurrenceDate'=>$occurrenceDate,'sequenceNumber'=>$sequenceNumber,'previousTotalRedFlags'=>$responseFlagType,'reviewNote'=>$reviewNote,'reviewStatus'=>$reviewStatus,'URL'=>$url,'msg'=>$message,"class"=>$alertClass];
        }

        $data['alertMsg'] = $alertMsg;
        $data['responseObject'] = $responseObject;

        return $data;

    }

    public function getPatientAlertMsg($referenceId,$alertClass,$alertContent)
    {

        $patient = User::where('type','patient')->where('reference_code',$referenceId)->first()->toArray();
        $url = "patients/".$patient['id'];
        $message = sprintf($alertContent, SETUP_ALERT ,SETUP_LIMIT);
        $alertMsg = ['patient'=>$referenceId,'referenceId'=>$patient['id'],'URL'=>$url,'msg'=>$message,"class"=>$alertClass];

        return $alertMsg;

    }

    public function alertMessageConfig()
    {
        $alertTypes = [
        'compared_to_previous_red_flags'=>"%u red flags have been raised for submission number %d in comparison with previous submission",
        'more_red_flags_compared_to_previous'=>"More than %u red flags have been raised for submission number %d in comparison with previous submission",
        'more_red_flags_compared_to_baseline'=>"More than %u red flags have been raised for submission number %d in comparison with baseline submission",
        'more_or_equal_red_flags_compared_to_previous'=>"%u or more red flags have been raised for submission number %d in comparison with previous submission",
        'more_or_equal_red_flags_compared_to_baseline'=>"%u or more red flags have been raised for submission number %d in comparison with baseline submission",

        'less_red_flags_compared_to_previous'=>"Less than %u red flags have been raised for submission number %d in comparison with previous submission",
        'less_red_flags_compared_to_baseline'=>"Less than %u red flags have been raised for submission number %d in comparison with baseline submission",
        'less_or_equal_red_flags_compared_to_previous'=>"%u or less red flags have been raised for submission number %d in comparison with previous submission",
        'less_or_equal_red_flags_compared_to_baseline'=>"%u or less red flags have been raised for submission number %d in comparison with baseline submission",

        'more_amber_flags_compared_to_previous'=>"More than %u amber flags have been raised for submission number %d in comparison with previous submission",
        'more_amber_flags_compared_to_baseline'=>"More than %u amber flags have been raised for submission number %d in comparison with baseline submission",
        'more_or_equal_amber_flags_compared_to_previous'=>"%u or more amber flags have been raised for submission number %d in comparison with previous submission",
        'more_or_equal_amber_flags_compared_to_baseline'=>"%u or more amber flags have been raised for submission number %d in comparison with baseline submission",

        'less_amber_flags_compared_to_previous'=>"Less than %u amber flags have been raised for submission number %d in comparison with previous submission",
        'less_amber_flags_compared_to_baseline'=>"Less than %u amber flags have been raised for submission number %d in comparison with baseline submission",
        'less_or_equal_amber_flags_compared_to_previous'=>"%u or less amber flags have been raised for submission number %d in comparison with previous submission",
        'less_or_equal_amber_flags_compared_to_baseline'=>"%u or less amber flags have been raised for submission number %d in comparison with baseline submission",

        'more_green_flags_compared_to_previous'=>"More than %u green flags have been raised for submission number %d in comparison with previous submission",
        'more_green_flags_compared_to_baseline'=>"More than %u green flags have been raised for submission number %d in comparison with baseline submission",
        'more_or_equal_green_flags_compared_to_previous'=>"%u or more green flags have been raised for submission number %d in comparison with previous submission",
        'more_or_equal_green_flags_compared_to_baseline'=>"%u or more green flags have been raised for submission number %d in comparison with baseline submission",

        'less_green_flags_compared_to_previous'=>"Less than %u green flags have been raised for submission number %d in comparison with previous submission",
        'less_green_flags_compared_to_baseline'=>"Less than %u green flags have been raised for submission number %d in comparison with baseline submission",
        'less_or_equal_green_flags_compared_to_previous'=>"%u or less green flags have been raised for submission number %d in comparison with previous submission",
        'less_or_equal_green_flags_compared_to_baseline'=>"%u or less green flags have been raised for submission number %d in comparison with baseline submission",

        'device_setup_alert'=>"Set up has been done from %u different devices.Account will be suspended when count reached %d",

        'new_patient'=>"New Patient Created"
        ];

        $alertClases = [
        'compared_to_previous_red_flags'=>"danger",
        'more_red_flags_compared_to_previous'=>"danger",
        'more_red_flags_compared_to_baseline'=>"danger",
        'more_or_equal_red_flags_compared_to_previous'=>"danger",
        'more_or_equal_red_flags_compared_to_baseline'=>"danger",

        'less_red_flags_compared_to_previous'=>"danger",
        'less_red_flags_compared_to_baseline'=>"danger",
        'less_or_equal_red_flags_compared_to_previous'=>"danger",
        'less_or_equal_red_flags_compared_to_baseline'=>"danger",

        'more_amber_flags_compared_to_previous'=>"warning",
        'more_amber_flags_compared_to_baseline'=>"warning",
        'more_or_equal_amber_flags_compared_to_previous'=>"warning",
        'more_or_equal_amber_flags_compared_to_baseline'=>"warning",

        'less_amber_flags_compared_to_previous'=>"warning",
        'less_amber_flags_compared_to_baseline'=>"warning",
        'less_or_equal_amber_flags_compared_to_previous'=>"warning",
        'less_or_equal_amber_flags_compared_to_baseline'=>"warning",

        'more_green_flags_compared_to_previous'=>"success",
        'more_green_flags_compared_to_baseline'=>"success",
        'more_or_equal_green_flags_compared_to_previous'=>"success",
        'more_or_equal_green_flags_compared_to_baseline'=>"success",

        'less_green_flags_compared_to_previous'=>"success",
        'less_green_flags_compared_to_baseline'=>"success",
        'less_or_equal_green_flags_compared_to_previous'=>"success",
        'less_or_equal_green_flags_compared_to_baseline'=>"success",

        'device_setup_alert'=>"warning",
        'new_patient'=>"info"
        ];

        $responseflagColumns = [
        'previousTotalRedFlags'=>["compared_to_previous_red_flags","more_red_flags_compared_to_previous","more_or_equal_red_flags_compared_to_previous","less_red_flags_compared_to_previous","less_or_equal_red_flags_compared_to_previous"],
        'baseLineTotalRedFlags'=>["more_red_flags_compared_to_baseline","more_or_equal_red_flags_compared_to_baseline","less_red_flags_compared_to_baseline","less_or_equal_red_flags_compared_to_baseline"],
        
        'previousTotalAmberFlags'=>["more_amber_flags_compared_to_previous","more_or_equal_amber_flags_compared_to_previous","less_amber_flags_compared_to_previous","less_or_equal_amber_flags_compared_to_previous"],

        'baseLineTotalAmberFlags'=>["more_amber_flags_compared_to_baseline","more_or_equal_amber_flags_compared_to_baseline","less_amber_flags_compared_to_baseline","less_or_equal_amber_flags_compared_to_baseline"],

        'previousTotalGreenFlags'=>["more_green_flags_compared_to_previous","more_or_equal_green_flags_compared_to_previous","less_green_flags_compared_to_previous","less_green_flags_compared_to_baseline"],

        'baseLineTotalGreenFlags'=>["more_green_flags_compared_to_baseline","more_or_equal_green_flags_compared_to_baseline","less_or_equal_green_flags_compared_to_previous","less_or_equal_green_flags_compared_to_baseline"],

        ];

        $data['alertTypes'] = $alertTypes;
        $data['alertClases'] = $alertClases;
        $data['responseflagColumns'] = $responseflagColumns;
        return $data;
    }

    public function getSubmissionList($hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $inputs = Input::get(); 

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('today - '.DATE_DIFFERENCE.' days'));
        $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');

        $startDateObj = array(
                  "__type" => "Date",
                  "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                 );

        $endDateObj = array(
                      "__type" => "Date",
                      "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                     );

        $cond = [];
        if($inputs['object_type']=="submission")
            $status=["completed"];
        else
            $status=["completed","late","missed"];

 
        if(isset($inputs['sort']))
        {
            $sortBy = $inputs['sort'];
            $sortData = explode('-', $inputs['sort']);
            if(count($sortData)==2)
            {
                $sort = [$sortData[1]=>$sortData[0]];
            }
            
        }

        if(isset($inputs['cond']))
        { 
            $filterBy = $inputs['cond'];
            $filterData = explode('-', $inputs['cond']);
            if(count($filterData)==2 && $filterData[0]!='')
            {
                if($filterData[0]=='unreviewed')
                    $cond = ['reviewed'=>'unreviewed'];
                else
                    $cond = [$filterData[1]=>$filterData[0]];
            }
            
        }

        

        if($inputs['object_type']=="patient-submission")
        {
            $patients[] =$inputs['object_id'];
            $patientController = new PatientController();
            $responses = $patientController->getPatientsResponseByDate($patients,0,[] ,$startDateObj,$endDateObj,$status,$cond,$sort,$inputs['limit']);
        }
        else
        {
            $responses = $this->getProjectResponsesByDate($projectId,0,[] ,$startDateObj,$endDateObj,$status,$cond,$sort,$inputs['limit']);
        }
 

     $submissionsSummary = $this->getSubmissionsSummary($responses);
      $str = '';
      foreach($submissionsSummary as $responseId=> $submission)
      {
            if($submission['status']=='missed' || $submission['status']=='late')
            {
                $str .='<tr >';
                if($inputs['object_type']=="submission")
                {
                    $str .='<td class="text-center">'.$submission['patient'].'</td>';
                }
                $str.='<td class="text-center">
                  <h4 class="semi-bold m-0 flagcount">'.$submission['occurrenceDate'].'</h4>
                  <sm><b># '.$submission['sequenceNumber'].' </b></sm>
               </td>
               
                  <td class="text-right sorting">0</td>
                  <td class="text-center sorting">0</td>
                  <td class="text-left sorting">0</td>
               
                 <td class="text-right semi-bold margin-none flagcount p-h-0">
                    <h4><b class="text">-</b></h4>
                 </td>
                 <td  class="text-center semi-bold margin-none flagcount p-h-0">
                   <h4><b>/</b></h4>
                 </td>
                 <td  class="text-left semi-bold margin-none flagcount p-h-0">
                    <h4><b class="f-w text-">-</b></h4>
                 </td>

                 <td class="text-right sorting text-error"> 0</td>
                 <td class="text-center sorting text-warning">0</td>
                 <td class="text-left sorting text-success"> 0</td>
            
                 <td class="text-right sorting text-error">0</td>
                 <td class="text-center sorting text-warning">0</td>
                 <td class="text-left sorting text-success">0</td>
              
               <td class="text-center text-success">-</td>
               <td class="text-center text-success">'. getStatusName($submission['status']) .'</td>
               <td class="text-center text-success">-</td>
            </tr>';
            }
            else
            {
                $str .='<tr onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/submissions/'.$responseId.' \';">';
                if($inputs['object_type']=="submission")
                { 
                    $str .='<td class="text-center">'.$submission['patient'].'</td>';
                }
                $str.='<td class="text-center">
                  <h4 class="semi-bold m-0 flagcount">'.$submission['occurrenceDate'].'</h4>
                  <sm><b># '.$submission['sequenceNumber'].' </b></sm>
               </td>
               
                  <td class="text-right sorting">'. $submission['baseLineScore'].'</td>
                  <td class="text-center sorting">'. $submission['previousScore'].'</td>
                  <td class="text-left sorting">'. $submission['totalScore'] .'</td>
               
                 <td class="text-right semi-bold margin-none flagcount p-h-0" width="4%">
                    <h4><b class="text-'.$submission['totalBaseLineFlag'] .'">'. $submission['comparedToBaslineScore'].'</b></h4>
                 </td>
                 <td  class="text-center semi-bold margin-none flagcount p-h-0">
                   <h4><b>/</b></h4>
                 </td>
                 <td  class="text-left semi-bold margin-none flagcount p-h-0">
                    <h4><b class="f-w text-'.$submission['totalPreviousFlag'].'">'.$submission['comparedToPrevious'] .'</b></h4>
                 </td>

                 <td class="text-right sorting text-error"> '.$submission['previousFlag']['red'] .'</td>
                 <td class="text-center sorting text-warning">'.$submission['previousFlag']['amber'] .'</td>
                 <td class="text-left sorting text-success"> '.$submission['previousFlag']['green'] .'</td>
            
                 <td class="text-right sorting text-error">'. $submission['baseLineFlag']['red'] .'</td>
                 <td class="text-center sorting text-warning">'. $submission['baseLineFlag']['amber'].'</td>
                 <td class="text-left sorting text-success">'. $submission['baseLineFlag']['green'] .'</td>
              
               <td class="text-center text-success">'.$submission['alert'].'</td>
               <td class="text-center text-success">'. getStatusName($submission['status']) .'</td>
                
              <td class="text-center text-success"><div class="submissionStatus">'. getStatusName($submission['reviewed']) .'</div></td>

            </tr>';
        }
      }
        

        return response()->json([
                    'code' => 'data',
                    'data' => $str,
                        ], 200);
   
         
    }

    public function getPatientSummaryList($hospitalSlug,$projectSlug)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $inputs = Input::get(); 

        $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('today - '.DATE_DIFFERENCE.' days'));
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


        $patients = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->where('created_at','>=',$startDateYmd)->where('created_at','<=',$endDateYmd)->orderBy('created_at','desc')->get()->toArray();

        $patientIds = [];
        foreach ($patients as  $patient) {
            $patientReferenceCode[] = $patient['reference_code'];
            $patientIds[$patient['reference_code']] = $patient['id'];
        }

        $sort=[];
        $cond=[];

        if(isset($inputs['sort']))
        {
            $sortBy = $inputs['sort'];
            $sortData = explode('-', $inputs['sort']);
            if(count($sortData)==2)
            {
                $sort = [$sortData[1]=>$sortData[0]];
            }
            
        }


        $patientController = new PatientController();
        $patientsSummary = $patientController->patientsSummary($patientReferenceCode ,$startDateObj,$endDateObj,$cond,$sort); 
        $patientResponses = $patientsSummary['patientResponses'];
        $patientSortedData = $patientsSummary['patientSortedData'];
        $patientMiniGraphData = $patientsSummary['patientMiniGraphData'];
 
      $str = '';

        $miniChartData = [];
        if(isset($inputs['limit']) && $inputs['limit']!='')
        {
            $limit=$inputs['limit'];
            $patientSortedData = array_slice($patientSortedData, 0, $limit, true);
        }
        
     
     foreach($patientSortedData as $referenceCode => $data)
     {
        $patientId = $patientIds[$referenceCode];    
        $status_class='';
        if(!isset($patientResponses[$referenceCode])) //inactive patient data
        {
            $patientsSummary[$referenceCode]['lastSubmission'] = '-';
            $patientsSummary[$referenceCode]['nextSubmission'] = '-';
            $patientsSummary[$referenceCode]['completed'] = [];
            $patientsSummary[$referenceCode]['missed'] = 0;
            $patientsSummary[$referenceCode]['late'] = [];
       
        }

        $miniChartData[$referenceCode] = (isset($patientMiniGraphData[$referenceCode]))?json_encode($patientMiniGraphData[$referenceCode]):'[]';
        
        $patientSummary = $patientResponses[$referenceCode];

        $str.= '<tr><td onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">'. $referenceCode .'</td>
           <td  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">
              <div class="lst-sub submission-count">
                 <h2 class="bold inline">
                    '. $patientSummary['completed'] .'<br>
                    <sm class="text-success">Completed</sm>
                 </h2>
                 <h2 class="bold inline">
                    '. $patientSummary['late'] .'<br>
                    <sm class="text-warning">Late</sm>
                 </h2>
                 <h2 class="bold inline">
                    '. $patientSummary['missed'] .'<br>
                    <sm class="text-danger">Missed</sm>
                 </h2>
                </div> 
            </td>
            <td>
              <div class="lst-sub text-center p-t-20">
                    <span class="sm-font">Last Submission  <b>'. $patientSummary['lastSubmission'] .'</b></span><br>
                    <span class="sm-font">Next Submission  <b>'. $patientSummary['nextSubmission'] .'</b></span>
                 </div>
           </td>
           <td class="text-right sorting text-error"  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">                              
              '. $patientSummary['previousFlag']['red'] .'
           </td>
           <td class="text-center sorting text-warning"  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">                              
              '. $patientSummary['previousFlag']['amber'] .'
           </td>
           <td class="text-left sorting text-success"  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">
              '. $patientSummary['previousFlag']['green'] .'
           </td>

           <td class="text-right sorting text-error"  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">
              '. $patientSummary['baseLineFlag']['red'] .'
           </td>
           <td class="text-center sorting text-warning"  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">
              '. $patientSummary['baseLineFlag']['amber'] .'
           </td>
           <td class="text-left sorting text-success"  onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">
              '. $patientSummary['baseLineFlag']['green'] .'
           </td>
           <td   onclick="window.document.location=\'/'.$hospitalSlug.'/'.$projectSlug.'/patients/'.$patientId.' \';">
              <div class="chart-block" style="padding:28px">
                 <div id="chart_mini_'. $patientId .'" style="vertical-align: middle; display: inline-block; width: 130px; height: 35px;"></div>
              </div>
           </td>
           
        </tr>';

        }
        

        return response()->json([
                    'code' => 'data',
                    'data' => $str,
                    'patientIds' => $patientIds,
                    'miniChartData' => $miniChartData,
                        ], 200); 
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

    public function getProjectResponsesByDate($projectId,$page=0,$responseData,$startDate,$endDate,$status,$cond=[],$sort=[],$limit="")
    {
        $displayLimit = 90; 

        $responseQry = new ParseQuery("Response");
        $responseQry->containedIn("status",$status);  //["completed","late","missed"]
        if(!empty($cond))
        {
            foreach ($cond as $key => $value) {
                $responseQry->equalTo($key,$value);
            }
        }
        
        $responseQry->equalTo("project",$projectId);
        $responseQry->greaterThanOrEqualTo("occurrenceDate",$startDate);
        $responseQry->lessThanOrEqualTo("occurrenceDate",$endDate);

        if($limit!="")
        {
            $responseQry->limit($limit);
        }
        else
        {
            $responseQry->limit($displayLimit);
            $responseQry->skip($page * $displayLimit);
        }
        
        if(!empty($sort))
        {
            foreach ($sort as $key => $value) {
                if($key=='asc')
                    $responseQry->ascending($value);
                else
                    $responseQry->descending($value);
            }

        }
        else
        {
            $responseQry->descending("createdAt","sequenceNumber");
        }

        $responses = $responseQry->find();

        $responseData = array_merge($responseData,$responses); 
         
        if(!empty($responses) && $limit=="")
        { 
            $page++;
            $responseData = $this->getProjectResponsesByDate($projectId,$page,$responseData ,$startDate,$endDate,$status,$cond,$sort,$limit);
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
                // $patientSubmissions[]=$response;
            }
            elseif ($responseStatus=='missed') {
                $missedResponses[]= $responseId;
            }

            if($reviewed=='unreviewed' && $responseStatus!='missed')
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

        $submissionCountData = getSubmissionCountData($totalResponses, $data['missedCount'], $data['completedCount'], $data['lateCount']);

        $data['completed'] =  $submissionCountData['completed'];
        $data['missed'] =  $submissionCountData['missed'];
        $data['late'] =  $submissionCountData['late'];
        $data['pieChartData'] = $submissionCountData['pieChartData'];


        $data['redBaseLine'] = (isset($redFlags['baseLine']))?array_sum($redFlags['baseLine']):0;
        $data['redPrevious'] = (isset($redFlags['previous']))?array_sum($redFlags['previous']):0;
        $data['amberBaseLine'] = (isset($amberFlags['baseLine']))?array_sum($amberFlags['baseLine']):0;
        $data['amberPrevious'] = (isset($amberFlags['previous']))?array_sum($amberFlags['previous']):0;
        $data['unreviewedSubmission'] = count($unreviewed);
        $data['patientSubmissions'] = $patientSubmissions;
        $data['totalSubmissionCount'] = $totalResponses;

        return $data;
    }

    public function projectFlagsChart($patientResponses)
    {

        $redFlagsByDate = [];
        $amberFlagsByDate = [];
        // $greenFlagsByDate = [];
        $unreviewedSubmissionByDate = [];
        $submissionByDate = [];
        $missedByDate = [];
        $completedByDate = [];
        $lateByDate = [];
        $submissionsNumberByDate=[];
       
        foreach ($patientResponses as $response) {
            $responseId = $response->getObjectId();
            
            $reviewed = $response->get("reviewed");
            $sequenceNumber = $response->get("sequenceNumber");
            $occurrenceDate = $response->get("occurrenceDate")->format('d-m-Y');
            $occurrenceDate = strtotime($occurrenceDate);


            $responseStatus = $response->get("status");

            if ($responseStatus=='completed') {
                $completedByDate[$occurrenceDate][]= $responseId;
            }
            elseif ($responseStatus=='late') {
                $lateByDate[$occurrenceDate][]= $responseId;
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

        $i=0;
        foreach($unreviewedSubmissionByDate as $date => $value)
        { 
            $submissionByDate[$i]["Date"] =  date('d M',$date);
            $submissionByDate[$i]["completed"] = (isset($completedByDate[$date]))?count($completedByDate[$date]):0;
            $submissionByDate[$i]["late"] = (isset($lateByDate[$date]))?count($lateByDate[$date]):0;
            $submissionByDate[$i]["missed"] = (isset($missedByDate[$date]))?count($missedByDate[$date]):0;
 
            $i++;
        }
       
     
        $data['redFlags'] = json_encode($redFlagData);
        $data['amberFlags'] = json_encode($amberFlagData);
        $data['greenFlags'] = json_encode($greenFlagData); 
        $data['unreviewedSubmission'] = json_encode($unreviewedData);
        $data['patientsSubmission'] = json_encode($submissionByDate);
        
        
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
            $alert = $response->get("alert");

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
            $submissionsData[$responseId]['alert'] = ($alert)?'Yes':'No';
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
        try
        {

          $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

          $hospital = $hospitalProjectData['hospital'];
          $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];


          $project = $hospitalProjectData['project'];
          $projectId = intval($project['id']);

          $inputs = Input::get();
          $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('today - '.DATE_DIFFERENCE.' days'));
          $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');


          $startDateYmd = date('Y-m-d', strtotime($startDate));
          $endDateYmd = date('Y-m-d', strtotime($endDate .'+1 day'));

          $startDateObj = array(
                    "__type" => "Date",
                    "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                   );

          $endDateObj = array(
                        "__type" => "Date",
                        "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                       );

          $referenceCode = (isset($inputs['referenceCode']))?$inputs['referenceCode']:0;
          $allPatients = User::where('type','patient')->where('hospital_id',$hospital['id'])->where('project_id',$project['id'])->lists('reference_code')->toArray();

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
              $sequenceNumber = $response->get("sequenceNumber");

              $responseArr[$responseId]['DATE'] = $response->get("occurrenceDate")->format('d M');
              $responseArr[$responseId]['SUBMISSIONNO'] = $response->get("sequenceNumber");

              if ($responseStatus=='completed') {
                  $completedResponses[]= $response;
                  $patientSubmissions[] = $response;
              }
              elseif ($responseStatus=='late') {
                  $lateResponses[]= $response;
                  //$patientSubmissions[] = $response;
              }
              elseif ($responseStatus=='missed') {
                  $missedResponses[]= $response;
              }

              $occurrenceDate = $response->get("occurrenceDate")->format('d-m-Y h:i:s');
              $occurrenceDate = strtotime($occurrenceDate);
              $responseByDate[$sequenceNumber] = $responseId;
          } 

          ksort($responseByDate);
          $patientSubmissionsByDate = [];
          foreach ($responseByDate as $sequenceNumber => $responseId) {
              $patientSubmissionsByDate[$responseId] = $responseArr[$responseId];
          }

          $totalResponses = count($patientResponses);
          $responseRate['completedCount'] = count($completedResponses);
          $responseRate['missedCount'] = count($missedResponses);
          $responseRate['lateCount'] = count($lateResponses);

          $submissionCountData = getSubmissionCountData($totalResponses, $responseRate['missedCount'], $responseRate['completedCount'], $responseRate['lateCount']);

          $responseRate['completed'] =  $submissionCountData['completed'];
          $responseRate['missed'] =  $submissionCountData['missed'];
          $responseRate['late'] =  $submissionCountData['late'];
          $responseRate['pieChartData'] = $submissionCountData['pieChartData'];

          $baselineAnwers = $patientController->getPatientBaseLine($referenceCode);
          $allBaselineAnwers = $patientController->getAllPatientBaseLine($referenceCode);

         
          //get patient answers
          $patientAnswers = $patientController->getPatientAnwersByDate($referenceCode,$projectId,0,[],$startDateObj,$endDateObj);
          

           //flags chart (total,red,amber,green)  
          $flagsCount = $patientController->patientFlagsCount($patientSubmissions,$baselineAnwers);    

          //health chart
          $healthChart = $patientController->healthChartData($patientAnswers);
          $submissionFlags = $healthChart['submissionFlags'];
          $flagsQuestions = $healthChart['questionLabel'];

          //question chart
          $questionsChartData = $patientController->getQuestionChartData($patientAnswers);
          $questionLabels = $questionsChartData['questionLabels'];
          $questionChartData = $questionsChartData['chartData'];

          $patientSubmissionChart = $patientController->getPatientSubmissionChart($patientAnswers,$allBaselineAnwers);
          $submissionChart = $patientSubmissionChart['submissionChart'] ;
          $submissionNumbers = $patientSubmissionChart['submissions'] ;
          $firstSubmission = (!empty($submissionNumbers)) ? current($submissionNumbers) :'';

        } catch (\Exception $e) {

            Log::error($e->getMessage());
            abort(404);         
        }

        return view('project.reports')->with('active_menu', 'reports')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('referenceCode', $referenceCode)
                                        ->with('totalResponses', $totalResponses)
                                        ->with('responseRate', $responseRate)
                                        ->with('responseArr', $patientSubmissionsByDate)
                                        ->with('flagsQuestions', $flagsQuestions)
                                        ->with('flagsCount', $flagsCount)
                                        ->with('submissionFlags', $submissionFlags)            
                                        ->with('questionLabels', $questionLabels)
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate)
                                        ->with('allPatients', $allPatients)
                                        ->with('firstSubmission', $firstSubmission)
                                        ->with('submissionChart', $submissionChart)
                                        ->with('submissionNumbers', $submissionNumbers)
                                        ->with('questionChartData', $questionChartData);


    }

    public function getNotifications($hospitalSlug,$projectSlug)
    {
        try
        {
          $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

          $hospital = $hospitalProjectData['hospital'];
          $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

          $project = $hospitalProjectData['project'];
          $projectId = intval($project['id']);
 
          $inputs = Input::get();
          $startDate = (isset($inputs['startDate']))?$inputs['startDate']:date('d-m-Y', strtotime('today - '.DATE_DIFFERENCE.' days'));
          $endDate = (isset($inputs['endDate']))?$inputs['endDate']: date('d-m-Y');
 
          $startDateYmd = date('Y-m-d', strtotime($startDate));
          $endDateYmd = date('Y-m-d', strtotime($endDate .'+1 day'));

          $startDateObj = array(
                    "__type" => "Date",
                    "iso" => date('Y-m-d\TH:i:s.u', strtotime($startDate))
                   );

          $endDateObj = array(
                        "__type" => "Date",
                        "iso" => date('Y-m-d\TH:i:s.u', strtotime($endDate .'+1 day'))
                       );

          $dateCond=['startDate'=>$startDateObj,'endDate'=>$endDateObj];

          $prejectAlerts = $this->getProjectAlerts($projectId,"",0,$dateCond);

        } catch (\Exception $e) {

            Log::error($e->getMessage());
            abort(404);         
        }

        return view('project.notifications')->with('active_menu', '')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('prejectAlerts', $prejectAlerts)
                                        
                                        ->with('endDate', $endDate)
                                        ->with('startDate', $startDate);
        
    }

    public function questionnaireSetting($hospitalSlug,$projectSlug)
    {
        try
        {
          $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

          $hospital = $hospitalProjectData['hospital'];
          $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

          $project = $hospitalProjectData['project'];
          $projectId = intval($project['id']);

          $questionnaireQry = new ParseQuery("Questionnaire");
          $questionnaireQry->equalTo("project",$projectId);
          $questionnaire = $questionnaireQry->first();

          $questionnaireId ="";
          $settings =[];
          $settings['frequency']['day'] = ''; 
          $settings['frequency']['hours'] = ''; 
          $settings['gracePeriod']['day'] = '';
          $settings['gracePeriod']['hours'] = '';
          $settings['reminderTime']['day'] = '';
          $settings['reminderTime']['hours'] = '';
          $settings['editable'] = '';
          $settings['type'] = ''; 
          $settings['pauseProject'] = '';  
          
          $action ="store-questionnaire-setting";
          if(!empty($questionnaire))
          {
            $gracePeriod = secondsToTime($questionnaire->get('gracePeriod'));
            $settings['gracePeriod']['day'] = $gracePeriod['d']; 
            $settings['gracePeriod']['hours'] = $gracePeriod['h']; 

            $reminderTime = secondsToTime($questionnaire->get('reminderTime'));
            $settings['reminderTime']['day'] = $reminderTime['d']; 
            $settings['reminderTime']['hours'] = $reminderTime['h']; 

            $settings['editable'] = $questionnaire->get('editable');
            $settings['type'] = $questionnaire->get('type');
            $settings['pauseProject'] = ($questionnaire->get('pauseProject')==true)?'yes':'no';

            $scheduleQry = new ParseQuery("Schedule");
            $scheduleQry->equalTo("questionnaire",$questionnaire);
            $scheduleQry->doesNotExist("patient");
            $schedule = $scheduleQry->first();
            
            if(!empty($schedule))
            {
     
              $frequency = secondsToTime($schedule->get('frequency'));
              $settings['frequency']['day'] = $frequency['d']; 
              $settings['frequency']['hours'] = $frequency['h']; 
            }
            $action ="update-questionnaire-setting";
            $questionnaireId = $questionnaire->getObjectId();

          }
       
        
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            abort(404);   
        }      

        

        return view('project.questionnaire-setting')->with('active_menu', 'settings')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('action', $action)
                                        ->with('questionnaireId', $questionnaireId)
                                        ->with('settings', $settings);
    }

    public function storeQuestionnaireSetting(Request $request,$hospitalSlug,$projectSlug)
    {

        try
        {
            $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

            $hospital = $hospitalProjectData['hospital'];
            $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

            $project = $hospitalProjectData['project'];
            $projectId = intval($project['id']);

            $frequencyDay = $request->input('frequencyDay');   
            $frequencyHours = $request->input('frequencyHours');  
            $gracePeriodDay = $request->input('gracePeriodDay');
            $gracePeriodHours = $request->input('gracePeriodHours');
            $reminderTimeDay = $request->input('reminderTimeDay');
            $reminderTimeHours = $request->input('reminderTimeHours');

            $frequency = strval(convertToSeconds($frequencyDay,$frequencyHours));   
            $gracePeriod = intval(convertToSeconds($gracePeriodDay,$gracePeriodHours));   
            $reminderTime = intval(convertToSeconds($reminderTimeDay,$reminderTimeHours));   

            $editable = ($request->input('editable')=='yes')?true:false;
            $pauseProject = ($request->input('pauseProject')=='yes')?true:false;
            $type = $request->input('type');

            $project->project_status = ($request->input('pauseProject')=='yes')?"paused":"active";
            $project->save();

            $questionnaire = new ParseObject("Questionnaire");
            $questionnaire->set("project",$projectId);
            $questionnaire->set('gracePeriod',$gracePeriod);
            $questionnaire->set('reminderTime',$reminderTime);
            $questionnaire->set('editable',$editable);
            $questionnaire->set('pauseProject',$pauseProject);
            $questionnaire->set('type',$type);
            $questionnaire->save();

     
            $schedule = new ParseObject("Schedule");
            $schedule->set("questionnaire", $questionnaire);
            $schedule->set("frequency", $frequency);
            $schedule->save();

            Session::flash('success_message','Project settings successfully created.');
            
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            abort(404);         
        }
        return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
    }

    public function saveQuestionnaireSetting(Request $request,$hospitalSlug,$projectSlug)
    {

        try
        {
            $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

            $hospital = $hospitalProjectData['hospital'];
            $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

            $project = $hospitalProjectData['project'];
            $projectId = intval($project['id']);

            $frequencyDay = $request->input('frequencyDay');   
            $frequencyHours = $request->input('frequencyHours');  
            $gracePeriodDay = $request->input('gracePeriodDay');
            $gracePeriodHours = $request->input('gracePeriodHours');
            $reminderTimeDay = $request->input('reminderTimeDay');
            $reminderTimeHours = $request->input('reminderTimeHours');

            $frequency = strval(convertToSeconds($frequencyDay,$frequencyHours));   
            $gracePeriod = intval(convertToSeconds($gracePeriodDay,$gracePeriodHours));   
            $reminderTime = intval(convertToSeconds($reminderTimeDay,$reminderTimeHours));   

            $editable = ($request->input('editable')=='yes')?true:false;
            $pauseProject = ($request->input('pauseProject')=='yes')?true:false;
            $type = $request->input('type');

            $project->project_status = ($request->input('pauseProject')=='yes')?"paused":"active";
            $project->save();

            $questionnaireQry = new ParseQuery("Questionnaire");
            $questionnaireQry->equalTo("project",$projectId);
            $questionnaire = $questionnaireQry->first();

            $questionnaire->set('gracePeriod',$gracePeriod);
            $questionnaire->set('reminderTime',$reminderTime);
            $questionnaire->set('editable',$editable);
            $questionnaire->set('pauseProject',$pauseProject);
            $questionnaire->set('type',$type);
            $questionnaire->save();

            $scheduleQry = new ParseQuery("Schedule");
            $scheduleQry->equalTo("questionnaire",$questionnaire);
            $scheduleQry->doesNotExist("patient");
            $schedule = $scheduleQry->first();

            if(empty($schedule))
            {
              $schedule = new ParseObject("Schedule");
              $schedule->set("questionnaire", $questionnaire);
            }

            $schedule->set("frequency", $frequency);
            $schedule->save();

            Session::flash('success_message','Project settings successfully updated.');
            
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            abort(404);         
        }
        return redirect(url($hospitalSlug .'/'. $projectSlug .'/questionnaire-setting')); 
    }

    public function configureQuestions($hospitalSlug,$projectSlug,$questionnaireId)
    {
        // try
        // {
          $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

          $hospital = $hospitalProjectData['hospital'];
          $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

          $project = $hospitalProjectData['project'];
          $projectId = intval($project['id']);

          $questionnaireObj = new ParseQuery("Questionnaire");
          $questionnaire = $questionnaireObj->get($questionnaireId);

          $questionObjs = new ParseQuery("Questions");
          $questionObjs->equalTo("questionnaire",$questionnaire);
          $questionObjs->ascending("createdAt");
          $questions = $questionObjs->find();

          $questionsList = [];
          foreach ($questions as $question) {
            $questionTxt = $question->get("question");
            $title = $question->get("title");
            $type = $question->get("type");
            $questionId = $question->getObjectId();

            $questionsList[$questionId] = ['question'=>$questionTxt, 'title'=>$title, 'type'=>$type];         
          }

          $optionObjs = new ParseQuery("Options");
          $optionObjs->containedIn("question",$questions);
          $optionObjs->ascending("score");
          $options = $optionObjs->find();

          $optionsList = [];
          foreach ($options as $option) {
            $label = $option->get("label");
            $score = $option->get("score");
            $optionId = $option->getObjectId();
            $questionId = $option->get("question")->getObjectId();
          
            $optionsList[$questionId][] = ['optionId'=>$optionId, 'score'=>$score, 'label'=>$label];         
          }

           
        
        // } catch (\Exception $e) {
        //     Log::error($e->getMessage());
        //     abort(404);   
        // }      

        

        return view('project.configure-questions')->with('active_menu', 'settings')
                                        ->with('questionnaireId', $questionnaireId)
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('optionsList', $optionsList)
                                        ->with('questionsList', $questionsList);
    }

    public function StoreQuestions(Request $request,$hospitalSlug,$projectSlug,$questionnaireId)
    {

       // dd($request->all());
      $questionsType = $request->input("questionType");
      $titles = $request->input("title");
      $questions = $request->input("question");
      $questionIds = $request->input("questionId");
      $options = $request->input("option");
      $optionIds = $request->input("optionId");
      $scores = $request->input("score");

      $questionnaireObj = new ParseQuery("Questionnaire");
      $questionnaire = $questionnaireObj->get($questionnaireId);

      foreach ($questionsType as $key => $questionType) {
          
          if($questionType=="")
            continue;

          $title = $titles[$key];
          $question = $questions[$key];
          $questionId = $questionIds[$key];
          $isChild = false;

          if($questionId !="")
          {
            $questionObject = new ParseQuery("Questions");
            $questionObj = $questionObject->get($questionId);
          }
          else
          {
            $questionObj = new ParseObject("Questions");
            $questionObj->set('questionnaire',$questionnaire);
          }
          
          $questionObj->set("question",$question);
          $questionObj->set('title',$title);
          $questionObj->set('isChild',$isChild);
          $questionObj->set('type',$questionType);
          $questionObj->save();
           

          if(isset($options[$key]))
          { 
            $questionOptions = $options[$key]; 
            $optionScores = $scores[$key];
            $optionId = $optionIds[$key];

            foreach ($questionOptions as $k => $option) {
              if($option=="")
                continue;

              if($optionId[$k] !="")
              {
                $optionObject = new ParseQuery("Options");
                $optionObj = $optionObject->get($optionId[$k]);
              }
              else
              {
                $optionObj = new ParseObject("Options");
                $optionObj->set("question",$questionObj);
              }

              $score = intval($optionScores[$k]);
              
              $optionObj->set('score',$score);
              $optionObj->set('label',$option);
              $optionObj->save();
            }
          }
          
      }

      return redirect(url($hospitalSlug .'/'. $projectSlug .'/configure-questions/'.$questionnaireId)); 

    } 

    public function deleteQuestion($hospitalSlug,$projectSlug,$questionId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $questionObj = new ParseQuery("Questions");
        $question = $questionObj->get($questionId);
        $question->destroy();

        return response()->json([
                    'code' => 'delete_question',
                    'message' => "question deleted",
                        ], 203);
    }

    public function deleteOption($hospitalSlug,$projectSlug,$optionId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $optionObject = new ParseQuery("Options");
        $optionObj = $optionObject->get($optionId);
        $optionObj->destroy();

        return response()->json([
                    'code' => 'delete_option',
                    'message' => "option deleted",
                        ], 203);
    }

    public function clearCache($projectId)
    {
        $responseCacheKey = "projectResponses_".$projectId;
        $patientsSummaryCacheKey = "patientsSummary_".$projectId;
        $patientsAlertsCacheKey = "patientsAlerts_".$projectId;

        Cache::forget($responseCacheKey);
        Cache::forget($patientsSummaryCacheKey);
        Cache::forget($patientsAlertsCacheKey);

        $json_resp = array(
                'code' => 'cache_cleared' , 
                'message' => 'Cache cleared'
                );
          $status_code = 200;        
        
         return response()->json( $json_resp, $status_code);  
    }

    public function flushCacheMemory()
    {
        Cache::flush();

        return response()->json([
                    'code' => 'cache_cleared',
                    'message' => "cache cleared",
                        ], 200);
    }

    public function alertSetting($hospitalSlug,$projectSlug)
    {
      try
        {
          $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

          $hospital = $hospitalProjectData['hospital'];
          $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

          $project = $hospitalProjectData['project'];
          $projectId = intval($project['id']);

          $alertsettingsQry = new ParseQuery("AlertSettings");
          $alertsettingsQry->equalTo("project",$projectId);
          $alertsettings = $alertsettingsQry->find();

          $settings =[];
          foreach ($alertsettings as $alertsetting) {
             $settings[] =['id'=>$alertsetting->getObjectId(),
                           'flagCount'=>$alertsetting->get("flagCount"),
                           'operation'=>$alertsetting->get("operation"),
                           'flagColour'=>$alertsetting->get("flagColour"),
                           'comparedTo'=>$alertsetting->get("comparedTo"),
                          ];
          }

           
        
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            abort(404);         
        }

        return view('project.alert-settings')->with('active_menu', 'settings')
                                        ->with('hospital', $hospital)
                                        ->with('project', $project)
                                        ->with('settings', $settings);
    }

    public function saveAlertSetting(Request $request,$hospitalSlug,$projectSlug)
    {

        try
        {
            $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

            $hospital = $hospitalProjectData['hospital'];
            $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

            $project = $hospitalProjectData['project'];
            $projectId = intval($project['id']);

 
            $flagCount = $request->input('flag_count');   
            $operation = $request->input('operation');  
            $flagColour = $request->input('flag_colour');
            $comparedTo = $request->input('compared_to');
            $settingsIds = $request->input('setting_id');
 
            foreach ($flagCount as $key => $value) {
              $settingsId = $settingsIds[$key];
              $flagCountVal = intval($value);
              $operationVal = $operation[$key];
              $flagColourVal = $flagColour[$key];
              $comparedToVal = $comparedTo[$key];

              if($value=='')
                continue;

              if($settingsId=='')
              {  
                $alertsetting = new ParseObject("AlertSettings");
                $alertsetting->set("project", $projectId);
                $alertsetting->set("flagCount", $flagCountVal);
                $alertsetting->set("operation", $operationVal);
                $alertsetting->set("flagColour", $flagColourVal);
                $alertsetting->set("comparedTo", $comparedToVal);
                $alertsetting->save();

             }
              else
              {
                $alertsettingObj = new ParseQuery("AlertSettings");
                $alertsetting = $alertsettingObj->get($settingsId);
                $alertsetting->set("flagCount", $flagCountVal);
                $alertsetting->set("operation", $operationVal);
                $alertsetting->set("flagColour", $flagColourVal);
                $alertsetting->set("comparedTo", $comparedToVal);
                $alertsetting->save();
              }
       
              
            }
             

            Session::flash('success_message','Alert settings successfully updated.');
            
        } catch (\Exception $e) {
            
            Log::error($e->getMessage());
            abort(404);         
        }
        return redirect(url($hospitalSlug .'/'. $projectSlug .'/alert-setting')); 
    }

    public function deleteAlertSettings($hospitalSlug,$projectSlug,$settingsId)
    {
        $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);

        $hospital = $hospitalProjectData['hospital'];
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $project = $hospitalProjectData['project'];
        $projectId = intval($project['id']);

        $alertsettingObj = new ParseQuery("AlertSettings");
        $alertsetting = $alertsettingObj->get($settingsId);
        $alertsetting->destroy();

        return response()->json([
                    'code' => 'delete_settings',
                    'message' => "Settings deleted",
                        ], 203);


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
