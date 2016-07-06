<?php

namespace App\Http\Controllers\Hospital;

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
use \Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($hospitalSlug)
    {
       
       $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
       $users = User::where('type','project_user')->where('hospital_id',$hospital['id'])->orderBy('created_at')->get()->toArray();
       $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        return view('hospital.users-list')->with('active_menu', 'users')
                                        ->with('hospital', $hospital)
                                        ->with('logoUrl', $logoUrl)
                                        ->with('users', $users); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($hospitalSlug)
    {  
        
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];
        $projects = Projects:: where('hospital_id',$hospital['id'])->get()->toArray(); 

        return view('hospital.user-add')->with('active_menu', 'users')
                                    ->with('projects', $projects)
                                    ->with('hospital', $hospital)
                                    ->with('logoUrl', $logoUrl);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$hospitalSlug)
    {	
		
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();
        $hospitalName = $hospital['name'];

        $password = randomPassword();

        $user = new User;
        $name =  ucfirst($request->input('name'));
        $email = $request->input('email');
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->phone = $request->input('phone');     
        $user->type = 'project_user'; 
        $user->account_status = 'active'; 
        $hasAllAccess = ($request->has('has_all_access'))?'yes':'no';
        $user->has_all_access = $hasAllAccess;
        $user->hospital_id = $hospital['id'];
        $user->save(); 
        $userId = $user->id;

        
        $loginUrls = url().'/admin/login <br>';
        
        $projects = $request->input('projects');
        if(!empty($projects))
        {
            foreach ($projects as $key => $project) {
                 if($project=='')
                    continue;

                $access = $request->input('access_'.$key);

                $userAccess = new UserAccess;
                $userAccess->object_type = 'project' ; 
                $userAccess->object_id = $project; 
                $userAccess->user_id = $userId; 
                $userAccess->access_type = $access; 
                $userAccess->save();
            }         
            
        }

        $data =[];
        $data['name'] = $name;
        $data['email'] = $email;
        $data['password'] = $password;
        $data['loginUrls'] = $loginUrls;
 
        Mail::send('admin.registermail', ['user'=>$data], function($message)use($data)
        {  
            $message->from(Auth::user()->email, Auth::user()->name);
            $message->to($data['email'], $data['name'])->subject('Welcome to Mylan!');
        });
        
        Session::flash('success_message','User created successfully. An email has been sent to the user email address with the login instruction');

        // return redirect(url($hospitalSlug . '/users/' . $userId . '/edit'));
        return redirect(url($hospitalSlug . '/users'));
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
    public function edit($hospitalSlug,$id)
    {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        $logoUrl = url() . "/mylan/hospitals/".$hospital['logo'];

        $projects = Projects:: where('hospital_id',$hospital['id'])->get()->toArray(); 
        $user = User::find($id)->toArray();
        $userAccess = UserAccess::where(['user_id'=>$id,'object_type'=>'project'])->get()->toArray();  
        
         
        return view('hospital.user-edit')->with('active_menu', 'users')
                                          ->with('hospital', $hospital)
                                          ->with('logoUrl', $logoUrl)
                                          ->with('projects', $projects)
                                          ->with('userAccess', $userAccess)
                                          ->with('user', $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $hospitalSlug, $userId)
    {
        $user = User::find($userId);
        $name =  ucfirst($request->input('name'));
        $user->name = $name;
 
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');     
        $hasAllAccess = ($request->has('has_all_access'))?'yes':'no';
        $user->has_all_access = $hasAllAccess;
        $user->save(); 

        $projects = $request->input('projects');
        if(!empty($projects))
        {
            foreach ($projects as $key => $project) {

                if($project=='')
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
                    $userAccess->object_type = 'project' ; 
                    $userAccess->object_id = $project; 
                    $userAccess->user_id = $userId; 
                    $userAccess->access_type = $access; 
                    $userAccess->save();
                }
                
            }
            
        }
        
        Session::flash('success_message','User details successfully updated.');
        return redirect(url($hospitalSlug . '/users/' . $userId . '/edit'));
    }

    public function authUserEmail(Request $request, $hospitalSlug,$userId) {
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

    public function changePassword($hospitalSlug) {
        $hospital = Hospital::where('url_slug',$hospitalSlug)->first()->toArray();  
        return view('hospital.changepassword')->with('active_menu', '')
                                            ->with('hospital', $hospital);
    }

    public function updateUserPassword(Request $request,$hospitalSlug) {
        
        $userId = Auth::user()->id;
        $password = $request->input('password');

        $user = User::find($userId);
        $user->password = Hash::make($password);
        $user->save(); 
                
        Session::flash('success_message','User password successfully updated');
         
        return redirect(url($hospitalSlug . '/changepassword'));
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
