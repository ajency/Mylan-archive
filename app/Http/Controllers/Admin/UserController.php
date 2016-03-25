<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserAccess;
use App\Hospital;
use App\Projects;
use \Mail;
use \Session;
 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $users = User::where('type','hospital_user')->orderBy('created_at')->get()->toArray();
       

        return view('admin.users-list')->with('active_menu', 'users')
                                          ->with('users', $users); 
    }

    public function dashboard()
    {  
        return view('admin.dashbord')->with('active_menu', 'dashbord');
    }

    public function loginLinks()
    {
        $accessData = [];
        $userId = \Auth::user()->id;
        $hasAllAccess = \Auth::user()->has_all_access;
        $user = User::find($userId)->toArray(); 

        if(\Auth::user()->type =='hospital_user')
        {

          if($hasAllAccess=='no')
          {
            $hospitalIds = UserAccess::where(['user_id'=>$userId,'object_type'=>'hospital'])->lists('object_id')->toArray(); 
            $hospitals = Hospital:: whereIn('id',$hospitalIds)->get()->toArray();
          }
          else
          {
            $hospitals = Hospital:: all()->toArray();
          }

          $accessData['type'] = 'Hospital';

          foreach ($hospitals as $hospital) {

            $url = url().'/'.$hospital['url_slug'];
            $accessData['links'][] =['NAME'=>$hospital['name'], 'loginName'=>$hospital['name'], 'URL'=>$url];
          }

        }
        elseif(\Auth::user()->type =='project_user')
        {
           
          $hospitalId = \Auth::user()->hospital_id;
          $hospital = Hospital:: where('id',$hospitalId)->first()->toArray();
               
            

          if($hasAllAccess=='no')
          {
            $projectIds = UserAccess::where(['user_id'=>$userId,'object_type'=>'project'])->lists('object_id')->toArray();  
            $projects = Projects:: whereIn('id',$projectIds)->get()->toArray();
          }
          else
          {  
            $projects = Projects:: where('hospital_id',$hospitalId)->get()->toArray();
          }
         
          $accessData['type'] = 'Project';

          $hospitalData=[];
        
          foreach ($projects as $project) {
 
            $name = $hospital['name'] .' ('.$project['name'].')';
            $url = url().'/'.$hospital['url_slug'].'/'.$project['project_slug'];
            $accessData['links'][] =['NAME'=>$name, 'loginName'=>$project['name'], 'URL'=>$url];
          }

          

        }

        return view('admin.loginlinks')->with('accessData', $accessData)
                                       ->with('active_menu', 'dashbord');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
 
        $roles = getRoles();
        $hospitals = Hospital:: all()->toArray(); 
        return view('admin.user-add')->with('active_menu', 'users')
                                    ->with('hospitals',$hospitals)
                                    ->with('roles',$roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $password = randomPassword();

        $user = new User;
        $name =  ucfirst($request->input('name'));
        $email = $request->input('email');
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->phone = $request->input('phone');     
        $user->type = 'hospital_user'; 
        $user->account_status = 'active';
        $hasAllAccess = ($request->has('has_all_access'))?'yes':'no';
        $user->has_all_access = $hasAllAccess;
        $user->save(); 
        $userId = $user->id;

        $hospitalIds = $request->input('hospital');
 
        if(!empty($hospitalIds))
        {
            foreach ($hospitalIds as $key => $hospitalId) {
                
                if($hospitalId=='')
                    continue;

                $access = $request->input('access_'.$key);

                $userAccess = new UserAccess;
                $userAccess->object_type = 'hospital' ; 
                $userAccess->object_id = $hospitalId; 
                $userAccess->user_id = $userId; 
                $userAccess->access_type = $access; 
                $userAccess->save();
            }
            
        }
        
        $loginUrls = url().'/admin/login <br>';

        $data =[];
        $data['name'] = $name;
        $data['email'] = $email;
        $data['password'] = $password;
        $data['loginUrls'] = $loginUrls;
 
        Mail::send('admin.registermail', ['user'=>$data], function($message)use($data)
        {  
            $message->to($data['email'], $data['name'])->subject('Welcome to Mylan!');
        });
        
        Session::flash('success_message','User created successfully. An email has been sent to the user email address with the login instruction');
         

        return redirect(url('/admin/users/' . $userId . '/edit'));
        
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
        $hospitals = Hospital:: all()->toArray();
        $user = User::find($id)->toArray();
        $userAccess = UserAccess::where(['user_id'=>$id,'object_type'=>'hospital'])->get()->toArray();  
        
        $mylanUserAccess['access_type'] = 'view';
        $mylanUserAccess['id'] = '';
 

        return view('admin.user-edit')->with('active_menu', 'users')
                                          ->with('hospitals', $hospitals)
                                          ->with('userAccess', $userAccess)
                                          ->with('mylanUserAccess', $mylanUserAccess)
                                          ->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
        $user = User::find($userId);
        $name =  ucfirst($request->input('name'));
        $user->name = $name;
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');     
        $user->has_all_access = ($request->has('has_all_access'))?'yes':'no';
        $user->save(); 

        $hospitals = $request->input('hospital');
        if(!empty($hospitals))
        {
            foreach ($hospitals as $key => $hospital) {

                if($hospital=='')
                    continue;

                $access = $request->input('access_'.$key);
                $user_access = $request->input('user_access')[$key];

                if($user_access!='')
                {
                    $userAccess = UserAccess::find($user_access);
                    $userAccess->access_type = $access; 
                    $userAccess->save();
                }
                else
                {
                    $userAccess = new UserAccess;
                    $userAccess->object_type = 'hospital' ; 
                    $userAccess->object_id = $hospital; 
                    $userAccess->user_id = $userId; 
                    $userAccess->access_type = $access; 
                    $userAccess->save();
                }
                
            }
            
        }
        
        Session::flash('success_message','User details successfully updated.');
        
        return redirect(url('/admin/users/' . $userId . '/edit'));
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

    public function authUserEmail(Request $request,$userId) {
        $email = $request->input('email');
        
        $msg = '';
        $flag = true;


        if ($userId)
            $patientData = User::where('email', $email)->where('type','!=', 'patient')->where('id', '!=', $userId)->get()->toArray();
        else
            $patientData = User::where('email', $email)->where('type','!=', 'patient')->get()->toArray();


        
        $status = 201;
        if (!empty($patientData)) {
            $msg = 'Emai Already Taken';
            $flag = false;
            $status = 200;
        }


        return response()->json([
                    'code' => 'reference_validation',
                    'message' => $msg,
                    'data' => $flag,
                        ], $status);
    }
}
