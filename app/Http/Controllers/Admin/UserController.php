<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\UserAccess;
use App\Hospital;
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
       $users = User::where('type','mylan_admin')->orderBy('created_at')->get()->toArray();
       

        return view('admin.users-list')->with('active_menu', 'users')
                                          ->with('users', $users); 
    }

    public function dashbord()
    {
        return view('admin.dashbord')->with('active_menu', 'dashbord');
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
        $user->name = $name;
        $user->reference_code = $request->input('email');
        $user->email = $request->input('email');
        $user->password = Hash::make($password);
        $user->phone = $request->input('phone');     
        $user->type = 'mylan_admin'; 
        $user->account_status = 'active'; 
        $user->project_access = ($request->has('has_access'))?'yes':'no';
        $user->mylan_access = ($request->has('had_mylan_access'))?'yes':'no';
        $user->save(); 
        $userId = $user->id;

        if(!$request->has('had_mylan_access'))
        {
            $access = $request->input('mylan_access');
            $userAccess = new UserAccess;
            $userAccess->object_type = 'mylan' ; 
            $userAccess->object_id = 0; 
            $userAccess->user_id = $userId; 
            $userAccess->access_type = $access; 
            $userAccess->save();
        }

        $hospitals = $request->input('hospital');
        if(!empty($hospitals))
        {
            foreach ($hospitals as $key => $hospital) {
                 if($hospital=='')
                    continue;

                $access = $request->input('access_'.$key);

                $userAccess = new UserAccess;
                $userAccess->object_type = 'hospital' ; 
                $userAccess->object_id = $hospital; 
                $userAccess->user_id = $userId; 
                $userAccess->access_type = $access; 
                $userAccess->save();
            }
            
        }
        
        $data =[];
        $data['name'] = $name;
        $data['email'] = $user->email;
        $data['password'] = $password;
 
        Mail::send('admin.registermail', ['user'=>$data], function($message)use($data)
        {  
            $message->to($data['email'], $data['name'])->subject('Welcome to Mylan!');
        });
        
         
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
        if($user['mylan_access']=='no')
            $mylanUserAccess = UserAccess::where(['user_id'=>$id,'object_type'=>'mylan'])->first()->toArray();  
 
         
         
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
        $user->project_access = ($request->has('has_access'))?'yes':'no';
        $user->mylan_access = ($request->has('had_mylan_access'))?'yes':'no';
        $user->save(); 

        $mylanAccessId = $request->input('mylan_access_id');
        if(!$request->has('had_mylan_access'))
        {   
            $access = $request->input('mylan_access');
            if($mylanAccessId)
            {
                $userAccess = UserAccess::find($mylanAccessId);
                $userAccess->access_type = $access; 
                $userAccess->save();
            }
            else
            {
                
                $userAccess = new UserAccess;
                $userAccess->object_type = 'mylan' ; 
                $userAccess->object_id = 0; 
                $userAccess->user_id = $userId; 
                $userAccess->access_type = $access; 
                $userAccess->save();  
            }
            
        }
        

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
}
