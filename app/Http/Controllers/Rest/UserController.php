<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Parse\ParseObject;
use Parse\ParseQuery;
use App\User;
use App\UserDevice;
use App\Hospital;
use App\Projects;
use \Mail;



class UserController extends Controller
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
    public function show($id)
    {
        //
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

    public function doSetup(Request $request)
    {
        try
        {
            $data = $request->all();  
            $referenceCode = strtolower($data['referenceCode']);
            $deviceIdentifier = $data['deviceIdentifier'];
            $user = User::where('type','patient')->where('reference_Code',$referenceCode)->first(); 
            
            if($user==null)
            {
                $json_resp = array(
                    'code' => 'invalid_reference_code' , 
                    'message' => 'reference code does not match',
                    'hospitalData' => array()
                    );
                $status_code = 200;
            }
            else
            {
                $userId = $user['id'];
                $hospitalId = $user['hospital_id'];
                $projectId = $user['project_id'];
                $postLoginData = $this->postLoginData($hospitalId,$projectId);
                $hospitalData = $postLoginData['hospital'];

                if($user['account_status'] =='created')
                {
                    //New setup
                     $this->addDevice($data,$userId,$hospitalData,'set_password');
                     $json_resp = array(
                        'code' => 'set_password' , 
                        'message' => 'New setup done',
                        'hospitalData' => $hospitalData
                        );
                        $status_code = 200;
                }
                elseif($user['account_status'] =='active')
                {

                    $userDevice = UserDevice::where(['user_id'=>$userId,'device_identifier'=>$deviceIdentifier,'status'=>'New device'])->get()->count(); 
                    if(!$userDevice)
                    {
                        $this->addDevice($data,$userId,$hospitalData,'do_login');
                    }

                    $json_resp = array(
                    'code' => 'do_login' , 
                    'message' => 'Device exist',
                    'hospitalData' => $hospitalData
                    );
                    $status_code = 200;
                }
                else
                {
                    $json_resp = array(
                        'code' => 'account_susspended' , 
                        'message' => 'patient account suspended',
                        'hospitalData' => $hospitalData
                        );
                        $status_code = 200;

                }
                 
                

            }
               
        } catch (Exception $ex) {

            $json_resp = array(
                'code' => 'Failed' , 
                'message' => 'Some error message'
                );
            $status_code = 404;        
        }
        

         return response()->json( $json_resp, $status_code);        

    }

    public function postLoginData($hospitalId , $projectId)
    {

        
        $projectId = intval ($projectId);
        $questionnaireQry = new ParseQuery("Questionnaire");
        $questionnaireQry->equalTo("project", $projectId);
        $questionnaire = $questionnaireQry->first(); 
        
        $hospital = Hospital::find($hospitalId)->toArray();  
        $project = Projects::find($projectId)->toArray(); 
        
        $logoUrl = ($hospital['logo']=='')?'':url() . "/mylan/hospitals/".$hospital['logo'];
        $filepath = public_path(). "/mylan/hospitals/".$hospital['logo'];

        if (!file_exists($filepath)) {
            $logoUrl = '';
        } 

        $data = $hospitalData = $questionnareData = [];
        $hospitalData['id'] = $hospital['id'];
        $hospitalData['name'] = $hospital['name'];
        $hospitalData['logoUrl'] = $logoUrl;
        $hospitalData['logo'] = $hospital['logo'];
        $hospitalData['phone'] = $hospital['phone'];
        $hospitalData['project_id'] = $project['id'];
        $hospitalData['project'] = $project['name'];
        $hospitalData['email'] = $hospital['email'];
        $hospitalData['address'] = $hospital['address_line_1'].','.$hospital['address_line_2'].','.$hospital['city'].','.$hospital['country'].','.$hospital['postal_code'];

        $questionnareData=[];
        if(!empty($questionnaire))
        {
            $questionnareData['id'] = $questionnaire->getObjectId();
            $questionnareData['name'] = $questionnaire->get('name');
            $questionnareData['description'] = $questionnaire->get('description');
        }
        

        $data['hospital'] = $hospitalData;
        $data['questionnaire'] = $questionnareData;
         
        return $data;
    }

 
    public function addDevice($deviceData,$userId,$hospitalData,$returnTo)
    {
        $userDevice =  new UserDevice();
        $userDevice->user_id = $userId;
        $userDevice->device_type = $deviceData['deviceType'];
        $userDevice->device_identifier = $deviceData['deviceIdentifier'];
        $userDevice->device_os = $deviceData['deviceOS'];
        $userDevice->access_type = $deviceData['accessType'];
        $userDevice->status = "New device";
        $userDevice->save();

        $json_resp = array(
                    'code' => $returnTo , 
                    'message' => 'Device exist',
                    'hospitalData' => $hospitalData
                    );

        return response()->json( $json_resp, 200);             
         
    }

    public function doLogin(Request $request)
    {
        try{
            $data = $request->all();  
            $referenceCode = strtolower($data['referenceCode']);
            $installationId = $data['installationId'];
            $password = trim($data['password']);
            $newpassword = getPassword($referenceCode , $password);
     
            $user = User::where('type','patient')->where('reference_Code',$referenceCode)->first();
             
            $userId = $user['id']; 
            
            $userDeviceCount = UserDevice::where('user_id',$userId)->where('status','New device')->get()->count();

            $project = Projects::find($user['project_id'])->toArray();  
            $loginAttempt = getUserLoginAttempts($referenceCode);

            if($loginAttempt >3)
            {
                $json_resp = array(
                    'code' => 'login_attempts' , 
                    'message' => 'Account Blocked, contact administrator'
                    );
                $status_code = 200;  
            }
            elseif($userDeviceCount >=SETUP_LIMIT)
            {
                $json_resp = array(
                    'code' => 'limit_exceeded' , 
                    'message' => 'cannot do setup more then '.SETUP_LIMIT.' times'
                    );
                    $status_code = 200;
            }
            elseif($project['project_status'] =="paused")
            {
                $json_resp = array(
                    'code' => 'project_paused' , 
                    'message' => 'Invalid Login details'
                    );
                $status_code = 200;
            }
            elseif($user==null)
            {
                $json_resp = array(
                    'code' => 'invalid_details' , 
                    'message' => 'In valid login details'
                    );
                $status_code = 200;
            }
            else
            {

                // $responseQry = new ParseQuery("Response");
                // $responseQry->equalTo("patient", $referenceCode); 
                // $responseQry->equalTo("status", 'base_line'); 
                // $response = $responseQry->first();
                if($user['baseline_set']=='no')
                {
                    $json_resp = array(
                        'code' => 'baseline_not_set' , 
                        'message' => 'Baseline not set for patient'
                        );
                        $status_code = 200;
                }
                elseif($user['account_status']=='created')
                {
                    $json_resp = array(
                        'code' => 'password_not_set' , 
                        'message' => 'Password not set for patient'
                        );
                        $status_code = 200;
                }
                elseif (Hash::check($newpassword, $user['password']) && $user['account_status']=='active')  
                {

                    $projectId = $user['project_id'];
                    $hospitalId = $user['hospital_id'];
                    $apiKey = $user->apiKey()->first()->key;

                    $parseUser = $this->getParseUser($referenceCode,$installationId,$apiKey);
                    if($parseUser!='error')
                    {

                        $data = $this -> postLoginData($hospitalId,$projectId);
                        $hospitalData = $data['hospital']; 
                        $questionnaireData = $data['questionnaire']; 
                        $parseUser =json_decode($parseUser,true); 

                        //if schedule not set for patient
                        /******************************/
                        if($parseUser['result']['scheduleFlag']==false)
                        {
                            $questionnaireObj = new ParseQuery("Questionnaire");
                            $questionnaire = $questionnaireObj->get($data['questionnaire']['id']);

                            $date = new \DateTime();
                            
                            $frequency = strval($user['frequency']);   
                            $gracePeriod = intval($user['grace_period']);   
                            $reminderTime = intval($user['reminder_time']);

                            $schedule = new ParseObject("Schedule");
                            $schedule->set("questionnaire", $questionnaire);
                            $schedule->set("patient", $referenceCode);
                            $schedule->set("startDate", $date);
                            $schedule->set("frequency",$frequency);
                            $schedule->set("gracePeriod",$gracePeriod);
                            $schedule->set("reminderTime",$reminderTime);
                            $schedule->set("nextOccurrence", $date);
                            $schedule->save();
                        }

                        /**************************/
                        $projectId = intval($projectId);
                        createSetupAlert($referenceCode,($userDeviceCount+1),$projectId);
                         
                        $json_resp = array( 
                            'user'=> $parseUser['result']['sessionToken'],
                            'hospital'=> $hospitalData,
                            'questionnaire'=> $questionnaireData,
                            'code' => 'successful_login' , 
                            'message' => 'Successfully logged in'
                        );

                        $status_code = 200;
                    }
                    else
                    {
                        $json_resp = array(
                        'code' => 'invalid_login' , 
                        'message' => 'Invalid Login details'
                        );
                        $status_code = 200;
                    }
                    
                }
                else
                {
                    
                    updateLoginAttemptforUser($referenceCode);
                    if($user!=null && $loginAttempt==3)
                    { 
                        $user->account_status ='inactive';
                        $user->save();
                        
                    }
        
                   $json_resp = array(
                    'code' => 'inactive_user' , 
                    'message' => 'Invalid Login details'
                    );
                    $status_code = 200; 
                }
            }
        }
        catch (Exception $ex) {

            $json_resp = array(
                'code' => 'Failed' , 
                'message' => 'Some error message'
                );
            $status_code = 404;        
        }

        return response()->json( $json_resp, $status_code); 
    }

    public function getParseUser($referenceCode,$installationId,$authKey)
    {

        $headers = array(
            "X-Parse-Application-Id: ".config('constants.parse_sdk.app_id'),
            "X-Parse-Master-Key: ".config('constants.parse_sdk.master_key')
        );

        $objectData = '{"authKey":"'.$authKey.'", "referenceCode":"'.$referenceCode.'", "installationId":"'.$installationId.'"}';

        $c = curl_init(); 
        curl_setopt($c, CURLOPT_URL, env('PARSE_SERVER_URL').'/functions/loginParseUser');
        curl_setopt($c, CURLOPT_POST,1);  
        curl_setopt($c, CURLOPT_POSTFIELDS,$objectData); 
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $o = curl_exec($c); 

        if (curl_errno($c)) {
            $sad = curl_error($c);
            print_r($sad);
            throw new Exception($sad);
        }   

        $info=curl_getinfo($c,CURLINFO_HTTP_CODE);
        curl_close($c);
     
        if($info==200){
            return $o;
        }else{
            return "error";
        }
    }

    public function setPassword(Request $request)
    {
        try{
            $data = $request->all();  
            $referenceCode = strtolower($data['referenceCode']);
            $password = trim($data['password']);
            $newpassword = getPassword($referenceCode , $password);
     
            $user = User::where('reference_Code',$referenceCode)->first(); 
            
            if($user==null)
            {
                $json_resp = array(
                    'code' => 'invalid_user' , 
                    'message' => 'In valid user'
                    );
                $status_code = 200;
            }
            else
            {    
                $user->password = Hash::make($newpassword);
                $user->account_status = 'active';
                $user->save();

                    $json_resp = array(
                    'code' => 'do_login' , 
                    'message' => 'Setup Successfully done'
                    );
                    $status_code = 200;
           
            }
        }
        catch (Exception $ex) {

            $json_resp = array(
                'code' => 'Failed' , 
                'message' => 'Some error message'
                );
            $status_code = 404;        
        }

        return response()->json( $json_resp, $status_code); 
    }

    public function userApiKey(Request $request)
    {
        try{
            $data = $request->all();  
            $referenceCode = $data['referenceCode'];
            $user = User::where('reference_Code',$referenceCode)->first(); 
            
            if($user==null)
            {
                $json_resp = array(
                    'code' => 'invalid_user' , 
                    'message' => 'In valid user'
                    );
                $status_code = 200;
            }
            else
            {
                $apiKey = $user->apiKey()->first();
                $json_resp = array(
                    'user-auth-key'=> $apiKey['key'],
                    'code' => 'successful_login' , 
                    'message' => 'Successfully logged in'
                );
                $status_code = 200;
                 
            }
        }
        catch (Exception $ex) {

            $json_resp = array(
                'code' => 'Failed' , 
                'message' => 'Some error message'
                );
            $status_code = 404;        
        }

        return response()->json( $json_resp, $status_code); 
    }

    public function contactUs(Request $request)
    {
        try{

            $requestData = $request->all();  
            $referenceCode = $requestData['referenceCode'];
            $patientName = $requestData['patientName'];
            $patientEmail = $requestData['patientEmail'];
            $patientPhone = $requestData['patientPhone'];
            $hospitalId = $requestData['hospitalId'];
            $projectId = $requestData['projectId'];
            $message = $requestData['message'];

            $user = User::where('reference_Code',$referenceCode)->first(); 
            $hospital = Hospital::find($hospitalId)->toArray();
            $project = Projects::find($projectId)->toArray();

            

            $data =[];
            $data['hospital_name'] = $hospital['name'];
            $data['hospital_email'] = $hospital['email'];
            $data['project'] = $project['name'];
            $data['patient_name'] = $patientName;
            $data['patient_email'] = $patientEmail;
            $data['patient_phone'] = $patientPhone;
            $data['patient_reference_code'] = $referenceCode;
            $data['message'] = $message;

     
            Mail::send('patient.contactmail', ['data'=>$data], function($message)use($data)
            {  
                $message->to($data['hospital_email'], $data['hospital_name'])->subject('Inquiry');
            });
             
             
            $json_resp = array(
               
                'code' => 'mail_sent' , 
                'message' => 'Mail sent'
            );
            $status_code = 200;
                 
             
        }
        catch (Exception $ex) {

            $json_resp = array(
                'code' => 'Failed' , 
                'message' => 'Some error message'
                );
            $status_code = 404;        
        }

        return response()->json( $json_resp, $status_code); 
    }
}
