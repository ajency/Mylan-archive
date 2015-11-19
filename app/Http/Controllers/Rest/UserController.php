<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;

use Crypt;
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
            $status_code = 404;
        }
        else
        {
            $userId = $user['id'];
            $hospitalId = $user['hospital_id'];
            $projectId = $user['project_id'];
            $hospitalData = $this -> getHospitalData($hospitalId,$projectId);

            $userDeviceCount = UserDevice::where('user_id',$userId)->get()->count(); 
            if($userDeviceCount)
            {
                $userDevice = UserDevice::where(['user_id'=>$userId,'device_identifier'=>$deviceIdentifier])->get()->count(); 
                if($userDevice)
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
                    $json_resp = array(
                    'code' => 'new_setup' , 
                    'message' => 'Device does not exist',
                    'hospitalData' => $hospitalData
                    );
                    $status_code = 404;
                }
            }
            else
            {
                $json_resp = array(
                'code' => 'new_setup' , 
                'message' => 'Device does not exist',
                'hospitalData' => $hospitalData
                );
                $status_code = 404;
            }

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
        $hospitalData['name'] = $hospital->get('name');
        $hospitalData['logo'] = $hospital->get('logo');
        $hospitalData['contact_number'] = $hospital->get('contact_number');
        $hospitalData['email'] = $hospital->get('email');
        $hospitalData['address'] = $hospital->get('address');
        $hospitalData['project'] = $project->get('name');
         
        return $hospitalData;
    }

    public function doLogin(Request $request)
    {
        $data = $request->all();  
        $referenceCode = $data['referenceCode'];
        $password = Crypt::encrypt($data['password']);
        dd($password);

        $user = User::where('reference_Code',$referenceCode)->first(); 
        
        if($user==null)
        {
            $json_resp = array(
                'code' => 'invalid_user' , 
                'message' => 'In valid user',
                'hospitalData' => array()
                );
            $status_code = 404;
        }
        else
        {
            
        }
    }
}
