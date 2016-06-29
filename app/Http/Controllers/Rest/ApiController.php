<?php

namespace App\Http\Controllers\Rest;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Hospital;
use App\Projects;
use \Auth;

class ApiController extends Controller
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

    //
    public function apiLogin(Request $request){
        $email = $request->input('email');
        $password = trim($request->input('password'));
		$data['hospitalid'] ="";
		$data['countHospitalId'] = "";
		$userType = 0;//1 admin 0 other user 
		$userTypeData = User::select('type')->where('email' => $email)->get();
		foreach($userTypeData as $Udatatye){
			if($Udatatye['type'] == "mylan_admin"){
				$userType = 1;
			}else{
				$userType = 0;
			}
		}
        if (Auth::attempt(['email' => $email, 'password' => $password])){
            $data['status'] = 200;
			$whereCondition  = [ 'users.email' => $email ];
			if($userType == 0){
				$hospitalData = Hospital::get();
			}else{	
				$hospitalData = $userData = User::select('users.email','hospitals.name','hospitals.id')->join('hospitals','hospitals.id','=','users.hospital_id')->where($whereCondition)->get();
			}	
            $data['hospital'] = "<option value='0'>Please select</option>";
			$counter = 0;
            foreach($hospitalData as $hospital){
                $data['hospital'] .= "<option value='".$hospital['id']."'>".$hospital['name']."</option>";
				$counter = $counter + 1;
				if($counter == 1){
					$data['hospitalid'] = $hospital['id'];
				}
            }
			$data['countHospitalId'] = $counter;
			$data['userEmail'] = $email;
            return $data;
        }else{
            $data['status'] = 404;
            $data['message'] = 'Please check your login credentials';
            return $data;
        }
    }
	
	public function hospitalData(Request $request){
		$data['status'] = 200;
		$email = $request->input('email');
		$userType = 0;//1 admin 0 other user 
		$userTypeData = User::select('type')->where('email' => $email)->get();
		foreach($userTypeData as $Udatatye){
			if($Udatatye['type'] == "mylan_admin"){
				$userType = 1;
			}else{
				$userType = 0;
			}
		}
		$whereCondition  = [ 'users.email' => $email ];
		if($userType == 0){
				$hospitalData = Hospital::get();
		}else{	
			$hospitalData = $userData = User::select('users.email','hospitals.name','hospitals.id')->join('hospitals','hospitals.id','=','users.hospital_id')->where($whereCondition)->get();
		}	
		$data['hospital'] = "<option value='0'>Please select</option>";
		$counter = 0;
		foreach($hospitalData as $hospital){
			$data['hospital'] .= "<option value='".$hospital['id']."'>".$hospital['name']."</option>";
			$counter = $counter + 1;
			if($counter == 1){
				$data['hospitalid'] = $hospital['id'];
			}
		}
		$data['countHospitalId'] = $counter;
		return $data;
    }
    
    public function projectList(Request $request){
        $hospitalId = intval($request->hospitalId);
        $projectData = Projects::where("hospital_id",$hospitalId)->get();
        $data['projects'] = "<option value='0'>Please select</option>";
		$data['projectItem'] = "";
        foreach($projectData as $project){
            $data['projects'] .= "<option value='".$project['id']."'>".$project['name']."</option>";
			$data['projectItem'] .= "<li class='menu-item' id='".$project['id']."'>".$project['name']."</li>";
        }
		
        return $data;
    }
    
    public function mappingList(Request $request){
        $hospitalId = intval($request->input('hospitalList'));
        $projectId = intval($request->input('projectList'));
        $whereCondition = [ 'users.hospital_id' => $hospitalId, 'users.project_id' => $projectId ];
        $userData = User::select('*','users.name as username','hospitals.name as hospitalName','projects.name as projectName')->join('hospitals','hospitals.id','=','users.hospital_id')->join('projects','projects.id','=','users.project_id')->where($whereCondition)->get();
        $data['content'] ='';
        $data['status'] = 200;
        $reference = array();
        if($userData){
            foreach($userData as $userList){
                $data['hospitalName'] = $userList['hospitalName'];
                $data['projectName']  = $userList['projectName'];
                $data['content']     .= "<tr>
                                            <td>".$userList["reference_code"]."</td>
                                            <td class='patientId-".$userList["reference_code"]." getData-".$userList["reference_code"]."'>".$userList["username"]."</td>
                                            <td><span class='text-right edit-case' id='".$userList["reference_code"]."'><i class='fa fa-pencil' id='".$userList["reference_code"]."' ></i></span></td>
                                        </tr>";
                $reference[] =  $userList["reference_code"];                    
            }
        }
		if( $data['content'] == ""){
            $projectNames = Projects::where("id",$projectId)->get();
            $hospitalNames = Hospital::where("id",$hospitalId)->get();
			foreach($hospitalNames as $hname){
				$data['hospitalName'] = $hname['name'];
			}
			foreach($projectNames as $pname){
				$data['projectName'] = $pname['name'];
			}
        }      
		
		$data['referCode'] = $reference;
        return $data;
    }
}
