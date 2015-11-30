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
            $referenceCode = $data['referenceCode'];
            $deviceIdentifier = $data['deviceIdentifier'];
            $user = User::where('reference_Code',$referenceCode)->first(); 
            
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
                $hospitalData = $this -> getHospitalData($hospitalId,$projectId);

                $userDeviceCount = UserDevice::where('user_id',$userId)->get()->count();
                if($userDeviceCount >=5)
                {
                    $json_resp = array(
                        'code' => 'limit_exceeded' , 
                        'message' => 'cannot do setup more then 5 times',
                        'hospitalData' => $hospitalData
                        );
                        $status_code = 200;
                }
                elseif($userDeviceCount)
                {
                    $userDevice = UserDevice::where(['user_id'=>$userId,'device_identifier'=>$deviceIdentifier])->get()->count(); 
                    if($userDevice && $user['password'] =='')
                    {
                        $json_resp = array(
                        'code' => 'set_password' , 
                        'message' => 'Set Password',
                        'hospitalData' => $hospitalData
                        );
                        $status_code = 200;
                    }
                    elseif($userDevice)
                    {
                        $json_resp = array(
                        'code' => 'do_login' , 
                        'message' => 'Device exist',
                        'hospitalData' => $hospitalData
                        );
                        $status_code = 200;
                    }
                    else
                    {
                        //add device
                        $this->addDevice($data,$userId,$hospitalData,'do_login');
                         $json_resp = array(
                        'code' => 'do_login' , 
                        'message' => 'Device exist',
                        'hospitalData' => $hospitalData
                        );
                        $status_code = 200;
                    }
                }
                else
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

    public function getHospitalData($hospitalId , $projectId)
    {

        $hospitalQry = new ParseQuery("Hospital");
        $hospitalQry->equalTo("objectId", $hospitalId);
        $hospital = $hospitalQry->first();  

        $projectQry = new ParseQuery("Project");
        $projectQry->equalTo("objectId", $projectId);
        $project = $projectQry->first();
        
        $hospitalData = [];
        $hospitalData['id'] = $hospital->objectId();
        $hospitalData['name'] = $hospital->get('name');
        $hospitalData['logo'] = $hospital->get('logo');
        $hospitalData['contact_number'] = $hospital->get('contact_number');
        $hospitalData['email'] = $hospital->get('email');
        $hospitalData['address'] = $hospital->get('address');
        $hospitalData['project'] = $project->get('name');
         
        return $hospitalData;
    }

    public function addDevice($deviceData,$userId,$hospitalData,$returnTo)
    {
        $userDevice =  new UserDevice();
        $userDevice->user_id = $userId;
        $userDevice->device_type = $deviceData['deviceType'];
        $userDevice->device_identifier = $deviceData['deviceIdentifier'];
        $userDevice->device_os = $deviceData['deviceOS'];
        $userDevice->access_type = $deviceData['accessType'];
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
            $referenceCode = $data['referenceCode'];
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
                
                if (Hash::check($newpassword, $user['password']))  
                {
                    $projectId = $user['project_id'];
                    $hospitalId = $user['hospital_id'];
                    $hospitalData = $this -> getHospitalData($hospitalId,$projectId);
                    $apiKey = $user->apiKey()->first();
                    $json_resp = array(
                        'patient_id'=> $user['id'], 
                        'project_id'=> $projectId,    
                        'user-auth-key'=> $apiKey['key'],
                        'hospital'=> $hospitalData,
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

    public function setPassword(Request $request)
    {
        try{
            $data = $request->all();  
            $referenceCode = $data['referenceCode'];
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
}
