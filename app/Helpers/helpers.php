<?php
 
/**
 * 
 * @param type $type_id
 * @return string
 */

//patient status array
// $patientStatus = ['created'=>'created','active'=>'Active','suspended'=>'Suspended'];
// $userTypes = ['mylan_admin'=>'Mylan Admin','internal_user'=>'Internal User','pateints'=>'Patient'];
function getRoles()
{
	$roles = ['edit'=>'Edit','view'=>'View'];
	return $roles;
}


//constatnts
define("SETUP_LIMIT", 10);
define("SETUP_ALERT", 5);
define("DATE_DIFFERENCE", 30);

function getUserApiKey( $userId ) {

    $key = App\ApiKeys::where('user_id',$userId)->get()->pluck('key');
         
    return $key[0];
} 

function updateLoginAttemptforUser($user)
{
    $cookieName = \Cookie::get("laravel_session");

    $userLoginAttempt = App\UserLoginAttempt::where('user_token',$cookieName)->where('user',$user)->first();
   
    if($userLoginAttempt!=null)
    {
        $loginAttempt = $userLoginAttempt->login_attempts;
        $userLoginAttempt->login_attempts = $loginAttempt+1;
        $userLoginAttempt->save();
    }
    else
    {
        $userLoginAttempt =  new App\UserLoginAttempt();
        $userLoginAttempt->user_token = $cookieName;
        $userLoginAttempt->user = $user;
        $userLoginAttempt->login_attempts = 1;
        $userLoginAttempt->save();
    }
    
 
    return $userLoginAttempt;
}

function getUserLoginAttempts($user)
{
    $cookieName = \Cookie::get("laravel_session");
    $loginAttempt = App\UserLoginAttempt::where('user_token',$cookieName)->where('user',$user)->first(); 
    $loginAttemptCount =  (!empty($loginAttempt))?intval($loginAttempt->login_attempts):0; 
 
    return $loginAttemptCount;
}

function getProjectAttributes($attributes)
{
    $data = [];
    foreach ($attributes as $key => $attribute) {
        $data[$attribute['label']][]= ['control_type'=>$attribute['control_type'], 'values'=>$attribute['values'], 'label'=>$attribute['label']];
    }

    return $data;
}  

function getSubmissionCountData($totalSubmission, $missedCount, $completedCount, $lateCount)
{
    $data = [];

    $completed = ($totalSubmission) ? ($completedCount/$totalSubmission) * 100 :0;
    $data['completed'] =  round($completed);

    $missed = ($totalSubmission) ? ($missedCount/$totalSubmission) * 100 :0;
    $data['missed'] =  round($missed);

    $late = ($totalSubmission) ? ($lateCount/$totalSubmission) * 100 :0;
    $data['late'] =  round($late);

    $pieChartData=[];
    if($totalSubmission)
    {
      $pieChartData[] = ["title"=> "# Missed","value"=>$missedCount];
      $pieChartData[] = ["title"=> "# Completed","value"=>$completedCount];
      $pieChartData[] = ["title"=> "# late","value"=>$lateCount];
    }

    $data['pieChartData'] = json_encode($pieChartData);

    return $data;
} 

function createSetupAlert($referenceCode,$setupCount,$project)
{
    $response = "alert_not_created";
    if($setupCount == SETUP_ALERT)
    {
        $alert = new Parse\ParseObject("Alerts");
        $alert->set("project", $project);
        $alert->set("patient", $referenceCode);
        $alert->set("alertType", "device_setup_alert");
        $alert->set("referenceType", "patient");
        $alert->set("cleared",false);
        $alert->save(); 
        $response = "alert_created";
    }

    return $response;
}

function getQuestionnaireData($projectId)
{
    $projectId = intval($projectId); 
    $questionnaireQry = new Parse\ParseQuery("Questionnaire");
    $questionnaireQry->equalTo("project",$projectId);
    $questionnaire = $questionnaireQry->first();

    $data=[];

    if(!empty($questionnaire))
    {
        $data['questionnaireId'] = $questionnaire->getObjectId();
        $data['status'] = $questionnaire->get('status');
    }
    

    return $data;
}

// function secondsToTime($inputSeconds) {

//     $secondsInAMinute = 60;
//     $secondsInAnHour  = 60 * $secondsInAMinute;
//     $secondsInADay    = 24 * $secondsInAnHour;

//     // extract days
//     $days = floor($inputSeconds / $secondsInADay);

//     // extract hours
//     $hourSeconds = $inputSeconds % $secondsInADay;
//     $hours = floor($hourSeconds / $secondsInAnHour);

//     // extract minutes
//     $minuteSeconds = $hourSeconds % $secondsInAnHour;
//     $minutes = floor($minuteSeconds / $secondsInAMinute);

//     // extract the remaining seconds
//     $remainingSeconds = $minuteSeconds % $secondsInAMinute;
//     $seconds = ceil($remainingSeconds);

//     // return the final array
//     $obj = array(
//         'd' => (int) $days,
//         'h' => (int) $hours,
//         'm' => (int) $minutes,
//         's' => (int) $seconds,
//     );
//     return $obj;
// }

function secondsToTime($inputSeconds) {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
 

    // return the final array
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
    
    );
    return $obj;
}

function convertToSeconds($days,$hours)
{
    $dayToseconds = $days * 86400;
    $hoursToseconds = $hours * 3600;
    $seconds = $dayToseconds + $hoursToseconds;
    
    return $seconds;
}

function convertStonePoundsToKgs($stones,$pounds)
{
    $value1 = $pounds / 2.2; 

    $pounds = $stones * 14; 

    $value2 = $pounds / 2.2; 

    $result = $value1 + $value2;     

    return round($result);   
}

function getInputValues($values,$withLabel=true)
{
    /*********
    $array['kg']=50
    $array=['st'=>50,'lb'=>30];

    /******/
    // if(count($values)==1)
    // {
    //     $result = current($values).' ';
    //     $result .= ($withLabel)? key($values) :'';
    // }
    // else
    // {
    //     if(isset($values['st']) && isset($values['lb']))
    //     {
    //         $result = convertStonePoundsToKgs($values['st'],$values['lb']) ;
    //         $result .= ($withLabel)? ' kg' :'';
    //     }
    //     else
    //     {
    //         $result = current($values).' ';
    //         $result .= ($withLabel)? key($values) :'';
    //     }
        
    // }


    if(isset($values['st']) || isset($values['lb']))
    {
        $stones = (isset($values['st'])) ? $values['st']:0;
        $pounds = (isset($values['lb'])) ? $values['lb']:0;

        $result = convertStonePoundsToKgs($stones,$pounds) ;
        $result .= ($withLabel)? ' kg' :'';
    }
    else
    {
        $result = current($values).' ';
        $result .= ($withLabel)? key($values) :'';
    }
        
    return $result;
} 

function getPassword($referenceCode , $password)
{
	$referenceCodePart1 = mb_substr($referenceCode, 0, 4);
	$referenceCodePart2 = mb_substr($referenceCode, 4, 7);

	$newPassword =  $referenceCodePart2.$password.$referenceCodePart1;

	return $newPassword;
}

function randomPassword() {
	$chars = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
	$password = substr( str_shuffle( $chars ), 0, 6 );
	return $password;
}

function randomPatientPassword() {
    $chars = "1234567890";
    $password = substr( str_shuffle( $chars ), 0, 4 );
    return $password;
}

function randomUserResetPassword() {
    $chars = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $password = substr( str_shuffle( $chars ), 0, 7 );
    return $password;
}



// function hasMylanPermission($userPermission)
// {  
//     $userId =  Auth::user()->id;
//     $user = App\User::find($userId); 
//     $userType = $user->type; 
//     $hasAccess = $user->mylan_access; 
    
//     $flag = false;
    
//     $permissions =[];
//  	$userAccess = [];
 	
//     if($userType=='mylan_admin')
//     {
//         if($hasAccess=='no')      //GET ROLES ONLY FOR THE PROJECT
//         {
//             $userAccess = $user->access()->where(['object_type'=>'mylan', 'object_id'=>0])->whereIn('access_type',$userPermission)->get()->toArray();
        	
//         	if(!empty($userAccess))
//     	        $flag = true;
//         }
//         else
//         	$flag = true;
//     }
    
//     return $flag;
// }

function hasHospitalPermission($hospitalSlug,$userPermission)
{  
    $userId =  Auth::user()->id;
    $user = App\User::find($userId); 
    $hasAccess = $user->has_all_access; 
    $userType =  $user->type; 

    
    $flag = false;
    
    $permissions =[];
    $userAccess = [];

    $hospital = App\Hospital::where('url_slug',$hospitalSlug)->first()->toArray(); 
     

    if($userType=='mylan_admin' || $userType=='hospital_user')
    {
        if($hasAccess=='no')      //GET ROLES ONLY FOR THE PROJECT
        {
            $userAccess = $user->access()->where(['object_type'=>'hospital', 'object_id'=>$hospital['id']])->whereIn('access_type',$userPermission)->get()->toArray();

            
            if(!empty($userAccess))
                $flag = true;
        }
        else
            $flag = true;
    }
    
    return $flag;
}

function hasMylanPermission()
{  
    $userId =  Auth::user()->id;
    $userType = Auth::user()->type; 

    $flag = false;
    
    if($userType=='mylan_admin')
    {
        $flag = true;
    }
    
    return $flag;
}


function userType()
{  
    $userType = Auth::user()->type; 

     $type = '';

    if($userType=='mylan_admin')
        $type = "Mylan Admin";
    elseif($userType=='hospital_user')
        $type = "Hospital Admin";
    elseif($userType=='project_user')
        $type = "Project Admin";
 
    
    return $type;
}


function hasProjectPermission($hospitalSlug,$projectSlug,$userPermission)
{  
    $userId =  Auth::user()->id;
    $user = App\User::find($userId); 
    $hasAccess = $user->has_all_access; 
    $userType =  $user->type; 
    $hospitalId =  Auth::user()->hospital_id;
    
    $flag = false;
    
    $permissions =[];
    $userAccess = [];

    $hospitalProjectData = verifyProjectSlug($hospitalSlug ,$projectSlug);
     

    $hospital = $hospitalProjectData['hospital'];
    $project = $hospitalProjectData['project']; 
    

    if($userType=='mylan_admin' || $userType=='hospital_user' || $userType=='project_user') // && $hospitalId==$hospital['id']
    {
        if($hasAccess=='no')      //GET ROLES ONLY FOR THE PROJECT
        {
            if($userType=='hospital_user')
            {
                $userAccess = $user->access()->where(['object_type'=>'hospital', 'object_id'=>$hospital['id']])->whereIn('access_type',$userPermission)->get()->toArray();

            }
            elseif($userType=='project_user')
            {
                $userAccess = $user->access()->where(['object_type'=>'project', 'object_id'=>$project['id']])->whereIn('access_type',$userPermission)->get()->toArray();
            }
            
            if(!empty($userAccess))
                $flag = true;
        }
        else
            $flag = true;

        if($userType=='project_user' && $hospitalId!=$hospital['id'])
            $flag = false;
    }
 
    return $flag;
}

function hospitalImageExist($hospital,$flag=true)
{ 
    $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
    $filepath = public_path(). "/mylan/hospitals/".$hospital['logo']; 

    if($hospital['logo']!='' && file_exists($filepath))
    {
        $logo = '<img src="'.$logoUrl.'" class="logo" alt=""  data-src="'.$logoUrl.'" data-src-retina="'.$logoUrl.'" width="auto" height="40"/> <h3 class="inline hospital-name test">'.$hospital['name'].'</h3>';
        if($flag)
            $logo .='<h3 class="inline"><span class="text-muted side-pipe">|</span></h3>';
    }
    else
    {
        $logo = '<h3 class="inline hospital-name test">'.$hospital['name'];
        if($flag)
            $logo .= '<span class="text-muted side-pipe">|</span>';
        $logo .= '</h3>';
    }

    echo $logo;  
}

function verifyProjectSlug($hospitalSlug ,$projectSlug)
{
    $hospital = App\Hospital::where('url_slug',$hospitalSlug)->first();
    if(empty($hospital))
        abort(404); 

    $project = App\Projects::where('project_slug',$projectSlug)->where('hospital_id',$hospital['id'])->first(); 
    if(empty($project))
        abort(404);

    $data['hospital']=$hospital;
    $data['project']=$project;

    return $data;
}

function getStatusName($status)
{
    $reviewStatus = ['reviewed'=>'Reviewed','reviewed_no_action'=>'Reviewed - No action','reviewed_call_done'=>'Reviewed - Call done','reviewed_appointment_fixed'=>'Reviewed - Appointment fixed','unreviewed'=>'Unreviewed','completed'=>'Completed','late'=>'Late','missed'=>'Missed'];

    return (isset($reviewStatus[$status]))?$reviewStatus[$status] :'';
}

function flushCacheMemory()
{
    \Cache::flush();

    return "1";
}

//php.net
function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    }else{
        $bname = 'Other'; 
        $ub = "Other"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
} 
 
function exceptionError($error)
{
    if(env('APP_ENV')=="local")
    {
        dd($error);
    }
    else
    {
        \Log::error($error->getMessage());
        abort(404);
    }
}
 




